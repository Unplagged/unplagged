<?php
/**
 * This file is the main entry point into the Unplagged application.
 *
 * It defines some environment variables and initiates the bootstrapping process.
 */

/**
 * @const BASE_PATH The path to the application directory.
 */
defined('BASE_PATH')
    || define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));

/**
 * @const APPLICATION_PATH The path to the application directory.
 */
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'application');


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
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));


require_once BASE_PATH . '/scripts/build/Installer/Installer.php';

$installer = new Installer();
if($installer->isInstalled()){
  // Ensure library folder is on the include_path
  set_include_path(implode(PATH_SEPARATOR, array(
      realpath(APPLICATION_PATH . '/../library'),
      get_include_path(),
  )));
  
  require_once 'Zend/Application.php';
  
  //Create application, bootstrap, and run
  $application = new Zend_Application(APPLICATION_ENV, array(
    'config'=>array(
      APPLICATION_PATH . '/configs/application.ini',
      APPLICATION_PATH . '/configs/unplagged-config.ini'
    )
  ));
  $application->bootstrap();
  $application->run();
} else {
  $installer->install();
}