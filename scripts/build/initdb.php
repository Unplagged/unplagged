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

unset($data);
$data["name"] = "page_simtext";
$data["description"] = "Create a simtext report for a single page";
$action = new Application_Model_Action($data);
$em->persist($action);


// 1) init notification actions
unset($data);
$data["name"] = "user_registered";
$data["description"] = "A user registerd on the plattform.";
$notificationAction = new Application_Model_Action($data);
$em->persist($notificationAction);

unset($data);
$data["name"] = "user_updated_profile";
$data["description"] = "A user updated the own account.";
$notificationAction = new Application_Model_Action($data);
$em->persist($notificationAction);

unset($data);
$data["name"] = "user_verified";
$data["description"] = "A user verified the own account.";
$notificationAction = new Application_Model_Action($data);
$em->persist($notificationAction);

unset($data);
$data["name"] = "user_requested_password";
$data["description"] = "A user requested the own password.";
$notificationAction = new Application_Model_Action($data);
$em->persist($notificationAction);

unset($data);
$data["name"] = "case_created";
$data["description"] = "A case was created.";
$notificationAction = new Application_Model_Action($data);
$em->persist($notificationAction);

unset($data);
$data["name"] = "file_uploaded";
$data["description"] = "A file was uploaded.";
$notificationAction = new Application_Model_Action($data);
$em->persist($notificationAction);

unset($data);
$data["name"] = "fragment_created";
$data["description"] = "A new fragment was created.";
$notificationAction = new Application_Model_Action($data);
$em->persist($notificationAction);

$em->flush();

// 3) init user states
unset($data);
$data["name"] = "user_registered";
$data["title"] = "registered";
$data["description"] = "A user registered on the page and did not finish the verification process yet.";
$state = new Application_Model_State($data);
$em->persist($state);

unset($data);
$data["name"] = "user_activated";
$data["title"] = "activated";
$data["description"] = "A user that can actually use the web page.";
$state = new Application_Model_State($data);
$em->persist($state);

unset($data);
$data["name"] = "user_locked";
$data["title"] = "locked";
$data["description"] = "A user that was locked.";
$state = new Application_Model_State($data);
$em->persist($state);

$em->flush();

// 5) report states
unset($data);
$data["name"] = "report_running";
$data["title"] = "running";
$data["description"] = "The report is currently being generated.";
$state = new Application_Model_State($data);
$em->persist($state);

unset($data);
$data["name"] = "task_scheduled";
$data["title"] = "scheduled";
$data["description"] = "A is being scheduled, and will be generated asap.";
$state = new Application_Model_State($data);
$em->persist($state);

unset($data);
$data["name"] = "task_finished";
$data["title"] = "finished";
$data["description"] = "The task was finished.";
$state = new Application_Model_State($data);
$em->persist($state);

unset($data);
$data["name"] = "report_generated";
$data["title"] = "generated";
$data["description"] = "The report was generated successfully.";
$state = new Application_Model_State($data);
$em->persist($state);

unset($data);
$data["name"] = "report_error";
$data["title"] = "error";
$data["description"] = "There was an error, the report could not be generated.";
$state = new Application_Model_State($data);
$em->persist($state);

$em->flush();

// 4) fragment types
unset($data);
$data["name"] = "UnbekannteQuelle";
$data["description"] = "UnbekannteQuelle";
$fragmentType = new Application_Model_Document_Fragment_Type($data);
$em->persist($fragmentType);

unset($data);
$data["name"] = "KeinPlagiat";
$data["description"] = "KeinPlagiat";
$fragmentType = new Application_Model_Document_Fragment_Type($data);
$em->persist($fragmentType);

unset($data);
$data["name"] = "Verschleierung";
$data["description"] = "Verschleierung";
$fragmentType = new Application_Model_Document_Fragment_Type($data);
$em->persist($fragmentType);

unset($data);
$data["name"] = "HalbsatzFlickerei";
$data["description"] = "HalbsatzFlickerei";
$fragmentType = new Application_Model_Document_Fragment_Type($data);
$em->persist($fragmentType);

unset($data);
$data["name"] = "ShakeAndPaste";
$data["description"] = "ShakeAndPaste";
$fragmentType = new Application_Model_Document_Fragment_Type($data);
$em->persist($fragmentType);

unset($data);
$data["name"] = "ÜbersetzungsPlagiat";
$data["description"] = "ÜbersetzungsPlagiat";
$fragmentType = new Application_Model_Document_Fragment_Type($data);
$em->persist($fragmentType);

unset($data);
$data["name"] = "StrukturPlagiat";
$data["description"] = "StrukturPlagiat";
$fragmentType = new Application_Model_Document_Fragment_Type($data);
$em->persist($fragmentType);

unset($data);
$data["name"] = "BauernOpfer";
$data["description"] = "BauernOpfer";
$fragmentType = new Application_Model_Document_Fragment_Type($data);
$em->persist($fragmentType);

unset($data);
$data["name"] = "VerschärftesBauernOpfer";
$data["description"] = "VerschärftesBauernOpfer";
$fragmentType = new Application_Model_Document_Fragment_Type($data);
$em->persist($fragmentType);

$em->flush();
