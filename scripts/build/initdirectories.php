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

defined('BASE_PATH')
    || define('BASE_PATH', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR));

//add directories that need to be created here
//make sure to include them in the right order, so that dependencies occur beforehand
//i. e. /data -> /data/uploads
$directories = array(
  'application' . DIRECTORY_SEPARATOR . 'storage', //@deprecated
  'application' . DIRECTORY_SEPARATOR . 'files', //@deprecated
  'data',
  'data' . DIRECTORY_SEPARATOR . 'uploads',
  'data' . DIRECTORY_SEPARATOR . 'logs',
  'data' . DIRECTORY_SEPARATOR . 'cache',
  'temp',
  'temp' . DIRECTORY_SEPARATOR . 'ocr',
  'temp' . DIRECTORY_SEPARATOR . 'imagemagick',
  'data' . DIRECTORY_SEPARATOR . 'doctrine',
  'data' . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR . 'proxies'
);

echo 'Creating directories' . PHP_EOL;

foreach($directories as $directory){
  echo 'The creation of ' . $directory . ' ';
  $result = createDirectory($directory) ? 'succeded' : 'failed';
  echo $result . PHP_EOL;
}

/**
 * Creates the given directory if it didn't exist and sets the permissions to 755.
 * 
 * @param string $directory
 * @return bool A boolean indicating whether the directory was created. False probably just means, that the directory
 * already existed.
 */
function createDirectory($directory){
  $fullPath = BASE_PATH . DIRECTORY_SEPARATOR . $directory;
  if(!is_dir($fullPath)){
    mkdir($fullPath);
    chmod($fullPath, 0755);

    return true;
  }

  return false;
}

?>