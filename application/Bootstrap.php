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
require_once('Doctrine' . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR . 'ClassLoader.php');

use \Doctrine\Common\ClassLoader;

/**
 * This class is the starting point for the Unplagged application and initalizes 
 * all base components.
 *
 * Please remember that all the '_init*' methods are called alphabetically, so make sure to explicitly bootstrap
 * all dependencies in methods that need to deviate from this order.
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap{

  /**
   * Makes sure that only authorized users can see certain parts of the application.
   * 
   * It's probably the best if this is called right up front, to make sure nobody can access
   * something accidentally somehow.
   */
  protected function _initAccessControl(){
    //we need the entity manager, so make sure this is created prior
    $this->bootstrap('doctrine');
    //make sure at least the guest user is set if nobody logged in yet
    $this->bootstrap('user');

    $registry = Zend_Registry::getInstance();

    //initalize the current users ACL
    $acl = new Unplagged_Acl($registry->user, $registry->entitymanager);
    $registry->acl = $acl;
    $accessControl = new Unplagged_AccessControl($acl, $registry->user);

    //make sure front controller is initalized, so that we can register the authorization plugin
    $this->bootstrap('FrontController');
    $frontController = $this->getResource('FrontController');
    $frontController->registerPlugin($accessControl);
  }

  /**
   * Stores the current user in the registry.
   * 
   * If no user is logged in, the guest user is set as a default.
   */
  protected function _initUser(){
    $registry = Zend_Registry::getInstance();
    $defaultNamespace = new Zend_Session_Namespace('Default');

    if(!$defaultNamespace->userId || $defaultNamespace->userId === 'guest'){
      $guestId = $registry->entitymanager->getRepository('Application_Model_Setting')->findOneBySettingKey('guest-id');
      $guest = $registry->entitymanager->getRepository('Application_Model_User')->findOneById($guestId->getValue());

      $caseId = $defaultNamespace->case;
      if($caseId){
        $case = $registry->entitymanager->getRepository('Application_Model_Case')->findOneById($caseId);
        $guest->setCurrentCase($case);
      }

      $registry->user = $guest;
      $defaultNamespace->userId = 'guest';
    }else{
      $registry->user = $registry->entitymanager->getRepository('Application_Model_User')->findOneById($defaultNamespace->userId);
    }

    //if we don't have a user something went wrong with the session, so clear everything and force new login
    if(!$registry->user){
      session_unset();
      header('Location: ');
    }
  }

  /**
   * Sets the logger in the registry, so that it can be called via:
   * 
   * <code>Zend_Registry::get('Log')->crit('Critical');</code>
   * 
   * The settings are provided inside the log.ini file.
   * 
   * This function can not be named _initLog, because this creates a conflict with Zend.
   */
  protected function _initLogger(){
    $log = null;

    //enable the logs as provided in the log.ini
    if($this->hasPluginResource('log')){
      $this->bootstrap('log');
      $log = $this->getResource('log');
    }else{
      //if no logger was provided within the config, stub it out so no errors get thrown when logging is called
      $writer = new Zend_Log_Writer_Null;
      $log = new Zend_Log($writer);
    }
    Zend_Registry::getInstance()->Log = $log;
    //Zend_Registry::get('Log')->err('Hi');
  }

  /**
   * 
   */
  protected function _initNavigation(){
    $config = new Zend_Config_Xml(BASE_PATH . '/data/navigation.xml', 'nav');
    $container = new Zend_Navigation($config);

    //show case files if a case is selected
    $user = Zend_Registry::get('user');
    if($user->getCurrentCase()){
      $caseFiles = $container->findOneBy('title', 'Case Files');
      $caseFiles->setVisible();
    }

    $this->bootstrap('layout');
    $layout = $this->getResource('layout');
    $view = $layout->getView();
    $registry = Zend_Registry::getInstance();
    $view->navigation($container)->setAcl($registry->acl)->setRole($registry->user->getRole());

    Zend_Registry::set('Zend_Navigation', $container);
  }

}
