<?php
/**
 * This file is the main entry point into the Unplagged application.
 *
 * It defines some environment variables and initiates the bootstrapping process.
 */


/**
 * @const APPLICATION_PATH The path to the application directory.
 */
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

/**
 * @const TEMP_PATH The path to the data directory.
 */
defined('TEMP_PATH')
    || define('TEMP_PATH', realpath(dirname(__FILE__) . '/../temp'));

/**
 * @const APPLICATION_ENV The application environment, from which the config values are taken. Defaults to the most 
 * secure environment 'production', but only if nothing has been set before, e. g. by defininig it in the vhost.conf or 
 * some similar meachanism.
 */
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library folder is on the include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();
$application->run();