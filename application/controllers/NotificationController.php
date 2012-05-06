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

    $query = $this->_em->createQuery("SELECT n FROM Application_Model_Notification n ORDER BY n.created DESC");
    $count = $this->_em->createQuery("SELECT COUNT(n.id) FROM Application_Model_Notification n");

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
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

}
?>