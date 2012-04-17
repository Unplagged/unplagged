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
 * This class bundles some common functions used by many of the Unplagged controllers.
 * 
 * @author Unplagged
 */
class BaseController extends Zend_Controller_Action{

  /**
   * Initalizes registry and namespace instance in the controller and allows to display flash messages in the view.
   * 
   * @see Zend_Controller_Action::init()
   */
  public function init(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
    $this->_defaultNamespace = new Zend_Session_Namespace('Default');

    $this->view->flashMessages = $this->_helper->flashMessenger->getMessages();
  }

  /**
   * Sets the page title to the given value and prepends it to the head title.
   * 
   * @param string $title 
   */
  protected function setTitle($title){
    $this->view->title = $title;
    $this->view->headTitle()->prepend($title);
  }

  /**
   * Looks up the session data and redirects the user to the page that was visited before. 
   */
  protected function redirectToLastPage(){
    $this->_helper->viewRenderer->setNoRender(true);
    $historySessionNamespace = new Zend_Session_Namespace('history');
    $this->_redirect($historySessionNamespace->last);
  }

}
?>
