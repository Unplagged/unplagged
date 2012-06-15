<?php

$time = microtime(true);
$memory = memory_get_usage();

$arguments = getopt("e:");

defined('BASE_PATH')
    || define('BASE_PATH', realpath(dirname(__FILE__) . '/../../'));

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

/**
 * @const TEMP_PATH The path to the directory where temporary data should be stored.
 */
defined('TEMP_PATH')
    || define('TEMP_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'temp');

/**
 * @const APPLICATION_ENV The application environment, from which the config values are taken. Defaults to the most 
 * secure environment 'production', but only if nothing has been set before, e. g. by defininig it in the vhost.conf or 
 * some similar meachanism.
 */
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (isset($arguments["e"]) ? $arguments["e"] : 'production'));

require_once BASE_PATH . '/scripts/build/Installer/Installer.php';

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
