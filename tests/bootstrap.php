<?php
error_reporting(E_ALL);

defined('BASE_PATH')
    || define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));

defined('TEMP_PATH')
    || define('TEMP_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'temp');

// Define path to application directory
defined('APPLICATION_PATH') 
     || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));


// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'ControllerTestCase.php';
?>
                         