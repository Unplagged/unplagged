<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DocumentController
 *
 * @author benjamin
 */
class DocumentController extends Zend_Controller_Action{

  public function init(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
    $this->_defaultNamespace = new Zend_Session_Namespace('Default');
    $this->view->flashMessages = $this->_helper->flashMessenger->getMessages();
  }

  public function indexAction(){
    $this->_helper->redirector('list', 'document');
  }
  
    public function listAction(){
    $query = $this->_em->createQuery('SELECT d FROM Application_Model_Document d');
    $documents = $query->getResult();

    $this->view->listDocuments = $documents;
  }
}

?>
