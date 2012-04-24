<?php

$arguments = getopt("e:");

//set only as development environment, when nothing was defined before
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (isset($arguments["e"]) ? $arguments["e"] : 'development'));

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
$em->flush();

// 1) init notification actions
$element = $em->getRepository('Application_Model_Action')->findOneByName("user_registered");
if(empty($element)){
  unset($data);
  $data["name"] = "user_registered";
  $data["description"] = "A user registerd on the plattform.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
$element = $em->getRepository('Application_Model_Action')->findOneByName("user_updated_profile");
if(empty($element)){
  unset($data);
  $data["name"] = "user_updated_profile";
  $data["description"] = "A user updated the own account.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
$element = $em->getRepository('Application_Model_Action')->findOneByName("user_verified");
if(empty($element)){
  unset($data);
  $data["name"] = "user_verified";
  $data["description"] = "A user verified the own account.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
$element = $em->getRepository('Application_Model_Action')->findOneByName("user_requested_password");
if(empty($element)){
  unset($data);
  $data["name"] = "user_requested_password";
  $data["description"] = "A user requested the own password.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
$element = $em->getRepository('Application_Model_Action')->findOneByName("case_created");
if(empty($element)){
  unset($data);
  $data["name"] = "case_created";
  $data["description"] = "A case was created.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
$element = $em->getRepository('Application_Model_Action')->findOneByName("file_uploaded");
if(empty($element)){
  unset($data);
  $data["name"] = "file_uploaded";
  $data["description"] = "A file was uploaded.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
$element = $em->getRepository('Application_Model_Action')->findOneByName("fragment_created");
if(empty($element)){
  unset($data);
  $data["name"] = "fragment_created";
  $data["description"] = "A new fragment was created.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}

// 3) init user states
$element = $em->getRepository('Application_Model_State')->findOneByName("user_registered");
if(empty($element)){
  unset($data);
  $data["name"] = "user_registered";
  $data["title"] = "registered";
  $data["description"] = "A user registered on the page and did not finish the verification process yet.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
$element = $em->getRepository('Application_Model_State')->findOneByName("user_activated");
if(empty($element)){
  unset($data);
  $data["name"] = "user_activated";
  $data["title"] = "activated";
  $data["description"] = "A user that can actually use the web page.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}

$element = $em->getRepository('Application_Model_State')->findOneByName("user_locked");
if(empty($element)){
  unset($data);
  $data["name"] = "user_locked";
  $data["title"] = "locked";
  $data["description"] = "A user that was locked.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}

// 5) report states
$element = $em->getRepository('Application_Model_State')->findOneByName("report_running");
if(empty($element)){
  unset($data);
  $data["name"] = "report_running";
  $data["title"] = "running";
  $data["description"] = "The report is currently being generated.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}

$element = $em->getRepository('Application_Model_State')->findOneByName("task_scheduled");
if(empty($element)){
  unset($data);
  $data["name"] = "task_scheduled";
  $data["title"] = "scheduled";
  $data["description"] = "A is being scheduled, and will be generated asap.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}

$element = $em->getRepository('Application_Model_State')->findOneByName("task_finished");
if(empty($element)){
  unset($data);
  $data["name"] = "task_finished";
  $data["title"] = "finished";
  $data["description"] = "The task was finished.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}

$element = $em->getRepository('Application_Model_State')->findOneByName("report_generated");
if(empty($element)){
  unset($data);
  $data["name"] = "report_generated";
  $data["title"] = "generated";
  $data["description"] = "The report was generated successfully.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}

$element = $em->getRepository('Application_Model_State')->findOneByName("report_error");
if(empty($element)){
  unset($data);
  $data["name"] = "report_error";
  $data["title"] = "error";
  $data["description"] = "There was an error, the report could not be generated.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}

// 4) fragment types
$element = $em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("UnbekannteQuelle");
if(empty($element)){
  unset($data);
  $data["name"] = "UnbekannteQuelle";
  $data["description"] = "UnbekannteQuelle";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}

$element = $em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("KeinPlagiat");
if(empty($element)){
  unset($data);
  $data["name"] = "KeinPlagiat";
  $data["description"] = "KeinPlagiat";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}

$element = $em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("Verschleierung");
if(empty($element)){
  unset($data);
  $data["name"] = "Verschleierung";
  $data["description"] = "Verschleierung";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}

$element = $em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("HalbsatzFlickerei");
if(empty($element)){
  unset($data);
  $data["name"] = "HalbsatzFlickerei";
  $data["description"] = "HalbsatzFlickerei";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}

$element = $em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("ShakeAndPaste");
if(empty($element)){
  unset($data);
  $data["name"] = "ShakeAndPaste";
  $data["description"] = "ShakeAndPaste";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}

$element = $em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("ÜbersetzungsPlagiat");
if(empty($element)){
  unset($data);
  $data["name"] = "ÜbersetzungsPlagiat";
  $data["description"] = "ÜbersetzungsPlagiat";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}

$element = $em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("StrukturPlagiat");
if(empty($element)){
  unset($data);
  $data["name"] = "StrukturPlagiat";
  $data["description"] = "StrukturPlagiat";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}

$element = $em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("BauernOpfer");
if(empty($element)){
  unset($data);
  $data["name"] = "BauernOpfer";
  $data["description"] = "BauernOpfer";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}

$element = $em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("VerschaerftesBauernOpfer");
if(empty($element)){
  unset($data);
  $data["name"] = "VerschaerftesBauernOpfer";
  $data["description"] = "VerschärftesBauernOpfer";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}

$element = $em->getRepository('Application_Model_Action')->findOneByName("file_parse");
if(empty($element)){
  unset($data);
  $data["name"] = "file_parse";
  $data["description"] = "Parse a file into a document";
  $action = new Application_Model_Action($data);
  $em->persist($action);
}
$element = $em->getRepository('Application_Model_Action')->findOneByName("page_simtext");
if(empty($element)){
  unset($data);
  $data["name"] = "page_simtext";
  $data["description"] = "Create a simtext report for a single page";
  $action = new Application_Model_Action($data);
  $em->persist($action);
}

$element = $em->getRepository('Application_Model_State')->findOneByName("task_running");
if(empty($element)){
  unset($data);
  $data["name"] = "task_running";
  $data["title"] = "running";
  $data["description"] = "The task is currently being executed.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}

$em->flush();