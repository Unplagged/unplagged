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
      $post = $this->_request->getPost();
      
      $uploadForm = new Application_Form_File_Upload();
      if($uploadForm->isValid($post)){
        $this->storeUpload();
      } else {
        var_dump($uploadForm->getErrors());
        die('{"jsonrpc" : "2.0", "error" : {"code": 500, "message": "File upload failed."}, "id" : "id"}');
      }
    }
  }

  /**
   * Moves the current file to the storage directory and stores an object for the file in the database.
   */
  private function storeUpload(){
    $adapter = new Zend_File_Transfer();

    $newName = $this->_request->getPost('newName');
    $description = $this->_request->getPost('description');

    $pathinfo = pathinfo($adapter->getFileName());

    $storageDir = BASE_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
    $fileNames = $this->findFilename($pathinfo, $newName);
    $adapter->addFilter('Rename', $storageDir . $fileNames[1]);

    //move the uploaded file to the before specified location
    if($adapter->receive()){
      chmod($storageDir . $fileNames[1], 0755);

      $user = Zend_Registry::getInstance()->user;
      
      $file = $this->createFileObject($adapter, $fileNames, $pathinfo, $description, $storageDir, $user);
      $this->_em->persist($file);
      $this->_em->flush();
      
      $user->addFile($file);

      $this->_em->persist($user);
      
      //store in the activity stream, that the current user uploaded this file
      Unplagged_Helper::notify('file_uploaded', $file, $user);
      $this->_helper->FlashMessenger(array('success'=>array('The file "%s" was successfully uploaded.', array($fileNames[0]))));

      die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }else{
      $this->_helper->FlashMessenger(array('error'=>'The file "' . $fileNames[0] . '" could not be uploaded.'));
    }
  }

  /**
   * Creates a unique filename from the specified data.
   * 
   * @param array $pathinfo An array as returned by the pathinfo() function for the uploaded file.
   * @param string $newName A different name for the file from user input.
   * @return array An array containing the original filename and a new unique filename to store the file locally. 
   */
  private function findFilename($pathinfo, $newName){
    $fileExtension = $pathinfo['extension'];

    $fileName = '';
    if($newName){
      $fileName = $newName;
    }else{
      $fileName = $pathinfo['filename'];
    }
    $localFilename = $this->sanitizeFilename($fileName) . '_' . uniqid() . '.' . $fileExtension;
    $fileName .= '.' . $fileExtension;

    return array($fileName, $localFilename);
  }

  /**
   * Based on Wordpress.
   * 
   * Sanitizes a filename replacing whitespace with dashes
   *
   * Removes special characters that are illegal in filenames on certain
   * operating systems and special characters requiring special escaping
   * to manipulate at the command line. Replaces spaces and consecutive
   * dashes with a single dash. Trim period, dash and underscore from beginning
   * and end of filename.
   *
   * @param string $filename The filename to be sanitized
   * @return string The sanitized filename
   */
  private function sanitizeFilename($filename){
    $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
    $filename = str_replace($special_chars, '', $filename);
    $filename = preg_replace('/[\s-]+/', '-', $filename);
    $filename = trim($filename, '.-_');
    return $filename;
  }

  /**
   * Takes the data to create an Application_Model_File object.
   * 
   * @param Zend_File_Transfer $adapter
   * @param array $fileNames
   * @param array $pathinfo
   * @param string $description
   * @param string $storageDir
   * @return \Application_Model_File 
   */
  private function createFileObject($adapter, $fileNames, $pathinfo, $description, $storageDir, $user){
    $data = array();
    $data['size'] = $adapter->getFileSize();
    //if the mime type is always application/octet-stream, then the 
    //mime magic and fileinfo extensions are probably not installed
    $data['mimetype'] = $adapter->getMimeType();
    $data['filename'] = $fileNames[0];
    $data['extension'] = $pathinfo['extension'];
    $data['location'] = $storageDir;
    $data['description'] = $description;
    $data['localFilename'] = $fileNames[1];
    $data['uploader'] = $user;

    $file = new Application_Model_File($data);

    return $file;
  }

  public function listAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());

    $this->setTitle('Public Files');

    $permissionAction = 'read';
    $query = 'SELECT b FROM Application_Model_File b';
    $count = 'SELECT COUNT(b.id) FROM Application_Model_File b';
    $orderBy = 'b.created DESC';

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count, null, $orderBy, $permissionAction));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

    // generate the action dropdown for each file
    // @todo: use centralised method for all three file lists
    foreach($paginator as $file){
      $file->actions = array();

      $parseAction['link'] = '/file/parse/id/' . $file->getId();
      $parseAction['title'] = Zend_Registry::getInstance()->Zend_Translate->translate('The character recognition of big files can take very long, you will be notified when this action is finished.');
      $parseAction['name'] = 'parse';
      $parseAction['label'] = 'OCR';
      $parseAction['icon'] = 'images/icons/page_gear.png';
      $file->actions[] = $parseAction;

      if(Zend_Registry::getInstance()->user->hasPermission(new Application_Model_Permission('file', 'read', $file))){
        $action['link'] = '/file/download/id/' . $file->getId();
        $action['label'] = 'Download';
        $action['icon'] = 'images/icons/disk.png';
        $file->actions[] = $action;
      }
      if(Zend_Registry::getInstance()->user->hasPermission(new Application_Model_Permission('file', 'delete', $file))){
        $action['link'] = '/file/delete/id/' . $file->getId();
        $action['label'] = 'Delete';
        $action['icon'] = 'images/icons/delete.png';
        $file->actions[] = $action;
      }
      $action['link'] = '/user/add-file/id/' . $file->getId();
      $action['label'] = 'Add to personal files';
      $action['icon'] = 'images/icons/basket_put.png';
      $file->actions[] = $action;


      $action['link'] = '/case/add-file/id/' . $file->getId();
      $action['label'] = 'Add to current case';
      $action['icon'] = 'images/icons/package_add.png';
      $file->actions[] = $action;
      
      if(Zend_Registry::getInstance()->user->hasPermission(new Application_Model_Permission('file', 'authorize', $file))){
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
        if(!Zend_Registry::getInstance()->user->hasPermission(new Application_Model_Permission('file', 'read', $file))){
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

  private function scheduleOcr(Application_Model_File $file){
    
  }

  /**
   * Parses a single file into a document using OCR. 
   */
  public function parseAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(empty($input->id)){
      $this->_helper->FlashMessenger(array('info'=>'A file id must be set to tell us what to OCR.'));
    }else{
      $file = $this->_em->getRepository('Application_Model_File')->findOneById($input->id);
      $language = 'eng';

      if(empty($file)){
        $this->_helper->FlashMessenger(array('error'=>"Sorry, we couldn't find a file with the specified id."));
      }else{
        // pdfs will be generated through cron
        if($file->getExtension() === 'pdf'){
          $data['title'] = $file->getFilename();
          $data['initialFile'] = $file;
          $data['state'] = $this->_em->getRepository('Application_Model_State')->findOneByName('task_scheduled');
          $document = new Application_Model_Document($data);

          // start task
          $data = array();
          $data['initiator'] = Zend_Registry::getInstance()->user;
          $data['ressource'] = $document;
          $data['action'] = $this->_em->getRepository('Application_Model_Action')->findOneByName('file_parse');
          $data['state'] = $this->_em->getRepository('Application_Model_State')->findOneByName('task_scheduled');
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
            $this->_helper->FlashMessenger(array('success'=>'The OCR of the file was successful.'));
          }
        }

        $case = Zend_Registry::getInstance()->user->getCurrentCase();
        $case->addDocument($document);
        $this->_em->persist($case);
        $this->_em->flush();
      }
    }
    $this->_helper->redirector('list', 'document');
  }

  /**
   * Deletes the file specified by the id parameter. 
   */
  public function deleteAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $file = $this->_em->getRepository('Application_Model_File')->findOneById($input->id);
      if($file){
        if(!Zend_Registry::getInstance()->user->hasPermission(new Application_Model_Permission('file', 'delete', $file))){
          $this->redirectToLastPage(true);
        }
        // remove file from file system
        $localPath = $file->getFullPath();
        $deleted = unlink($localPath);
        if($deleted || !file_exists($localPath)){
          // remove database record
          $this->_em->remove($file);
          $this->_em->flush();
          $this->_helper->FlashMessenger(array('success'=>'The file was deleted successfully.'));
        }else{
          $this->_helper->FlashMessenger(array('error'=>'We are sorry, but the file could not be deleted.'));
        }
      }else{
        $this->_helper->FlashMessenger(array('error'=>'The file you specified does not exist.'));
      }
    }

    $this->_helper->redirector('list', 'file');

    // disable view
    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

}

?>
