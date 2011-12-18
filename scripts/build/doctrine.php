<?php
//set only as development environment, when nothing was defined before
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
 echo APPLICATION_PATH;
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once ('Zend/Application.php');
 
// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
 
$application->getBootstrap()->bootstrap('doctrine');
$em = $application->getBootstrap()->getResource('doctrine');

$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
$metadata = $em->getMetadataFactory()->getAllMetadata();
$schemaTool->updateSchema($metadata);
