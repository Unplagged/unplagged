<?php
//@todo this file was moved to scripts/build can be removed after a brief period of overlap

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
$data["module"] = "user";
$data["title"] = "registration";
$data["description"] = "A user registers on the plattform.";
$logAction = new Application_Model_Log_Action($data);
$em->persist($logAction);

unset($data);
$data["module"] = "user";
$data["title"] = "profile_update";
$data["description"] = "A user updates the own account.";
$logAction = new Application_Model_Log_Action($data);
$em->persist($logAction);

unset($data);
$data["module"] = "user";
$data["title"] = "verification";
$data["description"] = "A user verifies the own account.";
$logAction = new Application_Model_Log_Action($data);
$em->persist($logAction);

unset($data);
$data["module"] = "mailer";
$data["title"] = "registraion";
$data["description"] = "A mail was sent to the user asking for verifying the account.";
$logAction = new Application_Model_Log_Action($data);
$em->persist($logAction);

$em->flush();


// init user states
unset($data);
$data["title"] = "registered";
$data["description"] = "A user registered on the page and did not finish the verification process yet.";
$userState = new Application_Model_User_State($data);
$em->persist($userState);

unset($data);
$data["title"] = "activated";
$data["description"] = "A user that can actually use the web page.";
$userState = new Application_Model_User_State($data);
$em->persist($userState);

unset($data);
$data["title"] = "locked";
$data["description"] = "A user that was locked.";
$userState = new Application_Model_User_State($data);
$em->persist($userState);

$em->flush();