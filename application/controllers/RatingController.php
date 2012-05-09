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
    $input = new Zend_Filter_Input(array('source'=>'Digits'), null, $this->_getAllParams());
    
    $modifyForm = new Application_Form_Rating_Modify();
    $modifyForm->setAction("/document_fragment/rate/source/" . $input->source);
    
    if($this->_request->isPost()){
      $result = $this->handleModifyData($modifyForm);

      if($result){
        // notification
        //$user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
        //Unplagged_Helper::notify("case_created", $result, $user);

        $this->_helper->FlashMessenger(array('success'=>'The rating was added successfully.'));
        //$this->_helper->redirector('list', 'case');
      }
    }
    
    $this->view->modifyForm = $modifyForm;
    $this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'rating'));
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

}