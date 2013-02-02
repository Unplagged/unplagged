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
namespace UnpApplication\Controller;

use \UnpCommon\Controller\BaseController;
use \Unplagged_Helper;

/**
 * This controller class handles all actions related to commenting.
 */
class CommentController extends BaseController{

  /**
   * Handles the creation of a new comment.
   * 
   * Expects three http parameters: target-element-id, comment-text and comment-title.
   */
  public function addAction(){
    $response = $this->getResponse();

    $targetElementId = $this->params('id');
    $entity = $this->em->getRepository('\UnpCommon\Model\Base')->findOneById(intval($targetElementId));
 
    $commentText = $this->params()->fromPost('comment-text');
    $commentTitle = $this->params()->fromPost('comment-title');

    $user = $this->zfcUserAuthentication()->getIdentity();
    if($entity){
      $comment = new \UnpCommon\Model\Comment($user, $entity, $commentTitle, $commentText);

      $this->em->persist($comment);
      $this->em->flush();

      //show a notification in the activity stream, but only 
      //if it wasn't a comment on an activity already
      if(!($entity instanceof \UnpCommon\Model\Activity)){
        $this->activityStream->publishActivity(
                '{actor.username} wrote a @{result.directLink}{comment} on {target.directName}', $user,
                'You wrote a @{result.directLink}{comment} on {target.directName}', '', $entity, $comment);
      }

      $response->setStatusCode(201);
      $response->getHeaders()->addHeaderLine('Location', $comment->getDirectLink());
      $response->setContent('');
    }else{
      $response->setContent('');
      $response->setStatusCode(500);
    }

    return $response;
  }

  /**
   * Displays a list with all activities related to a source.
   */
  public function listAction(){
    $input = new Zend_Filter_Input(array('source'=>'Digits', 'conversation'=>'Boolean'), null, $this->_getAllParams());

    $source = $this->_em->getRepository('Application_Model_Base')->findOneById($input->source);
    if($source){
      $comments = $this->_em->getRepository("Application_Model_Comment")->findBySource($input->source);

      $result = array();
      if($input->conversation){
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $result = $this->_request->getParam('data');
      }

      foreach($comments as $comment){
        $result[] = $comment->toArray(array('source'));
      }
    }else{
      if(!$input->conversation){
        $result = array();
        $result["errorcode"] = 400;
        $result["message"] = "No comments available.";
      }
    }

    if($input->conversation){
      $this->_forward('conversation', 'notification', null,
              array('data'=>$result, 'types'=>$this->_request->getParam('types')));
    }else{
      $this->_helper->json($result);
    }
  }

}