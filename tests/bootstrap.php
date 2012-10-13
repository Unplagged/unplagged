<?php
define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
require_once BASE_PATH . DIRECTORY_SEPARATOR . 'initApplication.php';

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', 'testing');

require_once 'ControllerTestCase.php';                   