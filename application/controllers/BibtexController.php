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
 */
class BibtexController extends Unplagged_Controller_Action{

  public function init(){
    parent::init();

    $case = Zend_Registry::getInstance()->user->getCurrentCase();
    if(!$case){
      $errorText = 'You have to select a case, before you can access the bibliography.';
      $this->_helper->FlashMessenger(array('error'=>$errorText));
      $this->redirectToLastPage();
    }
  }

  public function indexAction(){
    $this->_helper->redirector('list', 'bibtex');
  }

  /**
   * Show bibtex information of a document
   * 
   */
  public function showAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $bibTex = $this->_em->getRepository('Application_Model_BibTex')->findOneById($input->id);
      $this->view->bibTex = $bibTex;
    }

    $this->setTitle('Bibliography');
  }

  /**
   * Lists all documents.
   */
  public function listAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());
    $case = Zend_Registry::getInstance()->user->getCurrentCase();

    $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'read', 'base'=>null));
    $query = 'SELECT d FROM Application_Model_BibTex d JOIN d.document b';
    $count = 'SELECT COUNT(d.id) FROM Application_Model_BibTex d JOIN d.document b';

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count, array('b.case'=>$case->getId()), null, $permission));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

    // generate the action dropdown for each file
    foreach($paginator as $bibTex):
      $bibTex->actions = array();
      $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$bibTex->getDocument()));
      if(Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
        $action['link'] = '/document/edit/id/' . $bibTex->getDocument()->getId();
        $action['label'] = 'Edit bibliography';
        $action['icon'] = 'images/icons/pencil.png';
        $bibTex->actions[] = $action;
      }
    endforeach;

    $this->view->paginator = $paginator;
    $this->setTitle('Bibliography');
  }

}

?>
