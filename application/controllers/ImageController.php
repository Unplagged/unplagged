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
 * This controller provides functionality to access images that are not
 * public, i. e. where a user needs the right permission for the file.
 * 
 * @author Unplagged
 */
class ImageController extends Unplagged_Controller_Action{

  public function init(){
    parent::init();

    $this->_helper->viewRenderer->setNoRender(true);
    $this->_helper->layout()->disableLayout();
  }

  /**
   * Sends the requested image file with the appropriate content-type, in order to get browsers to display it like
   * a really downloaded image in a publicly accessible folder. 
   */
  public function showAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $file = $this->_em->getRepository('Application_Model_File')->findOneById($input->id);
      if($file){
        if(!$file->getFolder()){
          $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'file', 'action'=>'read', 'base'=>$file));
          if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
            $this->getResponse()->setHttpResponseCode(403);
          }
        }
        
        $localPath = $file->getFullPath();
        $allowedExtensions = array('jpg', 'jpeg', 'gif', 'png');

        $response = $this->getResponse();
        if(is_readable($localPath)){
          $this->getResponse()->setHeader('Expires', '', true);
          $this->getResponse()->setHeader('Cache-Control', 'private', true);
          $this->getResponse()->setHeader('Cache-Control', 'max-age=360000');
          $this->getResponse()->setHeader('Pragma', '', true);
          
          
          if(in_array($file->getExtension(), $allowedExtensions)){
            $response->setHeader('Content-type', 'image/' . $file->getExtension());
          }else{
            $response->setHeader('Content-type', $file->getMimeType());
          }

          readfile($localPath);
        }else{
          $response->setHttpResponseCode(404);
        }
      }else{
        $this->_helper->FlashMessenger('No file found.');
        $this->_helper->redirector('list', 'file');
      }
    }
  }

}

?>