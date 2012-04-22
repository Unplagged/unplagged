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
class CommentController extends Unplagged_Controller_Action{

  public function indexAction(){
    $this->_helper->redirector('list', 'comment');
  }

  /**
   * Handles the creation of a new comment. 
   */
  public function createAction(){
    $input = new Zend_Filter_Input(array('source'=>'Digits', 'title'=>'Alpha', 'text'=>'Alpha'), null, $this->_getAllParams());

    $source = $this->_em->getRepository('Application_Model_Base')->findOneById($input->source);
    $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);

    if($source && $input->text){
      $data = array();
      $data["author"] = $user;
      $data["source"] = $source;
      $data["title"] = $input->title;
      $data["text"] = $input->text;
      
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
    $input = new Zend_Filter_Input(array('source'=>'Digits'), null, $this->_getAllParams());
    
    $source = $this->_em->getRepository('Application_Model_Base')->findOneById($input->source);

    if($source){
      $comments = $this->_em->getRepository("Application_Model_Comment")->findBySource($input->source);
      
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