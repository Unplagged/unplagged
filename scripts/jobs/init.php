<?php
//register current time and memory state in global variables to create benchmark after execution
$time = microtime(true);
$memory = memory_get_usage();
//register trace here so the developer can find out which cronjob had the problem
$trace = debug_backtrace();

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


// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
      realpath(APPLICATION_PATH . '/../library'),
      get_include_path(),
    )));

require_once ('Zend/Application.php');

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, array(
    'config'=>array(
      APPLICATION_PATH . '/configs/application.ini',
      APPLICATION_PATH . '/configs/log.ini',
      APPLICATION_PATH . '/configs/routes.ini',
      APPLICATION_PATH . '/configs/unplagged-config.ini'
    )
  ));
$application->bootstrap();

register_shutdown_function('benchmark');

/**
 * Calculates time and memory usage to provide benchmark data.
 * 
 * @global int $time
 * @global int $memory 
 */
function benchmark(){
  global $time, $memory, $trace;
  $endTime = microtime(true);
  $endMemory = memory_get_usage();
  
  //in kb
  $usedMemory = ($endMemory - $memory) / 1024;
  
  //log if the system uses more than 30MB
  if($usedMemory > 30000){
    Zend_Registry::get('Log')->crit('High memory usage in cronjob:' . PHP_EOL . print_r($trace, true));
  }
  echo 'Time [' . ($endTime - $time) . '] Memory [' . number_format(( $endMemory - $memory) / 1024) . 'MB]';
}
