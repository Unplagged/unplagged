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
    // @todo: clean input
    $page = $this->_getParam('page');
    
    $query = $this->_em->createQuery("SELECT d FROM Application_Model_Document d");
		$count = $this->_em->createQuery("SELECT COUNT(d.id) FROM Application_Model_Document d");
		
		$paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
		$paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
		$paginator->setCurrentPageNumber($page);
    
    $this->view->paginator = $paginator;
  }

  public function deleteAction(){
    $documentId = $this->_getParam('id');

    if(!empty($documentId)){
      $documentId = preg_replace('/[^0-9]/', '', $documentId);
      $document = $this->_em->getRepository('Application_Model_Document')->findOneById($documentId);
      if($document){
        $this->_em->remove($document);
        $this->_em->flush();
      }else{
        $this->_helper->flashMessenger->addMessage('The document does not exist.');
      }
    }

    $this->_helper->flashMessenger->addMessage('The document was deleted successfully.');
    $this->_helper->redirector('list', 'document');

    // disable view
    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

}

?>
