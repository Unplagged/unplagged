<?php
//set only as development environment, when nothing was defined before
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

/**
 * @const BASE_PATH The path to the application directory.
 */
defined('BASE_PATH')
    || define('BASE_PATH', realpath(dirname(__FILE__) . '/../../'));

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


// 1) init notification actions
unset($data);
$data["name"] = "user_registered";
$data["description"] = "A user registerd on the plattform.";
$notificationAction = new Application_Model_Notification_Action($data);
$em->persist($notificationAction);

unset($data);
$data["name"] = "user_updated_profile";
$data["description"] = "A user updated the own account.";
$notificationAction = new Application_Model_Notification_Action($data);
$em->persist($notificationAction);

unset($data);
$data["name"] = "user_verified";
$data["description"] = "A user verified the own account.";
$notificationAction = new Application_Model_Notification_Action($data);
$em->persist($notificationAction);

unset($data);
$data["name"] = "user_requested_password";
$data["description"] = "A user requested the own password.";
$notificationAction = new Application_Model_Notification_Action($data);
$em->persist($notificationAction);

unset($data);
$data["name"] = "case_created";
$data["description"] = "A case was created.";
$notificationAction = new Application_Model_Notification_Action($data);
$em->persist($notificationAction);

unset($data);
$data["name"] = "file_uploaded";
$data["description"] = "A file was uploaded.";
$notificationAction = new Application_Model_Notification_Action($data);
$em->persist($notificationAction);

$em->flush();


// 3) init user states
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