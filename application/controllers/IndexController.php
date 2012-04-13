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
 */
class IndexController extends Zend_Controller_Action{

  public function init(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
    $this->_defaultNamespace = new Zend_Session_Namespace('Default');
    $this->view->flashMessages = $this->_helper->flashMessenger->getMessages();
  }

  public function indexAction(){
    if($this->_defaultNamespace->userId) {
      $this->_helper->redirector('recent-activity', 'notification');
    }
    Zend_Registry::get('Log')->debug('Index');
    
    /*$pageNumber = 1;
    $query = $this->_em->createQuery('
      SELECT f FROM Application_Model_Document_Fragment f
      JOIN f.posStart s
      JOIN s.page sp
      JOIN f.posEnd e
      JOIN e.page ep
      WHERE :pn >= sp.pageNumber AND :pn <= ep.pageNumber');
    $query->setParameter("pn", $pageNumber);
    
    $fragments = $query->getResult();
    foreach($fragments as $fragment) {
      echo "fragment: from[page:" . $fragment->getPosStart()->getPage()->getPageNumber() . " line:" . $fragment->getPosStart()->getLinePos() . " char:" . $fragment->getPosStart()->getCharacterPos() ."]" . 
          "- to[page:" . $fragment->getPosEnd()->getPage()->getPageNumber() . " line:" . $fragment->getPosEnd()->getLinePos() . " char:" . $fragment->getPosEnd()->getCharacterPos() ."]<br />";
    }*/
    $query = $this->_em->createQuery('SELECT p, d FROM Application_Model_Document d JOIN d.pages p WHERE d.id = 5 AND p.id > 1');
    //$query->setParameter("pn", $pageNumber);
     
     print_r($query->getArrayResult());
//    foreach($pages as $page) {
     // print_r($page);
 //    echo $page->getId();
  //  }
  }

}
