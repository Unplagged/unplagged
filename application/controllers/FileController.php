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

require_once 'BaseController.php';

/**
 * 
 */
class FileController extends BaseController{

  public function indexAction(){
    $this->_helper->redirector('list', 'file');
  }

  public function uploadAction(){
    $uploadform = new Application_Form_File_Upload();
    if($this->_request->isPost()){
      if($uploadform->isValid(($this->_request->getPost()))){
        $adapter = new Zend_File_Transfer_Adapter_Http();
        $adapter->setOptions(array('useByteString'=>false));

        // Maximal 20MB = 20480000
        // $adapter->setMaxFileSize(20480000);
        //$adapter->addValidator('NotEmpty');
        // Nur JPEG, PNG, und GIFs
        //$adapter->addValidator('Extension', true, array('png,gif,tif,jpg, tiff', 'messages'=>'<b>jpg</b>, <b>png</b>, or <b>gif</b> only allowed.'));
        //muss mit der gruppe geklärt werden
        //Neither APC nor uploadprogress extension installed 
        /* $adapterprogressbar = new Zend_ProgressBar_Adapter_Console();
          $adapterupload = Zend_File_Transfer_Adapter_Http::getProgress($adapterprogressbar);

          $adapterupload = null;
          while (!$adapterupload['done'])
          {
          $adapterupload = Zend_File_Transfer_Adapter_Http::getProgress($adapterupload);
          } */

        $newName = $this->_request->getPost('newName');

        // collect file information
        $fileName = pathinfo($adapter->getFileName(), PATHINFO_BASENAME);
        $fileExt = pathinfo($adapter->getFileName(), PATHINFO_EXTENSION);

        // store file in database to get an id
        $data = array();
        $data["size"] = $adapter->getFileSize('filepath');
        //if the mime type is always application/octet-stream, then the 
        //mime magic and fileinfo extensions are probably not installed
        $data["mimetype"] = $adapter->getMimeType('filepath');
        $data["filename"] = !empty($newName) ? $newName . "." . $fileExt : $fileName;
        $data["extension"] = $fileExt;
        $data["location"] = "application" . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "files";

        $file = new Application_Model_File($data);
        $this->_em->persist($file);
        $this->_em->flush();

        // prepare file for uploading
        $adapter->setDestination($file->getAbsoluteLocation());
        $adapter->addFilter('Rename', array('target'=>$file->getAbsoluteLocation() . DIRECTORY_SEPARATOR . $file->getId() . "." . $file->getExtension()));

        if($adapter->receive()){
          chmod($file->getAbsoluteLocation() . DIRECTORY_SEPARATOR . $file->getId() . "." . $file->getExtension(), 0755);
          
          // notification
          $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
          Unplagged_Helper::notify("file_uploaded", $file, $user);
      
          $this->_helper->flashMessenger->addMessage('File was uploaded successfully.');
          $this->_helper->redirector('list', 'file');
        }else{
          $this->_em->remove($file);
          $this->_em->flush();

          $this->_helper->flashMessenger->addMessage('File could not be uploaded.');

          //$messages = $adapter->getMessages();
        }
      }
    }else{
      // wenn nicht hochgeladen
      $uploadform->populate($this->_request->getPost());
    }
    $this->view->form = $uploadform;
  }

  public function listAction(){
    $this->setTitle('Public Files');
    
    // @todo: clean input
    $page = $this->_getParam('page');

    $query = $this->_em->createQuery("SELECT f FROM Application_Model_File f");
    $count = $this->_em->createQuery("SELECT COUNT(f.id) FROM Application_Model_File f");

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($page);

    $this->view->paginator = $paginator;
  }

  public function downloadAction(){
    $fileId = $this->_getParam('id');

    if(!empty($fileId)){
      $fileId = preg_replace('/[^0-9]/', '', $fileId);
      $file = $this->_em->getRepository('Application_Model_File')->findOneById($fileId);
      if($file){
        $downloadPath = $file->getAbsoluteLocation() . DIRECTORY_SEPARATOR . $file->getId() . "." . $file->getExtension();

        // set headers
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=\"" . $file->getFilename() . "\"");
        header("Content-type: " . $file->getMimeType());
        header("Content-Transfer-Encoding: binary");

        readfile($downloadPath);
      }else{
        $this->_helper->flashMessenger->addMessage('No file found.');
        $this->_helper->redirector('list', 'file');
      }
    }
    // disable view
    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

  public function setTargetAction(){
    $fileId = $this->_getParam('id');

    $this->targetAction($fileId, true);
  }

  public function unsetTargetAction(){
    $fileId = $this->_getParam('id');

    $this->targetAction($fileId, false);
  }

  private function targetAction($fileId, $isTarget){
    $fileId = $this->_getParam('id');

    if(!empty($fileId)){
      $fileId = preg_replace('/[^0-9]/', '', $fileId);
      $file = $this->_em->getRepository('Application_Model_File')->findOneById($fileId);
      if($file){
        $file->setIsTarget($isTarget);

        $this->_em->persist($file);
        $this->_em->flush();
      }else{
        $this->_helper->flashMessenger->addMessage('No file found.');
      }
    }

    $this->_helper->redirector('list', 'file');

    // disable view
    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

  public function parseAction(){
    $fileId = $this->_getParam('id');

    if(empty($fileId)){
      // show error message
      $this->_helper->flashMessenger->addMessage('The fileId has to be set.');
    }else{
      $fileId = preg_replace('/[^0-9]/', '', $fileId);

      $file = $this->_em->getRepository('Application_Model_File')->findOneById($fileId);
      $language = "eng";

      if(empty($file)){
        // show error message
        $this->_helper->flashMessenger->addMessage('No file found by that id.');
      }else{
        $parser = Unplagged_Parser::factory($file->getMimeType());
        
        $document = $parser->parseToDocument($file, $language);
        if(empty($document)){
          $this->_helper->flashMessenger->addMessage('The file could not be parsed.');
        }else{
          $this->_em->persist($document);
          $this->_em->flush();
          $this->_helper->flashMessenger->addMessage('The file was successfully parsed.');
        }
      }
    }
    $this->_helper->redirector('list', 'file');
  }

  public function deleteAction(){
    $fileId = $this->_getParam('id');

    if(!empty($fileId)){
      $fileId = preg_replace('/[^0-9]/', '', $fileId);
      $file = $this->_em->getRepository('Application_Model_File')->findOneById($fileId);
      if($file){

        // remove file from file system
        $downloadPath = $file->getAbsoluteLocation() . DIRECTORY_SEPARATOR . $file->getId() . "." . $file->getExtension();
        $deleted = unlink($downloadPath);
        if($deleted || !file_exists($downloadPath)){
          // remove database record
          $this->_em->remove($file);
          $this->_em->flush();
          $this->_helper->flashMessenger->addMessage('The file was deleted successfully.');
        }else{
          $this->_helper->flashMessenger->addMessage('The file could not be deleted.');
        }
      }else{
        $this->_helper->flashMessenger->addMessage('The file does not exist.');
      }
    }

    $this->_helper->redirector('list', 'file');

    // disable view
    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

}
?>
