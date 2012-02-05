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
