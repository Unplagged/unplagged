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
 * This controller class handles all the login and logout behaviour.
 *
 * @author Unplagged Development Team
 */
class CommentController extends Zend_Controller_Action{

  /**
   * Initalizes registry and namespace instance in the controller and allows to display flash messages in the view.
   * @see Zend_Controller_Action::init()
   */
  public function init(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
    $this->_defaultNamespace = new Zend_Session_Namespace('Default');
    $this->view->flashMessages = $this->_helper->flashMessenger->getMessages();
  }

  public function indexAction(){
    $this->_helper->redirector('list', 'comment');
  }

  public function createAction(){
    // @todo: sanitize
    $sourceId = $this->_getParam('source');
    $title = $this->_getParam('title');
    $text = $this->_getParam('text');

    $source = $this->_em->getRepository('Application_Model_Base')->findOneById($sourceId);
    $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);

    if($source && $text){
      $data = array();
      $data["author"] = $user;
      $data["source"] = $source;
      $data["title"] = $title;
      $data["text"] = $text;
      
      $comment = new Application_Model_Comment($data);
      $this->_em->persist($comment);
      $this->_em->flush();

      $result = $comment->toArray();
    } else {
      $result["errorcode"] = 500;
      $result["message"] = "Comment could not be inserted.";
    }
    $this->_helper->json($result);
  }

  /**
   * Displays a list with all activities related to a user.
   */
  public function listAction(){
    // @todo: sanitize
    $sourceId = $this->_getParam('source');
    $source = $this->_em->getRepository('Application_Model_Base')->findOneById($sourceId);

    if($source){
      $comments = $this->_em->getRepository("Application_Model_Comment")->findBySource($sourceId);
      
      $result = array();
      foreach($comments as $comment) {
        $result[] = $comment->toArray();
      }

      $this->_helper->json($result);
    }else{
      $result = array();
      $result["errorcode"] = 400;
      $result["message"] = "No comments available.";

      $this->_helper->json($result);
    }
  }

}