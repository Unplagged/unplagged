<?php

$time = microtime(true);
$memory = memory_get_usage();

$arguments = getopt("e:");

defined('BASE_PATH')
    || define('BASE_PATH', realpath(dirname(__FILE__) . '/../../'));

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (isset($arguments["e"]) ? $arguments["e"] : 'development'));

/**
 * @const TEMP_PATH The path to the directory where temporary data should be stored.
 */
defined('TEMP_PATH')
    || define('TEMP_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'temp');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
      realpath(APPLICATION_PATH . '/../library'),
      get_include_path(),
    )));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, array(
    'config'=>array(
      APPLICATION_PATH . '/configs/application.ini',
      APPLICATION_PATH . '/configs/unplagged-config.ini'
    )
  ));
$application->bootstrap();

register_shutdown_function('__shutdown');

function __shutdown(){
  global $time, $memory;
  $endTime = microtime(true);
  $endMemory = memory_get_usage();

  echo 'Time [' . ($endTime - $time) . '] Memory [' . number_format(( $endMemory - $memory) / 1024) . 'Kb]';
}