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
 */

include 'initbase.php';

//$application should have been set in initbase
$application->getBootstrap()->bootstrap('doctrine');
$em = $application->getBootstrap()->getResource('doctrine');
$em->flush();

$application->getBootstrap()->bootstrap('FrontController');
$front = $application->getBootstrap()->getResource('FrontController');

$basicResources = array();
$modelResources = array();

//first permission is allow everything
$allowAll = $em->getRepository('Application_Model_Permission')->findOneBy(array('type'=>'global', 'action'=>'*'));
if(!$allowAll){
  $allowAll = new Application_Model_PagePermission('global', '*');
  $em->persist($allowAll);
}

$classesArr = array();

//find all controllers and include them; we currently have no modules, 
//if we have later on, this probably needs to be included into the 
//permission name
foreach($front->getControllerDirectory() as $module=>$path){
  recursiveDirectories($path, $classesArr);
  recursiveDirectories(str_replace('controllers', 'models', $path), $classesArr);
}


//get the actions of every included controller to store them in the db
foreach($classesArr as $class){
    $class = $class[0];
  if(strpos($class, 'Controller') > 0){
    $controller = strtolower(substr($class, 0, strpos($class, 'Controller')));
    foreach(get_class_methods($class) as $action){
      if(strstr($action, 'Action') !== false){
        $actionWithHyphens = preg_replace_callback('/([A-Z])/', create_function('$matches', 'return \'-\' . strtolower($matches[1]);'), substr($action, 0, -6));
        $basicResources[] = array($controller, $actionWithHyphens);
      }
    }
  }elseif(strpos($class, 'Application_Model_') !== false && $class == 'Application_Model_Base' || is_subclass_of($class, 'Application_Model_Base')){
    // Application_Model_Base_Document will get base-document
    $modelWithHyphens = strtolower(str_replace('_', '-', substr($class, strlen('Application_Model_'))));
    if(!in_array($modelWithHyphens, Application_Model_Base::$blacklist)){
      foreach(Application_Model_Base::$permissionTypes as $permissionType){
        $resource = array($modelWithHyphens, $permissionType);
        $modelResources[] = $resource;
      }
    }
  }
}

$pagePermissions = storeResources($basicResources, $em, 'page');
$modelPermissions = storeResources($modelResources, $em, 'model');

function storeResources(array $resources, $em, $type = 'model'){
  $permissions = array();
  foreach($resources as $resource){
    if($type == 'model'){
      $permission = $em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>$resource[0], 'action'=>$resource[1], 'base'=>null));
    }else{
      $permission = $em->getRepository('Application_Model_PagePermission')->findOneBy(array('type'=>$resource[0], 'action'=>$resource[1], 'base'=>null));
    }
    if(empty($permission)){
      if($type === 'model'){
        $permission = new Application_Model_ModelPermission($resource[0], $resource[1]);
      }else{
        $permission = new Application_Model_PagePermission($resource[0], $resource[1]);
      }
      $em->persist($permission);
    }
    $permissions[] = $permission;
  }

  return $permissions;
}

//create the permissions for the model level
//make sure the permission are already in the db, so we can retrieve some
$em->flush();

//create the guest users role
$guestRole = $em->getRepository('Application_Model_User_Role')->findOneByRoleId('guest');

if(!$guestRole){
  $guestRole = new Application_Model_User_Role(Application_Model_User_Role::TYPE_GLOBAL);
  $guestRole->setRoleId('guest');
  $defaultPagePermissions = array(
    array('auth', 'login'),
    array('auth', 'logout'),
    array('index', 'index'),
    array('error', 'error'),
    array('user', 'register'),
    array('user', 'verify'),
    array('user', 'recover-password'),
    array('document', 'response-plagiarism'),
    array('user', 'set-current-case'),
    array('index', 'imprint'),
    array('user', 'activate-user')
  );

  foreach($defaultPagePermissions as $permissionName){
    $permission = $em->getRepository('Application_Model_PagePermission')->findOneBy(array('type'=>$permissionName[0], 'action'=>$permissionName[1]));

    if($permission){
      $guestRole->addPermission($permission);
    }
  }
  $em->persist($guestRole);
}

//create the default users role
$userRole = $em->getRepository('Application_Model_User_Role')->findOneByRoleId('user');

if(!$userRole){
  $userRole = new Application_Model_User_Role(Application_Model_User_Role::TYPE_GLOBAL);
  $userRole->setRoleId('user');
  $defaultPagePermissions = array(
    array('admin', 'index'),
    array('auth', 'login'),
    array('auth', 'logout'),
    array('case', 'get-roles'),
    array('case', 'add-file'),
    array('case', 'files'),
    array('case', 'list'),
    array('case', 'edit'),
    array('case', 'publish'),
    array('case', 'create'),
    array('comment', 'list'),
    array('comment', 'create'),
    array('document_fragment', 'rate'),
    array('document_fragment', 'delete'),
    array('document_fragment', 'changelog'),
    array('document_fragment', 'list'),
    array('document_fragment', 'edit'),
    array('document_fragment', 'create'),
    array('document_fragment', 'show'),
    array('document_fragment', 'rate-fragment'),
    array('document_page', 'compare'),
    array('document_page', 'fragment'),
    array('document_page', 'read'),
    array('document_page', 'changelog'),
    array('document_page', 'list-simtextreports'),
    array('document_page', 'create-simtextreport'),
    array('document_page', 'stopwords'),
    array('document_page', 'delete'),
    array('document_page', 'de-hyphen'),
    array('document_page', 'show'),
    array('document_page', 'detection-reports'),
    array('document_page', 'list'),
    array('document_page', 'edit'),
    array('document_page', 'create'),
    array('document', 'unset-target'),
    array('document', 'set-target'),
    array('document', 'response-plagiarism'),
    array('document', 'detect-plagiarism'),
    array('document', 'delete'),
    array('document', 'list'),
    array('document', 'edit'),
    array('document', 'read'),
    array('document', 'create'),
    array('file', 'delete'),
    array('file', 'parse'),
    array('file', 'download'),
    array('file', 'list'),
    array('file', 'upload'),
    array('index', 'index'),
    array('error', 'error'),
    array('user', 'register'),
    array('user', 'verify'),
    array('user', 'recover-password'),
    array('user', 'autocomplete'),
    array('user', 'add-file'),
    array('user', 'files'),
    array('user', 'edit'),
    array('user', 'remove-account'),
    array('user', 'set-current-case'),
    array('notification', 'conversation'),
    array('notification', 'comments'),
    array('notification', 'recent-activity'),
    array('permission', 'edit'),
    array('permission', 'autocomplete'),
    array('permission', 'list'),
    array('permission', 'edit-role'),
    array('rating', 'list'),
    array('rating', 'edit'),
    array('rating', 'create'),
    array('report', 'download'),
    array('report', 'create'),
    array('report', 'index'),
    array('report', 'list'),
    array('simtext', 'ajax'),
    array('simtext', 'download-report'),
    array('simtext', 'create-report'),
    array('simtext', 'delete-report'),
    array('simtext', 'list-reports'),
    array('simtext', 'show-report'),
    array('simtext', 'compare'),   
    array('image', 'show'),
    array('tag', 'autocomplete'),
    array('index', 'imprint'),
        array('bibtex', 'show'),
    array('bibtex', 'list')
  );

  foreach($defaultPagePermissions as $permissionName){
    $permission = $em->getRepository('Application_Model_PagePermission')->findOneBy(array('type'=>$permissionName[0], 'action'=>$permissionName[1], 'base'=>null));

    if($permission){
      $userRole->addPermission($permission);
    }
  }
  $em->persist($userRole);
}

function randomString($length){
  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789$./";

  $size = strlen($chars);
  $str = '';
  for($i = 0; $i < $length; $i++){
    $str .= $chars[rand(0, $size - 1)];
  }

  return $str;
}

//create the guest user object
$guestUser = $em->getRepository('Application_Model_User')->findOneByUsername('guest');
if(!$guestUser){
  $guestUser = new Application_Model_User(array(
        'role'=>$guestRole,
        'username'=>'guest',
        //essentially we won't need a password here, because everyone already got all the guest permission
        //but it's required by the class and probably better anyways to avoid some unforeseeable results
        //so we just set it here to some random string, for which we wouldn't know the real password, as it's hashed
        'password'=>randomString(60),
        'email'=>'',
        'verificationHash'=>''
      ));
  $em->persist($guestUser);

  //flush here to have access to the id
  $em->flush();
  //die('hier');
  //write the guest id into the settings
  $guestUserSetting = $em->getRepository('Application_Model_Setting')->findOneBySettingKey('guest-id');
  if(!$guestUserSetting){
    $guestUserSetting = new Application_Model_Setting('guest-id');
  }
  $guestUserSetting->setValue($guestUser->getId());
  $em->persist($guestUserSetting);
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
    $adminSetting = new Application_Model_Setting('admin-role-id');
  }
  $adminSetting->setValue($adminRole->getId());

  $em->persist($adminSetting);
}

//create the default case roles
$caseAdmin = $em->getRepository('Application_Model_User_Role')->findOneBy(array('roleId'=>'case-admin', 'type'=>Application_Model_User_Role::TYPE_CASE_DEFAULT));
if(!$caseAdmin){
  $caseAdmin = new Application_Model_User_InheritableRole(Application_Model_User_Role::TYPE_CASE_DEFAULT);
  $caseAdmin->setRoleId('case-admin');

  foreach($modelPermissions as $modelPermission){
    if($modelPermission->getType() != 'user') 
      $caseAdmin->addPermission($modelPermission);
  }

  $em->persist($caseAdmin);
}

//create the default case roles
$caseCollaborator = $em->getRepository('Application_Model_User_Role')->findOneBy(array('roleId'=>'case-collaborator', 'type'=>Application_Model_User_Role::TYPE_CASE_DEFAULT));
if(!$caseCollaborator){
  $caseCollaborator = new Application_Model_User_InheritableRole(Application_Model_User_Role::TYPE_CASE_DEFAULT);
  $caseCollaborator->setRoleId('case-collaborator');

  foreach($modelPermissions as $modelPermission){
    if($modelPermission->getType() != 'user') 
      $caseCollaborator->addPermission($modelPermission);
  }

  $em->persist($caseCollaborator);
}


$em->flush();

// some helper stuff for loading the classes needed
function file_get_php_classes($filepath){
  $php_code = file_get_contents($filepath);
  $classes = get_php_classes($php_code);

  return $classes;
}

function get_php_classes($php_code){
  $classes = array();

  $tokens = token_get_all($php_code);

  $count = count($tokens);
  for($i = 2; $i < $count; $i++){
    if($tokens[$i - 2][0] == T_CLASS
        && $tokens[$i - 1][0] == T_WHITESPACE
        && $tokens[$i][0] == T_STRING){

      $class_name = (String) $tokens[$i][1];
      $classes[] = $class_name;
    }
  }
  return $classes;
}

/**
 * Include all *Controller.php files from the given path and it's subdirectories.
 * @param string $path 
 */
function recursiveDirectories($path, &$classesArr){
  $content = scandir($path);
  foreach($content as $directoryContent){
    if($directoryContent !== '..' && $directoryContent !== '.' && is_dir($path . DIRECTORY_SEPARATOR . $directoryContent)){
      recursiveDirectories($path . DIRECTORY_SEPARATOR . $directoryContent, $classesArr);
    }else{
      $filePath = $path . DIRECTORY_SEPARATOR . $directoryContent;
      if(strstr($filePath, '.php')){
        require_once($filePath);
        array_push($classesArr, file_get_php_classes($filePath));
      }
    }
  }
}

?>