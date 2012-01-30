<?php

/**
 * File for class {@link AccessControl}.
 */

/**
 * A bootstrap plugin, that checks if the current user is allowed to access the requested resource.
 * 
 * Based on the tutorial at {@link http://codeutopia.net/blog/2009/02/18/zend_acl-part-3-creating-and-storing-dynamic-acls/}. 
 */
class Unplagged_AccessControl extends Zend_Controller_Plugin_Abstract{

  private $acl = null;

  public function __construct(Zend_Acl $acl){
    $this->acl = $acl;
  }

  public function preDispatch(Zend_Controller_Request_Abstract $request){

//As in the earlier example, authed users will have the role user
    $role = 'guest';
    if(Zend_Auth::getInstance()->hasIdentity()){
      $role = 'user';

      $front = Zend_Controller_Front::getInstance();
      $bootstrap = $front->getParam('bootstrap');
      $bootstrap->bootstrap('layout');
      $layout = $bootstrap->getResource('layout');
      $view = $layout->getView();
      $view->navigation()->setRole('user');
    }

    //For this example, we will use the controller as the resource:
    $resource = $request->getControllerName();

    if(!$this->acl->isAllowed($role, $resource)){
      //If the user has no access we send him elsewhere by changing the request
      $request->setControllerName('auth')
          ->setActionName('login');
    }
  }

}
?>
