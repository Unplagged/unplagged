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
 * @author Unplagged
 */
class ImageController extends Zend_Controller_Action{

  public function init(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
    $this->_helper->viewRenderer->setNoRender(true);
    $this->_helper->layout()->disableLayout();
  }

  /**
   * Displays an image specified by the name parameter.
   */
  public function indexAction(){
    $imageName = $this->_getParam('name');

    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
    $fullPath = $path . $imageName;

    if(!empty($imageName) && file_exists($fullPath)){
      $imageInfo = getimagesize($fullPath);
      $this->getResponse()->setHeader('Content-Type', $imageInfo['mime']);
      readfile($fullPath);
    }else{
      $this->getResponse()->setHttpResponseCode(404);
    }
  }
  
  
public function viewAction(){
    $fileId = $this->_getParam('id');

    if(!empty($fileId)){
      $fileId = preg_replace('/[^0-9]/', '', $fileId);
      $file = $this->_em->getRepository('Application_Model_File')->findOneById($fileId);
      if($file){
        $downloadPath = $file->getAbsoluteLocation() . DIRECTORY_SEPARATOR . $file->getId() . "." . $file->getExtension();

        if($file->getExtension()== 'jpg' || $file->getExtension()== 'jpeg' || $file->getExtension()== 'gif' || $file->getExtension()== 'png' ||$file->getExtension()== 'tiff' )
        {
            header("Content-type: image/".$file->getExtension());
        }
        else
            {
                header("Content-type: " . $file->getMimeType());
            }

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
}
?>
