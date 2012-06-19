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
class BibtexController extends Unplagged_Controller_Action{

  public function init(){
    parent::init();

    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    Zend_Layout::getMvcInstance()->menu = 'document-tools';
    Zend_Layout::getMvcInstance()->versionableId = $input->id;
  }

  public function indexAction(){
    $this->_helper->redirector('list', 'bibtex');
  }

  
  /**
   * Lists all documents.
   */
  public function listAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());
    $case = Zend_Registry::getInstance()->user->getCurrentCase();

    if($case){
      $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'read', 'base'=>null));
      $query = 'SELECT b FROM Application_Model_Document b';
      $count = 'SELECT COUNT(b.id) FROM Application_Model_Document b';
	  
      $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count, array('b.case'=>$case->getId()), null, $permission));
      $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
      $paginator->setCurrentPageNumber($input->page);

      // generate the action dropdown for each file
      foreach($paginator as $document):
        if($document->getState()->getName() == 'task_scheduled'){
          // find the associated task and get percentage
          $state = $this->_em->getRepository('Application_Model_State')->findOneByName('task_running');
          $task = $this->_em->getRepository('Application_Model_Task')->findOneBy(array('ressource'=>$document->getId(), 'state'=>$state));
          if(!$task){
            $percentage = 0;
          }else{
            $percentage = $task->getProgressPercentage();
          }
          $document->outputState = '<div class="progress"><div class="bar" style="width: ' . $percentage . '%;"></div></div>';
        }else{
          $document->outputState = $document->getState()->getTitle();
        }

        $document->actions = array();
        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$document));
        if(Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
          $action['link'] = '/document/edit/id/' . $document->getId();
          $action['label'] = 'Edit bibtex';
          $action['icon'] = 'images/icons/pencil.png';
          $document->actions[] = $action;
        }
        
        
      endforeach;

      $this->view->paginator = $paginator;

      Zend_Layout::getMvcInstance()->sidebar = null;
      Zend_Layout::getMvcInstance()->versionableId = null;
    }else{
      $this->_helper->FlashMessenger('You need to select a case first.');
    }
  }

  

}

?>
