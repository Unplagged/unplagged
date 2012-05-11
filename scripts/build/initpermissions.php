<?php

/*
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
 * 
 * 
 * This file searches the controller directory for all actions and stores
 * them in the permissions table as $controller_$action.
 * 
 * @todo when running this script on the command line, there is a weird 
 * output of some <script> tag, it works so not that important right now,
 * but I'm curious where this could come from
 */

include 'initbase.php';

//$application should have been set in initbase
$application->getBootstrap()->bootstrap('doctrine');
$em = $application->getBootstrap()->getResource('doctrine');
$em->flush();

$application->getBootstrap()->bootstrap('FrontController');
$front = $application->getBootstrap()->getResource('FrontController');

$basicResources = array();

//first permission is allow everything
$allowAll = $em->getRepository('Application_Model_Permission')->findOneByName('*');
if(!$allowAll){
  $allowAll = new Application_Model_Permission('*');
  $em->persist($allowAll);
}

//find all controllers and include them; we currently have no modules, 
//if we have later on, this probably needs to be included into the 
//permission name
foreach($front->getControllerDirectory() as $module=>$path){
  recursiveDirectories($path);
}

/**
 * Include all *Controller.php files from the given path and it's subdirectories.
 * @param string $path 
 */
function recursiveDirectories($path){
  $content = scandir($path);
  foreach($content as $directoryContent){
    if($directoryContent !== '..' && $directoryContent !== '.' && is_dir($path . DIRECTORY_SEPARATOR . $directoryContent)){
      recursiveDirectories($path . DIRECTORY_SEPARATOR . $directoryContent);
    }else{
      if(strstr($path . DIRECTORY_SEPARATOR . $directoryContent, 'Controller.php') !== false){
        include_once $path . DIRECTORY_SEPARATOR . $directoryContent;
      }
    }
  }
}

//get the actions of every included controller to store them in the db
foreach(get_declared_classes() as $class){
  if(is_subclass_of($class, 'Zend_Controller_Action')){

    $controller = strtolower(substr($class, 0, strpos($class, 'Controller')));

    foreach(get_class_methods($class) as $action){

      if(strstr($action, 'Action') !== false){
        $actionWithHyphens = preg_replace_callback('/([A-Z])/', create_function('$matches', 'return \'-\' . strtolower($matches[1]);'), substr($action, 0, -6));
        $basicResources[] = $controller . '_' . $actionWithHyphens;
      }
    }
  }
}

//store all found resources in the db
foreach($basicResources as $resource){
  $permission = $em->getRepository('Application_Model_Permission')->findOneByName($resource);
  if(empty($permission)){
    $permission = new Application_Model_Permission($resource, 'action');
    $em->persist($permission);
  }
}
//make sure the permission are already in the db, so we can retrieve some
$em->flush();

//create the guest users role
$element = $em->getRepository('Application_Model_User_Role')->findOneByRoleId('guest');
if(empty($element)){
  $guestRole = new Application_Model_User_Role(Application_Model_User_Role::TYPE_GLOBAL);
  $guestRole->setRoleId('guest');

  $defaultPermissions = array(
    'auth_login',
    'auth_logout',
    'index_index',
    'error_error',
    'case_list',
    'file_list',
    'user_register',
    'user_verify',
    'user_recover-password',
    'document_response-plagiarism'
  );

  foreach($defaultPermissions as $permissionName){
    $permission = $em->getRepository('Application_Model_Permission')->findOneByName($permissionName);

    if($permission){
      $guestRole->addPermission($permission);
    }
  }
  $em->persist($guestRole);
  //flush here to have access to the id
  $em->flush();

  $guestSetting = $em->getRepository('Application_Model_Setting')->findOneBySettingKey('guest-id');
  if(!$guestSetting){
    $guestSetting = new Application_Model_Setting();
    $guestSetting->setSettingKey('guest-role-id');
  }
  $guestSetting->setValue($guestRole->getId());

  $em->persist($guestSetting);
}

//create the admin user role
$element = $em->getRepository('Application_Model_User_Role')->findOneByRoleId('admin');
if(!$element){
  $adminRole = new Application_Model_User_InheritableRole(Application_Model_User_Role::TYPE_GLOBAL);
  $adminRole->setRoleId('admin');

  $adminRole->addPermission($allowAll);

  $em->persist($adminRole);
  //flush here to have access to the id
  $em->flush();

  $adminSetting = $em->getRepository('Application_Model_Setting')->findOneBySettingKey('admin-role-id');
  if(!$adminSetting){
    $adminSetting = new Application_Model_Setting();
    $adminSetting->setSettingKey('admin-role-id');
  }
  $adminSetting->setValue($adminRole->getId());

  $em->persist($adminSetting);
}

$em->flush();
?>