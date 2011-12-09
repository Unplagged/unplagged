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
    $request = $this->getRequest();
    
    if ($request->isPost()) {
      $this->handleCreationData($request);
    }else{
      $createForm = new Application_Form_Case_Create();      
      $this->view->createForm = $createForm;
    }
    
  }
  
  private function handleCreationData($request){
    
  }
}

?>
