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

/**
 * Installs all necessary components of the Unplagged application 
 */
class Installer {
  
  private static $configFilePath;
  
  public function __construct(){
    self::$configFilePath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'unplagged-config.ini';
  }
  
  /**
   * Checks whether the "unplagged-config.ini" exists as the indicator for whether the application is 
   * installed or not.
   * 
   * @return boolean
   */
  public function isInstalled(){
    if(file_exists(self::$configFilePath)){
      return true;  
    }
    
    return false;
  }
  
  public function install(){
    echo '<h1>Not installed</h1>';
    echo '<p style="width:450px;">The application is not installed. In order to install it, please create ' . self::$configFilePath . ' manually and fill in alle necessary values as provided in the unplagged-config-sample.ini.</p>';
    die();
  }
}
?>