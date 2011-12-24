<?php

class FileController extends Zend_Controller_Action{

  public function init(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
    $this->_defaultNamespace = new Zend_Session_Namespace('Default');
    $this->view->flashMessages = $this->_helper->flashMessenger->getMessages();
  }

  public function indexAction(){
    $this->_helper->redirector('list', 'file');
  }

  public function uploadAction(){
    $uploadform = new Application_Form_File_Upload();
    if($this->_request->isPost()){
      if($uploadform->isValid(($this->_request->getPost()))){
        $adapter = new Zend_File_Transfer_Adapter_Http();
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
        $fileDirectory = "storage" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR;
        $fileName = pathinfo($adapter->getFileName(), PATHINFO_BASENAME);
        $fileExt = pathinfo($adapter->getFileName(), PATHINFO_EXTENSION);

        // store file in database to get an id
        $data = array();
        $data["size"] = $adapter->getFileSize('filepath');
        $data["mimetype"] = $adapter->getMimeType('filepath');
        $data["filename"] = !empty($newName) ? $newName . "." . $fileExt : $fileName;
        $data["extension"] = $fileExt;
        $data["location"] = $fileDirectory;

        $file = new Application_Model_File($data);
        $this->_em->persist($file);
        $this->_em->flush();

        // prepare file for uploading
        $adapter->setDestination(APPLICATION_PATH . DIRECTORY_SEPARATOR . $fileDirectory);
        $adapter->addFilter('Rename', array('target'=>APPLICATION_PATH . DIRECTORY_SEPARATOR . $file->getLocation()));
        $adapter->setOptions(array('useByteString'=>false));

        if($adapter->receive()){
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
    $query = $this->_em->createQuery('SELECT f FROM Application_Model_File f');
    $files = $query->getResult();

    $this->view->listFiles = $files;
  }

  public function downloadAction(){
    $fileId = $this->_getParam('id');

    if(!empty($fileId)){
      $fileId = preg_replace('/[^0-9]/', '', $fileId);
      $file = $this->_em->getRepository('Application_Model_File')->findOneById($fileId);
      if($file){
        $downloadPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . $file->getLocation();

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
        
        if(empty($document)) {
           $this->_helper->flashMessenger->addMessage('The file could not be parsed.');
        }else {
          $this->_em->persist($document);
          $this->_em->flush();
          $this->_helper->flashMessenger->addMessage('The file was successfully parsed.');
        }
      }
    }
    $this->_helper->redirector('list', 'file');
  }

}

?>
