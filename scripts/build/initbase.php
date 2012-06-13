<?php

/*
 * Unplagged - The plagiarism detection cockpit.
 * Copyright (C) 2012 Unplagged
 *  
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *  
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$arguments = getopt("e:");

/**
 * @todo could it lead to problems to have development as default? Can't think of anything
 * right now, but 'production' would feel more secure as a default even though it would be
 * inconvenient 
 */
//set only as development environment, when no parameter is present
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (isset($arguments["e"]) ? $arguments["e"] : 'development'));

defined('BASE_PATH')
    || define('BASE_PATH', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR));

defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'application'));

set_include_path(implode(PATH_SEPARATOR, array(
      realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'library'),
      get_include_path(),
    )));

require_once ('Zend/Application.php');

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, array(
    'config'=>array(
      APPLICATION_PATH . '/configs/application.ini',
      APPLICATION_PATH . '/configs/unplagged-config.ini'
    )
  ));

//make sure doctrine was initialized, so we can get access to the db via the entity manager
$application->getBootstrap()->bootstrap('doctrine');
$em = $application->getBootstrap()->getResource('doctrine');
?>