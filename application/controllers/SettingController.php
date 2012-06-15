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
 * Handles action related to cases.
 */
class SettingController extends Unplagged_Controller_Action{

  public function indexAction(){
    
  }

  /**
   * Creates a new state. 
   */
  public function createStateAction(){
    $modifyForm = new Application_Form_Setting_State_Modify();

    if($this->_request->isPost()){
      $result = $this->handleStateModifyData($modifyForm);

      if($result){
        // notification
        // @todo: add notification
        //$user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
        //Unplagged_Helper::notify("case_created", $result, $user);

        $this->_helper->FlashMessenger(array('success'=>'Your state was added successfully.'));
        $this->_redirect('setting/list-states');
      }
    }

    $this->view->title = 'Create state';
    $this->view->modifyForm = $modifyForm;
    $this->_helper->viewRenderer->renderBySpec('state/modify', array('controller'=>'setting'));
  }

  /**
   * Edits an existing state. 
   */
  public function editStateAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    $state = $this->_em->getRepository('Application_Model_State')->findOneById($input->id);

    if($state){
      $modifyForm = new Application_Form_Setting_State_Modify();
      $modifyForm->setAction("/setting/edit-state/id/" . $input->id);

      $modifyForm->getElement("name")->setValue($state->getName());
      $modifyForm->getElement("title")->setValue($state->getTitle());
      $modifyForm->getElement("description")->setValue($state->getDescription());
      $modifyForm->getElement("submit")->setLabel("Save state");

      if($this->_request->isPost()){
        $result = $this->handleStateModifyData($modifyForm, $state);

        if($result){
          // notification
          //$user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
          //Unplagged_Helper::notify("case_updated", $result, $user);

          $this->_helper->FlashMessenger(array('success'=>'The state was updated successfully.'));
          $this->_helper->redirector('list-states', 'setting');
        }
      }

      $this->view->title = "Edit state";
      $this->view->modifyForm = $modifyForm;
      $this->_helper->viewRenderer->renderBySpec('state/modify', array('controller'=>'setting'));
    }else{
      $this->_helper->FlashMessenger(array('error'=>'The specified state does not exist.'));
      $this->_helper->redirector('list-states', 'setting');
    }
  }

  /**
   * Deletes a single state. 
   */
  public function deleteStateAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    $state = $this->_em->getRepository('Application_Model_State')->findOneById($input->id);
    if($state){
      $this->_em->remove($state);
      $this->_em->flush();
      $this->_helper->FlashMessenger(array('success'=>'The state was deleted successfully.'));
    }else{
      $this->_helper->FlashMessenger(array('error'=>'The action does not exist.'));
    }
    $this->_helper->redirector('list-states', 'setting');
  }

  /**
   * Lists all states in the application.
   */
  public function listStatesAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());

    $query = 'SELECT b FROM Application_Model_State b';
    $count = 'SELECT COUNT(b.id) FROM Application_Model_State b';

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

    // generate the action dropdown for each fragment
    foreach($paginator as $state):
      $state->actions = array();

      $action['link'] = '/setting/edit-state/id/' . $state->getId();
      $action['label'] = 'Edit state';
      $action['icon'] = 'images/icons/pencil.png';
      $state->actions[] = $action;

      $action['link'] = '/setting/delete-state/id/' . $state->getId();
      $action['label'] = 'Delete state';
      $action['icon'] = 'images/icons/delete.png';
      $state->actions[] = $action;
    endforeach;

    $this->view->paginator = $paginator;
    $this->_helper->viewRenderer->renderBySpec('state/list', array('controller'=>'setting'));
  }

  /**
   * Creates a new action.
   */
  public function createActionAction(){
    $modifyForm = new Application_Form_Setting_Action_Modify();

    if($this->_request->isPost()){
      $result = $this->handleActionModifyData($modifyForm);

      if($result){
        // notification
        // @todo: add notification
        //$user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
        //Unplagged_Helper::notify("case_created", $result, $user);

        $this->_helper->FlashMessenger(array('success'=>'Your action was added successfully.'));
        $this->_redirect('setting/list-actions');
      }
    }

    $this->view->title = 'Create action';
    $this->view->modifyForm = $modifyForm;
    $this->_helper->viewRenderer->renderBySpec('action/modify', array('controller'=>'setting'));
  }

  /**
   * Edits an existing action. 
   */
  public function editActionAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    $action = $this->_em->getRepository('Application_Model_Action')->findOneById($input->id);

    if($action){
      $modifyForm = new Application_Form_Setting_Action_Modify();
      $modifyForm->setAction("/setting/edit-action/id/" . $input->id);

      $modifyForm->getElement("name")->setValue($action->getName());
      $modifyForm->getElement("title")->setValue($action->getTitle());
      $modifyForm->getElement("description")->setValue($action->getDescription());
      $modifyForm->getElement("submit")->setLabel("Save action");

      if($this->_request->isPost()){
        $result = $this->handleActionModifyData($modifyForm, $action);

        if($result){
          // notification
          //$user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
          //Unplagged_Helper::notify("case_updated", $result, $user);

          $this->_helper->FlashMessenger(array('success'=>'The action was updated successfully.'));
          $this->_helper->redirector('list-actions', 'setting');
        }
      }

      $this->view->title = "Edit action";
      $this->view->modifyForm = $modifyForm;
      $this->_helper->viewRenderer->renderBySpec('action/modify', array('controller'=>'setting'));
    }else{
      $this->_helper->FlashMessenger(array('error'=>'The specified action does not exist.'));
      $this->_helper->redirector('list-actions', 'setting');
    }
  }

  /**
   * Lists all action in the application. 
   */
  public function listActionsAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());

    $query = 'SELECT a FROM Application_Model_Action a';
    $count = 'SELECT COUNT(a.id) FROM Application_Model_Action a';

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

    // generate the action dropdown for each fragment
    foreach($paginator as $aaction):
      $aaction->actions = array();

      $action['link'] = '/setting/edit-action/id/' . $aaction->getId();
      $action['label'] = 'Edit action';
      $action['icon'] = 'images/icons/pencil.png';
      $aaction->actions[] = $action;

      $action['link'] = '/setting/delete-action/id/' . $aaction->getId();
      $action['label'] = 'Delete action';
      $action['icon'] = 'images/icons/delete.png';
      $aaction->actions[] = $action;
      
    endforeach;

    $this->view->paginator = $paginator;
    $this->_helper->viewRenderer->renderBySpec('action/list', array('controller'=>'setting'));
  }
  
    /**
   * Deletes a single action. 
   */
  public function deleteActionAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    $action = $this->_em->getRepository('Application_Model_Action')->findOneById($input->id);
    if($action){
      $this->_em->remove($action);
      $this->_em->flush();
      $this->_helper->FlashMessenger(array('success'=>'The action was deleted successfully.'));
    }else{
      $this->_helper->FlashMessenger(array('error'=>'The action does not exist.'));
    }
    $this->_helper->redirector('list-actions', 'setting');
  }

  /**
   * Handles the form data for modifing a state.
   * 
   * @param Application_Form_Setting_State_Modify $modifyForm
   * @param Application_Model_State $state
   * @return \Application_Model_State|boolean 
   */
  private function handleStateModifyData(Application_Form_Setting_State_Modify $modifyForm, Application_Model_State $state = null){
    if(!($state)){
      $state = new Application_Model_State();
    }

    $formData = $this->_request->getPost();
    if($modifyForm->isValid($formData)){

      $state->setName($formData['name']);
      $state->setTitle($formData['title']);
      $state->setDescription($formData['description']);

      // write back to persistence manager and flush it
      $this->_em->persist($state);
      $this->_em->flush();

      return $state;
    }

    return false;
  }

  /**
   * Handles the form data for modifying an action.
   * 
   * @param Application_Form_Setting_Action_Modify $modifyForm
   * @param Application_Model_Action $action
   * @return Application_Model_Action|boolean 
   */
  private function handleActionModifyData(Application_Form_Setting_Action_Modify $modifyForm, Application_Model_Action $action = null){
    if(!($action)){
      $action = new Application_Model_Action();
    }

    $formData = $this->_request->getPost();
    if($modifyForm->isValid($formData)){

      $action->setName($formData['name']);
      $action->setTitle($formData['title']);
      $action->setDescription($formData['description']);

      // write back to persistence manager and flush it
      $this->_em->persist($action);
      $this->_em->flush();

      return $action;
    }

    return false;
  }

}

?>
