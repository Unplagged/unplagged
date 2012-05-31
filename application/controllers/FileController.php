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
    $uploadform = new Application_Form_File_Upload();

    if($this->_request->isPost()){
      $post = $this->_request->getPost();
      if($uploadform->isValid($post)){
        $this->storeUpload();
      }
    } else {
      $this->view->form = $uploadform;
    }
  }

  /**
    * Based on upload.php from Plupload.
    *
    * Copyright 2009, Moxiecode Systems AB
    * Released under GPL License.
    *
    * License: http://www.plupload.com/license
    * Contributing: http://www.plupload.com/contributing
    */
  private function storeUpload(){

    $adapter = new Zend_File_Transfer();
    $fileName = pathinfo($adapter->getFileName(), PATHINFO_BASENAME);
    $fileExtension = pathinfo($adapter->getFileName(), PATHINFO_EXTENSION);

    $targetDir = BASE_PATH . DIRECTORY_SEPARATOR . 'temp';

    // 5 minutes execution time
    @set_time_limit(5 * 60);

    $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
    $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

    // Make sure the fileName is unique but only if chunking is disabled
    if($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)){
      $ext = strrpos($fileName, '.');
      $fileName_a = substr($fileName, 0, $ext);

      $count = 1;
      while(file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileExtension)){
        $count++;
      }

      $fileName = $fileName_a . '_' . $count . $fileExtension;
    }

    $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

    // Look for the content type header
    if(isset($_SERVER["HTTP_CONTENT_TYPE"]))
      $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

    if(isset($_SERVER["CONTENT_TYPE"]))
      $contentType = $_SERVER["CONTENT_TYPE"];

    // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
    if(strpos($contentType, "multipart") !== false){
      if(isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])){
        // Open temp file
        $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
        if($out){
          // Read binary input stream and append it to temp file
          $in = fopen($_FILES['file']['tmp_name'], "rb");

          if($in){
            while($buff = fread($in, 4096))
              fwrite($out, $buff);
          } else
            die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
          fclose($in);
          fclose($out);
          @unlink($_FILES['file']['tmp_name']);
        } else
          die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
      } else
        die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
    } else{
      // Open temp file
      $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
      if($out){
        // Read binary input stream and append it to temp file
        $in = fopen("php://input", "rb");

        if($in){
          while($buff = fread($in, 4096))
            fwrite($out, $buff);
        } else
          die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

        fclose($in);
        fclose($out);
      } else
        die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
    }

    // Check if file has been uploaded
    if(!$chunks || $chunk == $chunks - 1){
      // Strip the temp .part suffix off 
      rename("{$filePath}.part", $filePath);
      $fileinfo = fileinfo($filePath);
      var_dump($fileinfo);
      $newName = $this->_request->getPost('newName');
      $description = $this->_request->getPost('description');

      // collect file information
      // store file in database to get an id
      $data = array();
      $data["size"] = $adapter->getFileSize();
      //if the mime type is always application/octet-stream, then the 
      //mime magic and fileinfo extensions are probably not installed
      $data["mimetype"] = $adapter->getMimeType();
      $data["filename"] = !empty($newName) ? $newName . "." . $fileExt : $fileName;
      $data["extension"] = $fileExt;
      $data["location"] = BASE_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
      $data['description'] = $description;

/*
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

        $this->_helper->FlashMessenger(array('success'=>'File was uploaded successfully.'));
        $this->_helper->redirector('list', 'file');
      }else{
        $this->_em->remove($file);
        $this->_em->flush();

        $this->_helper->FlashMessenger(array('error'=>'File could not be uploaded.'));
      }*/
    }


    // Return JSON-RPC response
    die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
  }

  public function listAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());

    $this->setTitle('Public Files');

    $query = $this->_em->createQuery("SELECT f FROM Application_Model_File f");
    $count = $this->_em->createQuery("SELECT COUNT(f.id) FROM Application_Model_File f");

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

    // generate the action dropdown for each file
    // @todo: use centralised method for all three file lists
    foreach($paginator as $file){
      $file->actions = array();

      $parseAction['link'] = '/file/parse/id/' . $file->getId();
      $parseAction['title'] = 'Parsing big files can take very long, you will be notified when this action is finalized.';
      $parseAction['name'] = 'parse';
      $parseAction['label'] = 'Parse';
      $parseAction['icon'] = 'images/icons/page_gear.png';
      $file->actions[] = $parseAction;

      $action['link'] = '/file/download/id/' . $file->getId();
      $action['label'] = 'Download';
      $action['icon'] = 'images/icons/disk.png';
      $file->actions[] = $action;

      $action['link'] = '/file/delete/id/' . $file->getId();
      $action['label'] = 'Delete';
      $action['icon'] = 'images/icons/delete.png';
      $file->actions[] = $action;

      $action['link'] = '/user/add-file/id/' . $file->getId();
      $action['label'] = 'Add to personal files';
      $action['icon'] = 'images/icons/basket_put.png';
      $file->actions[] = $action;


      $action['link'] = '/case/add-file/id/' . $file->getId();
      $action['label'] = 'Add to current case';
      $action['icon'] = 'images/icons/package_add.png';
      $file->actions[] = $action;
    }

    $this->view->paginator = $paginator;
    $this->view->uploadLink = '/file/upload?area=public';
  }

  /**
   * Enables 
   */
  public function downloadAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $file = $this->_em->getRepository('Application_Model_File')->findOneById($input->id);
      if($file){
        // disable view
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
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
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(empty($input->id)){
      // show error message
      $this->_helper->FlashMessenger('The fileId has to be set.');
    }else{
      $file = $this->_em->getRepository('Application_Model_File')->findOneById($input->id);
      $language = "eng";

      if(empty($file)){
        // show error message
        $this->_helper->FlashMessenger('No file found by that id.');
      }else{
        // pdfs will e generated through cron
        if($file->getExtension() == "pdf"){
          $data["title"] = $file->getFilename();
          $data["initialFile"] = $file;
          $data["state"] = $this->_em->getRepository('Application_Model_State')->findOneByName('task_scheduled');
          $document = new Application_Model_Document($data);

          // start task
          $data = array();
          $data["initiator"] = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
          $data["ressource"] = $document;
          $data["action"] = $this->_em->getRepository('Application_Model_Action')->findOneByName('file_parse');
          $data["state"] = $this->_em->getRepository('Application_Model_State')->findOneByName('task_scheduled');
          $task = new Application_Model_Task($data);

          $this->_em->persist($task);
          $this->_em->flush();

          $this->_helper->FlashMessenger('The file will be generated now, you will be notified as soon as possible.');

          // images will be parsed directly
        }else{
          $parser = Unplagged_Parser::factory($file->getMimeType());

          $document = $parser->parseToDocument($file, $language);
          if(empty($document)){
            $this->_helper->FlashMessenger(array('error'=>'The file could not be parsed.'));
          }else{
            $document->setState($this->_em->getRepository('Application_Model_State')->findOneByName('parsed'));

            $this->_em->persist($document);
            $this->_em->flush();
            $this->_helper->FlashMessenger(array('success'=>'The file was successfully parsed.'));
          }
        }

        $case = Zend_Registry::getInstance()->user->getCurrentCase();
        $case->addDocument($document);
        $this->_em->persist($case);
        $this->_em->flush();
      }
    }
    $this->_helper->redirector('list', 'file');
  }

  /**
   * Deletes a single file. 
   */
  public function deleteAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $file = $this->_em->getRepository('Application_Model_File')->findOneById($input->id);
      if($file){

        // remove file from file system
        $downloadPath = $file->getAbsoluteLocation() . DIRECTORY_SEPARATOR . $file->getId() . "." . $file->getExtension();
        $deleted = unlink($downloadPath);
        if($deleted || !file_exists($downloadPath)){
          // remove database record
          $this->_em->remove($file);
          $this->_em->flush();
          $this->_helper->FlashMessenger('The file was deleted successfully.');
        }else{
          $this->_helper->FlashMessenger('The file could not be deleted.');
        }
      }else{
        $this->_helper->FlashMessenger('The file does not exist.');
      }
    }

    $this->_helper->redirector('list', 'file');

    // disable view
    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

}
?>