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
use UnpInstaller\InstallerAware;
use Zend\Json\Json;

/**
 * Controller to serve the webinstaller.
 */
class InstallerController extends BaseController implements InstallerAware{

  private $configFilePath = '';
  private $installer = null;

  public function setConfigFilePath($configFilePath){
    $this->configFilePath = $configFilePath;
  }
  
  public function setInstaller(Installer $installer){
    $this->installer = $installer;
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

  /**
   * Sends the webinstaller app to the user.
   * 
   * The actual instalation is done via js from the install action.
   */
  public function indexAction(){
    $config = $this->getServiceLocator()->get('Config');

    if(!$this->installerEnabled($config)){
      $response = $this->redirect()->toUrl('/');
      $response->setStatusCode(403);
    }else{
      $this->layout('layout/installer');
    }
  }

  /**
   * 
   * @return type
   */
  public function installDirectoriesAction(){
    $config = $this->getServiceLocator()->get('Config');
    $response = $this->getResponse();
    $responseData = array('success'=>false);
    
    $writePermissions = $this->installer->checkWritePermissions($config['unp_settings']['installation_directories']['writeable']);
    $responseData['messages'] = $this->installer->getMessages();
    $this->installer->resetMessages();
    if(!$writePermissions){
      $responseData['messages'][] = array(
          'message'=>'Not all important directories are writeable. Please use chmod from the console to set the necessary write permissions.',
          'namespace'=>'error'
      );
      $response->setContent(Json::encode($responseData));
      return $response;
    }
    $responseData['success'] = $this->installer->installDirectories($config['unp_settings']['installation_directories']['create']);
    $responseData['messages'] = array_merge($responseData['messages'], $this->installer->getMessages());
    $response->setContent(Json::encode($responseData));

    return $response;
  }

  /**
   * Ajax action that checks the POST for database credentials and the connection.
   * 
   * @return type
   */
  public function installDatabaseAction(){
    $response = $this->getResponse();

    $post = filter_var_array($this->params()->fromPost(), array(
        'host'=>FILTER_UNSAFE_RAW,
        'port'=>FILTER_SANITIZE_NUMBER_INT,
        'user'=>FILTER_UNSAFE_RAW,
        'password'=>FILTER_UNSAFE_RAW,
        'dbname'=>FILTER_UNSAFE_RAW,
            ));
    $responseData = array('success'=>false);

    $connectionData = array('params'=>$post, 'driverClass'=>'Doctrine\DBAL\Driver\PDOMySql\Driver');
    $hasConnection = $this->installer->checkDatabaseConnection($connectionData);
    $responseData['messages'] = $this->installer->getMessages();
    if($hasConnection){
      $defaultConfig = include __DIR__ . '/../../resources/example-settings.local.php';
      $newConfigData = array('doctrine'=>array('connection'=>array('orm_default'=>$connectionData)));
      $config = array_replace_recursive($defaultConfig, $newConfigData);
      $this->installer->createConfigFile($this->configFilePath, $config, true);
    }

    $responseData['success'] = $hasConnection;
    $response->setContent(Json::encode($responseData));

    return $response;
  }

  public function installSettingsAction(){
    $response = $this->getResponse();
    $responseData = array('success'=>false);

    if($this->em){
      $post = filter_var_array($this->params()->fromPost(), array(
          'defaultName'=>FILTER_SANITIZE_STRING,
          'defaultSender'=>FILTER_SANITIZE_STRING,
          'defaultEmail'=>FILTER_SANITIZE_STRING,
          'enableImprint'=>FILTER_VALIDATE_BOOLEAN,
          'contactPerson'=>FILTER_SANITIZE_STRING,
          'street'=>FILTER_SANITIZE_STRING,
          'zip'=>FILTER_SANITIZE_STRING,
          'city'=>FILTER_SANITIZE_STRING,
          'imprintPhone'=>FILTER_SANITIZE_STRING,
          'imprintEmail'=>FILTER_SANITIZE_STRING,
              ));

      $config = array(
          'unp_settings'=>array(
              'application_name'=>$post['defaultName'],
              'imprint_enabled'=>$post['enableImprint'],
              'mailer'=>array(
                  'sender_name'=>$post['defaultSender'],
                  'sender_mail'=>$post['defaultEmail'],
              )
          ),
          'contact'=>array(
              'address'=>array(
                  'street'=>$post['street'],
                  'zip'=>$post['zip'],
                  'city'=>$post['city'],
                  'telephone'=>$post['imprintPhone']
              ),
              'email'=>$post['imprintEmail'],
              'name'=>$post['contactPerson'],
          )
      );
      $responseData['success'] = $this->installer->createConfigFile($this->configFilePath, $config, true);
      $responseData['messages'] = $this->installer->getMessages();
    }else{
      $responseData['messages'] = array(array('message'=>'There was a problem connecting to the database.', 'namespace'=>'error'));
    }

    $response->setContent(Json::encode($responseData));
    return $response;
  }

  public function installAdminAction(){
    $response = $this->getResponse();
    $responseData = array('success'=>false);
    if($this->em){
      $this->installer->updateDatabaseSchema($this->em);
      
      $post = filter_var_array($this->params()->fromPost(), array(
          'adminPassword'=>FILTER_UNSAFE_RAW,
          'adminUsername'=>FILTER_SANITIZE_NUMBER_INT,
          'adminEmail'=>FILTER_UNSAFE_RAW,
              ));

      $adminCreated = $this->installer->createAdmin($this->em, $post['adminUsername'], $post['adminEmail'], $post['adminPassword']);
      $responseData['messages'] = $this->installer->getMessages();
      $responseData['success'] = $adminCreated;
    }else{
      $responseData['messages'] = array(array('message'=>'There was a problem connecting to the database.', 'namespace'=>'error'));
    }
    $response->setContent(Json::encode($responseData));

    return $response;
  }

}
