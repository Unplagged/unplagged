<?php

define('APPLICATION_ENV', 'development');
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));
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

// init log actions
$logAction = new Application_Model_Log_Action();
$logAction->setModule("user");
$logAction->setTitle("registration");
$logAction->setDescription("A user registers on the plattform.");
$em->persist($logAction);

$logAction = new Application_Model_Log_Action();
$logAction->setModule("user");
$logAction->setTitle("profile_update");
$logAction->setDescription("A user updates the own account.");
$em->persist($logAction);

$logAction = new Application_Model_Log_Action();
$logAction->setModule("user");
$logAction->setTitle("verification");
$logAction->setDescription("A user verifies the own account.");
$em->persist($logAction);

$logAction = new Application_Model_Log_Action();
$logAction->setModule("mailer");
$logAction->setTitle("registraion");
$logAction->setDescription("A mail was sent to the user asking for verifying the account.");
$em->persist($logAction);

$em->flush();

// init user states
$userState = new Application_Model_User_State();
$userState->setTitle("registered");
$userState->setDescription("A user registered on the page and did not finish the verification process yet.");
$em->persist($userState);

$userState = new Application_Model_User_State();
$userState->setTitle("activated");
$userState->setDescription("A user that can actually use the web page.");
$em->persist($userState);

$userState = new Application_Model_User_State();
$userState->setTitle("locked");
$userState->setDescription("A user that was locked.");
$em->persist($userState);

$em->flush();