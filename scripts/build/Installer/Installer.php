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
 */

defined('BASE_PATH')
    || define('BASE_PATH', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR));

/**
 * Installs all necessary components of the Unplagged application.
 */
class Installer{

  private $configFilePath = '';

  public function __construct($configFilePath = ''){
    $this->configFilePath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'unplagged-config.ini';
  }

  /**
   * Checks all necessary indicators for whether the system is installed successfully.
   * 
   * @return boolean
   */
  public function isInstalled(){
    if($this->configExists()){
      return true;
    }

    return false;
  }
  
  /**
   * Checks whether the "unplagged-config.ini" exists as an indicator for whether the application is 
   * installed or not.
   * 
   * @return boolean
   */
  private function configExists(){
    if(file_exists($this->configFilePath)){
      return true;
    }

    return false;
  }

  /**
   * @todo This should have a nice webinterface which asks the user for all necessary values for the config file. 
   */
  public function install(){
    include_once 'header.php';
    echo '<pre class="console">';
    $this->initDirectories();
    echo '</pre>';

    echo '<h1>Not installed</h1>';
    echo '<p style="width:450px;">The application is not installed. In order to install it, please create ' . $this->configFilePath . ' manually and fill in alle necessary values as provided in the unplagged-config-sample.ini.</p>';
    include_once 'footer.php';
  }

  /**
   * Runs all necessary installation processes, but only when the config file with the necessary preconditions exist.
   */
  public function installCli(){
    if($this->configExists()){
      echo 'Starting the installation of Unplagged...' . PHP_EOL;
      try{
        $this->initDirectories();
        echo 'The installation of Unplagged finished.' . PHP_EOL;
      } catch(Exception $e){
        
      }
    } else {
      echo 'In order to install from the command line, you need provide a valid config file in: ' . $this->configFilePath . PHP_EOL;
    }
  }
  
  /**
   * Creates all necessary directories 
   */
  private function initDirectories(){
    //add directories that need to be created here
    //make sure to include them in the right order, so that dependencies occur beforehand
    //i. e. /data -> /data/uploads
    $directories = array(
      'data',
      'data' . DIRECTORY_SEPARATOR . 'uploads',
      'data' . DIRECTORY_SEPARATOR . 'logs',
      'data' . DIRECTORY_SEPARATOR . 'cache',
      'data' . DIRECTORY_SEPARATOR . 'reports',
      'data' . DIRECTORY_SEPARATOR . 'doctrine',
      'data' . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR . 'proxies',
      'temp',
      'temp' . DIRECTORY_SEPARATOR . 'ocr',
      'temp' . DIRECTORY_SEPARATOR . 'imagemagick',
      'data' . DIRECTORY_SEPARATOR . 'avatars'
    );

    echo 'Starting creation of directories...' . PHP_EOL;

    foreach($directories as $directory){
      $fullPath = BASE_PATH . DIRECTORY_SEPARATOR . $directory;
      if($this->createDirectory($fullPath)){
        echo 'The creation of ' . $fullPath . ' succeded.' . PHP_EOL;
      }else{
        if(!is_dir($fullPath)){
          echo 'An error occured while creating ' . $fullPath . PHP_EOL;
        }
      }
    }
    echo 'Finished creation of directories.' . PHP_EOL;
  }

  /**
   * Creates the given directory if it didn't exist and sets the Linux permissions to 777.
   * 
   * @param string $directory The full path of the directory to create.
   * @return bool A boolean indicating whether the directory was created. False probably just means, that the directory
   * already existed, but could also mean that no write access was there.
   * 
   * @todo check if 0777 is really necessary
   */
  function createDirectory($directory){
    if(!is_dir($directory)){
      mkdir($directory);

      chmod($directory, 0777);
      return true;
    }

    //use chmod even if the directory already existed, to make sure the directory can be accessed later on
    chmod($directory, 0777);

    return false;
  }

}
?>