<?php

/**
 * Unplagged - The plagiarism detection cockpit.
 * Copyright (C) 2012 Unplagged
 *  
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *  
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 
 */
class FileController extends Unplagged_Controller_Action{

  public function indexAction(){
    $this->_helper->redirector('list', 'file');
  }

  public function uploadAction(){
    if($this->_request->isPost()){
      $this->_helper->viewRenderer->setNoRender(true);
      $this->_helper->layout()->disableLayout();
      $post = $this->_request->getPost();

      $uploadForm = new Application_Form_File_Upload();
      if($uploadForm->isValid($post)){
        $this->storeUpload();
      }else{
        $this->_helper->FlashMessenger(array('error'=>'The file "' . $file->getFilename() . '" could not be uploaded.'));
        echo '{"jsonrpc" : "2.0", "error" : {"code": 500, "message": "File upload failed."}, "id" : "id"}';
      }
    }
    
    $this->setTitle('Upload files');
  }

  /**
   * Moves the current file to the storage directory and stores an object for the file in the database.
   */
  private function storeUpload(){
    $filename = $this->_request->getPost('newName');
    $description = $this->_request->getPost('description');

    $storageDir = BASE_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
    $uploader = new Unplagged_Uploader($filename, $description, $storageDir, null);
    $file = $uploader->upload();

    if($file){
      $user = Zend_Registry::getInstance()->user;
      $user->addFile($file);
      $this->_em->persist($file);

      if($this->_request->getPost('makePublic') === 'true'){
        $this->storePublic($file);
      }

      if($this->_request->getPost('addToCase') === 'true'){
        $this->addToCase($file, $user);
      }

      $this->_em->persist($user);
      $this->_em->flush();

      //store in the activity stream, that the current user uploaded this file
      Unplagged_Helper::notify('file_uploaded', $file, $user);
      $this->_helper->FlashMessenger(array('success'=>array('The file "%s" was successfully uploaded.', array($file->getFilename()))));

      echo '{"jsonrpc" : "2.0", "result" : null, "id" : "id"}';
    }else{
      $this->_helper->FlashMessenger(array('error'=>'The file "' . $file->getFilename() . '" could not be uploaded.'));
      echo '{"jsonrpc" : "2.0", "error" : {"code": 500, "message": "File upload failed."}, "id" : "id"}';
    }
  }

  /**
   * Adds the given file to the current case.
   * 
   * @param Application_Model_File $file
   * @param Application_Model_User $user 
   */
  private function addToCase(Application_Model_File $file, Application_Model_User $user){
    $currentCase = $user->getCurrentCase();

    if($currentCase){
      $currentCase->addFile($file);
      $this->_em->persist($currentCase);
    }
  }

  /**
   * Adds the given file to the guest user, so that it can be displayed in the public files area.
   * 
   * @param Application_Model_File $file 
   */
  private function storePublic(Application_Model_File $file){
    $registry = Zend_Registry::getInstance();
    $guestId = $registry->entitymanager->getRepository('Application_Model_Setting')->findOneBySettingKey('guest-id');
    $guest = $registry->entitymanager->getRepository('Application_Model_User')->findOneById($guestId->getValue());

    $guest->addFile($file);

    $this->_em->persist($guest);
    $this->_em->flush();
  }

  /**
   * Shows the public files.
   * 
   * Public files are essentially all the files that registered for the guest user. 
   */
  public function listAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());

    $this->setTitle('Public Files');

    $registry = Zend_Registry::getInstance();
    $guestId = $registry->entitymanager->getRepository('Application_Model_Setting')->findOneBySettingKey('guest-id');
    $guest = $registry->entitymanager->getRepository('Application_Model_User')->findOneById($guestId->getValue());
    $guestFiles = $guest->getFiles();

    $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($guestFiles->toArray()));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

    // generate the action dropdown for each file
    // @todo: use centralised method for all three file lists
    foreach($paginator as $file){
      $file->actions = array();

      $action['link'] = '#parseFile';
      $action['label'] = 'Create document';
      $action['icon'] = 'images/icons/page_gear.png';
      $action['data-toggle'] = 'modal';
      $action['data-id'] = $file->getId();
      $file->actions[] = $action;

      $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'file', 'action'=>'read', 'base'=>$file));
      if(Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
        $action['link'] = '/file/download/id/' . $file->getId();
        $action['label'] = 'Download';
        $action['icon'] = 'images/icons/disk.png';
        $file->actions[] = $action;
      }
      $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'file', 'action'=>'delete', 'base'=>$file));
      if(Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
        $action['link'] = '/file/delete/id/' . $file->getId();
        $action['label'] = 'Delete';
        $action['icon'] = 'images/icons/delete.png';
        $file->actions[] = $action;
      }

      $action['link'] = '/case/add-file/id/' . $file->getId();
      $action['label'] = 'Add to current case';
      $action['icon'] = 'images/icons/package_add.png';
      $file->actions[] = $action;

      $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'file', 'action'=>'authorize', 'base'=>$file));
      if(Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
        $action['link'] = '/permission/edit/id/' . $file->getId();
        $action['label'] = 'Set permissions';
        $action['icon'] = 'images/icons/shield.png';
        $file->actions[] = $action;
      }
    }

    $this->view->paginator = $paginator;
    $this->view->uploadLink = '/file/upload?area=public';
  }

  /**
   * Send the requested file to the user.
   */
  public function downloadAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $file = $this->_em->getRepository('Application_Model_File')->findOneById($input->id);
      if($file){
        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'file', 'action'=>'read', 'base'=>$file));
        if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
          $this->redirectToLastPage(true);
        }

        // disable view
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $downloadPath = $file->getFullPath();
        // set headers
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=\"" . $file->getFilename() . "\"");
        header("Content-type: " . $file->getMimeType());
        header("Content-Transfer-Encoding: binary");

        readfile($downloadPath);
      }else{
        $this->_helper->FlashMessenger('No file found.');
        $this->_helper->redirector('list', 'file');
      }
    }else{
      $this->_helper->FlashMessenger('The file couldn\'t be found.');
      $this->_helper->redirector('list', 'file');
    }
  }

  /**
   * Parses a single file into a document using OCR. 
   */
  public function parseAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits', 'language'=>'Alpha'), null, $this->_getAllParams());
    $language = !empty($input->language) ? $input->language : 'en';
    $case = Zend_Registry::getInstance()->user->getCurrentCase();
    if($case){
      if(empty($input->id)){
        $this->_helper->FlashMessenger(array('info'=>'A file id must be set to tell us what to OCR.'));
      }else{
        $file = $this->_em->getRepository('Application_Model_File')->findOneById($input->id);

        if(empty($file)){
          $this->_helper->FlashMessenger(array('error'=>"Sorry, we couldn't find a file with the specified id."));
        }else{
          $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'file', 'action'=>'read', 'base'=>$file));
          if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
            $this->redirectToLastPage(true);
          }
          // pdfs will be generated through cron
          if($file->getExtension() === 'pdf'){
            $data['title'] = $file->getFilename();
            $data['initialFile'] = $file;
            $data['state'] = $this->_em->getRepository('Application_Model_State')->findOneByName('scheduled');
            $data['language'] = $language;
            $document = new Application_Model_Document($data);

            // start task
            $data = array();
            $data['initiator'] = Zend_Registry::getInstance()->user;
            $data['ressource'] = $document;
            $data['action'] = $this->_em->getRepository('Application_Model_Action')->findOneByName('file_parse');
            $data['state'] = $this->_em->getRepository('Application_Model_State')->findOneByName('scheduled');
            $task = new Application_Model_Task($data);

            $this->_em->persist($task);
            $this->_em->flush();

            $this->_helper->FlashMessenger(array('success'=>array('The OCR of "%s" was scheduled, you will be notified as soon as the process finished.', array($file->getFilename()))));
          }else{
            // images will be parsed directly
            $parser = Unplagged_Parser::factory($file->getMimeType());

            $document = $parser->parseToDocument($file, $language);
            if(empty($document)){
              $this->_helper->FlashMessenger(array('error'=>'We are sorry, but an error occured during the OCR, please try again later.'));
            }else{
              $document->setState($this->_em->getRepository('Application_Model_State')->findOneByName('parsed'));

              $this->_em->persist($document);
              $this->_em->flush();

              // add notification to activity stream
              Unplagged_Helper::notify("document_created", $document, Zend_Registry::getInstance()->user);
              $this->_helper->FlashMessenger(array('success'=>'The OCR of the file was successful.'));
            }
          }

          $case->addDocument($document);
          $this->_em->persist($case);
          $this->_em->flush();
        }
      }
    }else{
      $this->_helper->FlashMessenger(array('error'=>'You need to select a case for which this document should be parsed.'));
    }
    $this->_helper->redirector('list', 'document');
  }

  /**
   * Deletes the file specified by the id parameter. 
   */
  public function deleteAction(){
    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $file = $this->_em->getRepository('Application_Model_File')->findOneById($input->id);
      if($file){
        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'file', 'action'=>'delete', 'base'=>$file));
        if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
          $this->redirectToLastPage(true);
        }
        // remove file from file system
        $localPath = $file->getFullPath();
        $deleted = unlink($localPath);
        if($deleted || !file_exists($localPath)){
          // set removed state in database record
          $file->remove();
          $registry = Zend_Registry::getInstance();

          //@todo could be inefficient, but I got no better solution right now
          //remove file from all users
          $users = $registry->entitymanager->getRepository('Application_Model_User')->findAll();
          foreach($users as $user){
            $user->removeFile($file);
            $this->_em->persist($user);
          }

          //remove file from all cases
          $cases = $registry->entitymanager->getRepository('Application_Model_Case')->findAll();
          foreach($cases as $case){
            $case->removeFile($file);
            $this->_em->persist($case);
          }

          $user = $registry->user;
          $user->removeFile($file);
          $this->_em->persist($user);

          //remove from public files
          $guestId = $registry->entitymanager->getRepository('Application_Model_Setting')->findOneBySettingKey('guest-id');
          $guest = $registry->entitymanager->getRepository('Application_Model_User')->findOneById($guestId->getValue());
          $guest->removeFile($file);
          $this->_em->persist($guest);

          $this->_em->persist($file);
          $this->_em->flush();
          $this->_helper->FlashMessenger(array('success'=>'The file was deleted successfully.'));
        }else{
          $this->_helper->FlashMessenger(array('error'=>'We are sorry, but the file could not be deleted.'));
        }
      }else{
        $this->_helper->FlashMessenger(array('error'=>'The file you specified does not exist.'));
      }
    }

    $this->redirectToLastPage();
  }

}