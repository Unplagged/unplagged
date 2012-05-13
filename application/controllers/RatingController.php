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
class RatingController extends Unplagged_Controller_Action{

  public function indexAction(){
    
  }

  /**
   * Handles the creation of a new rating. 
   */
  public function createAction(){
    $input = new Zend_Filter_Input(array('source'=>'Digits', 'redirect'=>'StringTrim'), null, $this->_getAllParams());

    $modifyForm = new Application_Form_Rating_Modify();
    $modifyForm->setAction('/document_fragment/rate/source/' . $input->source);

    if($this->_request->isPost()){
      $result = $this->handleModifyData($modifyForm);

      if($result){
        // notification
        //$user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
        //Unplagged_Helper::notify("case_created", $result, $user);

        $this->_helper->FlashMessenger(array('success'=>'Your rating was added successfully.'));
        $this->_redirect($input->redirect);
      }
    }

    $this->view->modifyForm = $modifyForm;
    $this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'rating'));
  }

  /**
   * Handles the edit of an existing rating. 
   */
  public function editAction(){
    $input = new Zend_Filter_Input(array('source'=>'Digits', 'id'=>'Digits', 'redirect'=>'StringTrim'), null, $this->_getAllParams());

    $rating = $this->_em->getRepository('Application_Model_Rating')->findOneById($input->id);
    if($rating){
      $modifyForm = new Application_Form_Rating_Modify();
      $modifyForm->setAction('/document_fragment/rate/id/' . $input->id . '/source/' . $input->source);

      $modifyForm->getElement("rating")->setValue($rating->getRating() ? '1' : '0');
      $modifyForm->getElement("reason")->setValue($rating->getReason());

      $modifyForm->getElement("submit")->setLabel("Save rating");

      if($this->_request->isPost()){
        $result = $this->handleModifyData($modifyForm, $rating);

        if($result){
          // notification
          //$user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
          //Unplagged_Helper::notify("case_created", $result, $user);

          $this->_helper->FlashMessenger(array('success'=>'Your rating was edited successfully.'));
          $this->_redirect($input->redirect);
        }
      }

      $this->view->modifyForm = $modifyForm;
      $this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'rating'));
    }
  }

  private function handleModifyData(Application_Form_Rating_Modify $modifyForm, Application_Model_Rating $rating = null){
    $input = new Zend_Filter_Input(array('source'=>'Digits'), null, $this->_getAllParams());

    if(!($rating)){
      $rating = new Application_Model_Rating();
    }

    $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
    $source = $this->_em->getRepository('Application_Model_Base')->findOneById($input->source);

    $formData = $this->_request->getPost();
    if($modifyForm->isValid($formData)){

      $rating->setRating($formData['rating'] == 1);
      $rating->setReason($formData['reason']);
      $rating->setUser($user);
      $rating->setSource($source);

      // write back to persistence manager and flush it
      $this->_em->persist($rating);
      $this->_em->flush();

      return $rating;
    }

    return false;
  }

  /**
   * Displays a list with all ratings a source.
   */
  public function listAction(){
    $this->_helper->viewRenderer->setNoRender(true);
    $this->_helper->layout->disableLayout();

    $input = new Zend_Filter_Input(array('source'=>'Digits', 'returnType'=>'Alnum'), null, $this->_getAllParams());

    $source = $this->_em->getRepository('Application_Model_Base')->findOneById($input->source);

    if($source){
      $ratings = $this->_em->getRepository("Application_Model_Rating")->findBySource($input->source);

      if($input->returnType == 'json'){
        $result = array();
      }else{
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $result = $this->_request->getParam('data');
      }

      foreach($ratings as $rating){
        $result[] = $rating->toArray(array('source'));
      }

      if($input->returnType == 'json'){
        $this->_helper->json($result);
      }else{
        $this->_request->setParam('data', $result);
        $this->_request->setParam('types', $this->_request->getParam('types'));
        $this->_forward('conversation', 'notification', null);
      }
    }else{
      if($input->type == 'json'){
        $result = array();
        $result["errorcode"] = 400;
        $result["message"] = "No comments available.";

        $this->_helper->json($result);
      }else{
        $this->_request->setParam('data', $result);
        $this->_request->setParam('types', $this->_request->getParam('types'));
        $this->_forward('conversation', 'notification', null);
      }
    }
  }

}