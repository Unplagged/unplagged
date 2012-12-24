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
 * This file sets common constants and the include path. It also provides a method to
 * bootstrap the ZEND application.
 *
 * IMPORTANT: If you want set a different value for the APPLICATION_ENV constant, this
 * to happen before this file gets included.
 */

define('BASE_PATH', realpath('.' . DIRECTORY_SEPARATOR));

/**
 * @const APPLICATION_PATH The path to the main source directory.
 */
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'application');

/**
 * @const TEMP_PATH The path to the directory where temporary data should be stored.
 */
defined('TEMP_PATH')
        || define('TEMP_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'temp');

/**
 * @const WEBROOT_PATH The webroot directory.
 */
defined('WEBROOT_PATH')
        || define('WEBROOT_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'public');

/**
 * @const LIBRARY_PATH The directory of all the library files.
 */
defined('LIBRARY_PATH')
        || define('LIBRARY_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'library');

/**
 * @const BUILD_PATH The directory of all the build files.
 */
defined('BUILD_PATH')
        || define('BUILD_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'build');

// Composer autoloading
if (is_readable(__DIR__ . '/vendor/autoload.php')) {
  include __DIR__ . '/vendor/autoload.php';
}