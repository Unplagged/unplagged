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
     $this->_helper->redirector('list', 'case');
  }

  public function createAction(){
    $createForm = new Application_Form_Case_Create();
    
    if($this->_request->isPost()){
      $this->handleCreationData($createForm);
    }
    $this->view->createForm = $createForm;
  }
  
  public function listAction(){
    $query = $this->_em->createQuery('SELECT c FROM Application_Model_InvestigationCase c');
    $cases = $query->getResult();

    $this->view->listCases = $cases;
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
