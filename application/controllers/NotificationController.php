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
 * 
 *
 * @author Unplagged Development Team
 */
class NotificationController extends Unplagged_Controller_Action{

  public function indexAction(){
    $this->_helper->redirector('recent-activity', 'notification');
  }

  /**
   * Displays a list with the most recent activites related to a user.
   */
  public function recentActivityAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());

    $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'base', 'action'=>'read', 'base'=>null));
    $query = 'SELECT n FROM Application_Model_Notification n JOIN n.permissionSource b';
    $count = 'SELECT COUNT(n.id) FROM Application_Model_Notification n JOIN n.permissionSource b';

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count, null, 'n.created DESC', $permission, true));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

    $this->view->paginator = $paginator;
  }

  public function commentsAction(){
    $input = new Zend_Filter_Input(array('source'=>'Digits'), null, $this->_getAllParams());
    $source = $this->_em->getRepository('Application_Model_Base')->findOneById($input->source);

    if($source){
      $query = $this->_em->createQuery("SELECT c FROM Application_Model_Comment c");
      $result = $query->getArrayResult();

      $this->_helper->json($result);
    }else{
      $result = array();
      $result["errorcode"] = 400;
      $result["message"] = "No comments available.";

      $this->_helper->json($result);
    }
  }

  public function conversationAction(){
    $input = new Zend_Filter_Input(array('source'=>'Digits'), null, $this->_getAllParams());

    $result = $this->_request->getParam('data');
    if(!isset($result)){
      $result = array();
    }

    // here is determined which model has which types of messages in the conversation stream
    $types = $this->_request->getParam('types');
    if(!isset($types)){
      $source = $this->_em->getRepository('Application_Model_Base')->findOneById($input->source);
      $types = $source->getConversationTypes();
    }

    // at each step, the next type of the conversation strem types is picked and the items related to the type are selected
    if(count($types) > 0){
      $this->_helper->viewRenderer->setNoRender(true);
      $this->_helper->layout->disableLayout();

      $type = array_pop($types);

      $this->_request->setParam('types', $types);
      $this->_request->setParam('data', $result);

      switch($type){
        case 'comment':
          $this->_forward('list', 'comment', null, array('source'=>$input->source, 'conversation'=>true));
          break;
        case 'rating':
          $this->_forward('list', 'rating', null, array('source'=>$input->source, 'conversation'=>true));
          break;
      }
    }else{

      // before the result is returned, all the items are sorted by there creation date
      function date_sort($a, $b){
        return strcmp($a['created']['dateTime'], $b['created']['dateTime']);
      }

      usort($result, 'date_sort');

      $this->_helper->json($result);
    }
  }

}

?>