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
 * A bootstrap plugin, that checks if the current user is allowed to access the requested resource.
 * 
 * Based on the tutorial at {@link http://codeutopia.net/blog/2009/02/18/zend_acl-part-3-creating-and-storing-dynamic-acls/}. 
 */
class Unplagged_AccessControl extends Zend_Controller_Plugin_Abstract{

  private $acl = null;
  private $user = null;

  public function __construct(Zend_Acl $acl, Application_Model_User $user){
    $this->acl = $acl;
    $this->user = $user;
  }

  public function preDispatch(Zend_Controller_Request_Abstract $request){

    $role = $this->user->getRole();

    $front = Zend_Controller_Front::getInstance();
    $bootstrap = $front->getParam('bootstrap');
    $bootstrap->bootstrap('layout');
    $layout = $bootstrap->getResource('layout');

    //set the role in the view, so that the navigation is displayed appropriately
    $view = $layout->getView();
    $view->navigation()->setRole($role);

    //For this example, we will use the controller as the resource:
    $resource = $request->getControllerName();
    $action = $request->getActionName();

    var_dump($resource);
    var_dump($action);
    $resourceKey = $resource . '_' . $action;
    
    $allowAlways = array(
      'auth_login',
      'user_register',
      'auth_logout',
      'user_verify',
      'user_recover-password'
    );
    
    if(!in_array($resourceKey, $allowAlways) && ($this->acl->has($resourceKey) && !$this->acl->isAllowed($role, $resourceKey))){
      //If the user has no access we send him elsewhere by changing the request
      if(Zend_Auth::getInstance()->hasIdentity()){
        $request->setControllerName('index');
        $request->setActionName('empty');
        $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
        
        $flashMessenger->addMessage('You are not allowed to access this page.');
      }else {
        $request->setControllerName('auth')
            ->setActionName('login');
      }
    }
  }

}
?>
