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
namespace UnpInstaller\Controller;

use UnpCommon\Controller\BaseController;
use UnpInstaller\Installer;

/**
 * Controller to serve the index and other mostly static pages.
 */
class InstallerController extends BaseController{

  private $configFilePath = '';

  public function setConfigFilePath($configFilePath){
    $this->configFilePath = $configFilePath;
  }

  /**
   * Allow the installer when we have no flag set or when we have a flag that explicitly allows the
   * installer. This makes it possible to access the installer even if it already ran.
   * 
   * @param array $config
   * @return boolean
   */
  private function installerEnabled($config){
    if((isset($config['unp_settings']['installer_enabled']) &&
            $config['unp_settings']['installer_enabled'] === true) ||
            !isset($config['unp_settings']['installer_enabled'])){
      return true;
    }
    return false;
  }

  private function createInstaller(){
    $installer = new Installer(BASE_PATH, $this->getServiceLocator()->get('translator'), $this->flashMessenger());

    return $installer;
  }

  /**
   * Sends the webinstaller app to the user.
   * 
   * The actual instalation is done via js and websockets from the install action.
   */
  public function indexAction(){
    if(!$this->installerEnabled($this->getServiceLocator()->get('Config'))){
      $response = $this->redirect()->toUrl('/');
      $response->setStatusCode(403);
    } else {
      $this->layout('layout/installer');
    }
  }

  public function installAction(){
    die('hier');
    $post = $this->params()->fromPost();

    $installer = $this->createInstaller();
    
    
    if(!empty($post)){
      /* $data = $this->validateInputData();

        $this->initDirectories();
        $this->checkWritePermissions();
        $this->checkConsoleCommands($data);
        if($this->checkDatabaseParams($data)){
        if($this->createConfig($data)){
        $this->initDatabase();
        $this->createAdmin($data);
        }
        }

        $this->parseResponse(); */
    }
  }

}
