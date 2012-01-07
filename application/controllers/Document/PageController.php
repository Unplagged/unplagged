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
class Document_PageController extends Zend_Controller_Action{

  public function init(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
    $this->_defaultNamespace = new Zend_Session_Namespace('Default');
    $this->view->flashMessages = $this->_helper->flashMessenger->getMessages();
  }

  public function indexAction(){
    $this->_helper->redirector('list', 'document_page');
  }

  public function listAction(){
    $documentId = $this->_getParam('id');
    if(!empty($documentId)){
      $documentId = preg_replace('/[^0-9]/', '', $documentId);

      $qb = $this->_em->createQueryBuilder();
      $qb->add('select', 'p')
          ->add('from', 'Application_Model_Document_Page p')
          ->add('where', 'p.document = ' . $documentId);
      $query = $qb->getQuery();
      $pages = $query->getResult();

      $this->view->listPages = $pages;
    }
  }

  public function showAction(){
    $pageId = $this->_getParam('id');

    if(!empty($pageId)){
      $pageId = preg_replace('/[^0-9]/', '', $pageId);
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($pageId);
      if($page){
        $this->view->page = $page;
      }
    }
  }
  
  public function editAction() {
    $pageId = $this->getRequest()->getParam('id');
    $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($pageId);

    $editForm = new Application_Form_Document_Page_Modify();
    $editForm->setAction("/document_page/edit/id/" . $pageId);

    $editForm->getElement("pageNumber")->setValue($page->getPageNumber());
    $editForm->getElement("content")->setValue($page->getContent());

    if($this->_request->isPost()){
      $formData = $this->_request->getPost();

      if($editForm->isValid($formData)){
        $page->setPageNumber($formData["pageNumber"]);
        $page->setContent($formData["content"]);
        
        // write back to persistence manager and flush it
        $this->_em->persist($page);
        $this->_em->flush();

        $this->_helper->flashMessenger->addMessage('The document page was updated successfully.');
        $params = array('id' => $page->getDocument()->getId());
        $this->_helper->redirector('list', 'document_page', '', $params);
      }
    }

    $this->view->editForm = $editForm;
    $this->view->page = $page;
  }

}

?>
