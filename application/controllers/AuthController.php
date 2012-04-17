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
 * This controller class handles all the login and logout behaviour.
 *
 * @author Unplagged Development Team
 */
class AuthController extends Zend_Controller_Action{

  /**
   * Initalizes registry and namespace instance in the controller and allows to display flash messages in the view.
   * @see Zend_Controller_Action::init()
   */
  public function init(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
    $this->_defaultNamespace = new Zend_Session_Namespace('Default');
    $this->view->flashMessages = $this->_helper->flashMessenger->getMessages();
  }

  public function indexAction(){
    $auth = Zend_Auth::getInstance();
    if($auth->hasIdentity()){
      $this->_helper->redirector('index', 'index');
    }

    $this->_forward("login");
  }

  public function loginAction(){
    $auth = Zend_Auth::getInstance();
    if($auth->hasIdentity()){
      $this->_helper->redirector('index', 'index');
    }

    $loginForm = new Application_Form_Auth_Login();
    $request = $this->getRequest();
    if($request->isPost()){
      $formData = $this->_request->getPost();

      if($loginForm->isValid($formData)){
        $username = $this->getRequest()->getParam('username');
        $password = Unplagged_Helper::hashString($this->getRequest()->getParam("password"));

        $adapter = new Unplagged_Auth_Adapter_Doctrine($this->_em, "Application_Model_User", "username", "password", $username, $password);
        $result = $auth->authenticate($adapter);

        if($result->isValid()){
          $defaultNamespace = new Zend_Session_Namespace('Default');
          $defaultNamespace->userId = $result->getIdentity();
          ;

          $this->_helper->flashMessenger->addMessage('You were logged in successfully.');
          $this->_helper->redirector('recent-activity', 'notification');
        }else{
          $this->_helper->flashMessenger->addMessage('Login failed.');
          $this->_helper->redirector('login', 'auth');
        }
      }
    }

    $this->view->loginForm = $loginForm;
  }

  /**
   * Logs the user off. The identity is removed and the session is cleared.
   */
  public function logoutAction(){
    Zend_Auth::getInstance()->clearIdentity();
    Zend_Session::forgetMe();
    unset($this->_defaultNamespace->userId);

    $this->_helper->flashMessenger->addMessage('You were logged off successfully.');
    $this->_helper->redirector('index', 'index');
  }

}