<?php

/**
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
 *
 *
 * This file sets common constants and the include path.
 */

define('BASE_PATH', __DIR__);

/**
 * @const TEMP_PATH The path to the directory where temporary data should be stored.
 */
defined('DATA_PATH')
        || define('TEMP_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'data');

/**
 * @const WEBROOT_PATH The webroot directory.
 */
defined('WEBROOT_PATH')
        || define('WEBROOT_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'public');

// Composer autoloading
if (is_readable(__DIR__ . '/vendor/autoload.php')) {
  include __DIR__ . '/vendor/autoload.php';
}else{
  die('It seems like the necessary libraries are not installed, did you forget to run Composer?');
}

/**
 * Finds out if we have a request uri set, which indicates that it is no call from the console
 * and checks whether it contains the /installer path, which indicates that we are currently 
 * installing.
 * 
 * @return boolean
 */
function isInstallingOrConsole(){
  if(isset($_SERVER['REQUEST_URI'])){
    return strpos($_SERVER["REQUEST_URI"], '/installer') === 0;
  }
  return true;
}