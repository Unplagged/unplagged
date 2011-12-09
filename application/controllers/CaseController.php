<?php

/**
 * File for class {@link CaseController}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class CaseController extends Zend_Controller_Action{

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
    $auth = Zend_Auth::getInstance();

    if($auth->hasIdentity()){
      $this->_helper->redirector('index', 'index');
    }

    $this->_forward("login");
  }

  public function createAction(){
    $createForm = new Application_Form_Case_Create();
    
    if($this->_request->isPost()){
      $this->handleCreationData($createForm);
    }else{
      $this->view->createForm = $createForm;
    }
  }

  private function handleCreationData(Application_Form_Case_Create $createForm){
    $formData = $this->_request->getPost();

    if($createForm->isValid($formData)){
      $case = new Application_Model_InvestigationCase($formData['name'], $formData['alias']);

      // write back to persistence manager and flush it
      $this->_em->persist($case);
      $this->_em->flush();

      $this->_helper->flashMessenger->addMessage('The case was successfully created.');
      $this->_helper->redirector('index', 'case');
    }else{
      //@todo error message here
    }
  }

}

?>
