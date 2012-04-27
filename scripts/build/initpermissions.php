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

//find all controllers and include them; we currently have no modules, 
//if we have later on, this probably needs to be included into the 
//permission name
foreach($front->getControllerDirectory() as $module=>$path){
  foreach(scandir($path) as $file){

    if(strstr($file, "Controller.php") !== false){
      include_once $path . DIRECTORY_SEPARATOR . $file;
    }
  }
}

//get the actions of every included controller to store them in the db
foreach(get_declared_classes() as $class){
  if(is_subclass_of($class, 'Zend_Controller_Action')){

    $controller = strtolower(substr($class, 0, strpos($class, "Controller")));

    foreach(get_class_methods($class) as $action){

      if(strstr($action, "Action") !== false){
        $actionWithHyphens = preg_replace_callback ('/([A-Z])/', create_function('$matches','return \'-\' . strtolower($matches[1]);'), substr($action, 0, -6));
        $basicResources[] = $controller . '_' . $actionWithHyphens;
      }
    }
  }
}

//store all found resources in the db
foreach($basicResources as $resource){
  $permission = $em->getRepository('Application_Model_Permission')->findOneByName($resource);
  if(empty($permission)){
    $permission = new Application_Model_Permission($resource);
    $em->persist($permission);
  }
}
$em->flush();
?>