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
 * This controller class handles all user authentication behaviour, i. e. login and logout.
 *
 * @author Unplagged Development Team
 */
class AuthController extends Unplagged_Controller_Action{

  private $auth;

  public function init(){
    parent::init();
    $this->auth = Zend_Auth::getInstance();
  }

  /**
   * Forwards to the login action.
   * 
   * The login action isn't done directly here, to keep the possibility of adding something else to the index later on.
   */
  public function indexAction(){
    $this->_forward('login');
  }

  /**
   * Shows the login form or handles the login data if something was sent.
   * 
   * The user is redirected to the last visited page.
   */
  public function loginAction(){
    if($this->auth->hasIdentity()){
      $this->redirectToLastPage();
    }else{
      $loginForm = new Application_Form_Auth_Login();

      $request = $this->getRequest();
      if($request->isPost()){
        $this->handleLoginData($loginForm);
      }

      $this->view->loginForm = $loginForm;
    }
  }

  /**
   * Checks the POST data for valid login credentials and sets session data.
   * 
   * @param Application_Form_Auth_Login $loginForm 
   */
  private function handleLoginData(Application_Form_Auth_Login $loginForm){
    $formData = $this->_request->getPost();

    if($loginForm->isValid($formData)){
      $username = $this->getRequest()->getParam('username');
      $password = Unplagged_Helper::hashString($this->getRequest()->getParam('password'));

      $adapter = new Unplagged_Auth_Adapter_Doctrine($this->_em, 'Application_Model_User', 'username', 'password', $username, $password);
      $result = $this->auth->authenticate($adapter);

      if($result->isValid()){
        $defaultNamespace = new Zend_Session_Namespace('Default');
        $defaultNamespace->user = $result->getIdentity();
        $defaultNamespace->userId = $result->getIdentity()->getId();

        $this->_helper->flashMessenger->addMessage('You were logged in successfully.');
        $this->redirectToLastPage();
      }else{
        $this->_helper->flashMessenger->addMessage('Login failed.');
        $this->_helper->redirector('login', 'auth');
      }
    }
  }
  
  /**
   * Logs the user off. The identity is removed and the session is cleared.
   */
  public function logoutAction(){
    $this->logout();
    $this->_helper->flashMessenger->addMessage('You were logged off successfully.');
    $this->redirectToLastPage();
  }
  
  /**
   * Clears all session data that was stored for the current user. 
   */
  private function logout(){
    $this->auth->clearIdentity();
    Zend_Session::forgetMe();
    unset($this->_defaultNamespace->userId);
  }

}
