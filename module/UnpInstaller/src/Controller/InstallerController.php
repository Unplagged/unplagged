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
 * Controller to serve the webinstaller.
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
    $installer = new Installer(BASE_PATH, $this->getServiceLocator()->get('translator'));

    return $installer;
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
      $installer = $this->createInstaller();
      $stepStatus = array(
          'download'=>$this->findDownloadStatus($installer),
          'directories'=>$this->checkDirectoryStatus($config),
          'database'=>$this->findDatabaseStatus($installer, $config),
          'contact'=>array('finished'=>false, 'messages'=>array()),
          'admin'=>array('finished'=>$installer->adminCreated(), 'messages'=>array()),
          'software'=>array('finished'=>false, 'messages'=>array()),
      );
      $stepStatus['currentStep'] = $this->findCurrentStep($stepStatus);

      return array('stepStatus'=>$stepStatus);
    }
  }

  private function findCurrentStep(array $stepStatus){
    $count = 0;
    if($stepStatus['download']['finished']){
      $count++;
      if($stepStatus['directories']['finished']){
        $count++;
        if($stepStatus['database']['finished']){
          $count++;
          if($stepStatus['contact']['finished']){
            $count++;
            if($stepStatus['admin']['finished']){
              $count++;
            }
          }
        }
      }
    }
    return $count;
  }

  private function checkDirectoryStatus($config){
    $status = true;
    foreach($config['unp_settings']['installation_directories']['create'] as $directory){
      $status = $status && is_writeable($directory);
    }
    $result = array(
        'finished'=>$status,
        'messages'=>array(),
    );
    if($status){
      $result['messages'][] = array('message'=>'All necessary directories exist.', 'namespace'=>'success');
    }

    return $result;
  }

  /**
   * Checks the database status and returns an array with the status and messages.
   * 
   * @param \UnpInstaller\Installer $installer
   * @param type $config
   * @return array
   */
  private function findDatabaseStatus(\UnpInstaller\Installer $installer, $config){
    $parameters = array();
    if(isset($config['doctrine']['connection']['orm_default'])){
      $parameters = $config['doctrine']['connection']['orm_default'];
    }
    $result = $this->findStepStatus($installer, 'checkDatabaseConnection', $parameters);
    //we only want to show if the connection already works, because the installer will ask for credentials otherwise
    $result['messages'] = array();
    if($result['finished']){
      $result['messages'][] = array('message'=>$this->getServiceLocator()->get('translator')->translate('The database connection is working.'), 'namespace'=>'success');
    }
    return $result;
  }

  private function findDownloadStatus(\UnpInstaller\Installer $installer){
    $result = $this->findStepStatus($installer, 'composerWasRun');
    if(!$result['finished']){
      $result['messages'][] = array('message'=>$this->getServiceLocator()->get('translator')->translate('Dependency downloads are not finished. Please run "composer update" from the command line.'), 'namespace'=>'success');
    }

    return $result;
  }

  /**
   * Creates an array with the status and messages for the requested callback.
   * @param \UnpInstaller\Messenger $callbackObject
   * @param string $callbackFunction
   * @param array $params
   * @return array
   */
  private function findStepStatus(\UnpInstaller\Messenger $callbackObject, $callbackFunction, $params = array()){
    $finished = false;
    if(method_exists($callbackObject, $callbackFunction)){
      $finished = $callbackObject->$callbackFunction($params);
    }
    $result = array('finished'=>$finished, 'messages'=>$callbackObject->getMessages());
    $callbackObject->resetMessages();

    return $result;
  }

  /**
   * Ajax action that checks the given parameters and returns the current installation status messages.
   */
  public function installAction(){
    $request = $this->getRequest();

    $installer = $this->createInstaller();

    if(!$installer->composerWasRun()){
      $installer->runComposer();
    }
    if($request->isPost()){
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

    //$response->s
  }

  /**
   * 
   * @return type
   */
  public function installDirectoriesAction(){
    $config = $this->getServiceLocator()->get('Config');
    $response = $this->getResponse();
    $responseData = array('success'=>false);

    $installer = $this->createInstaller();
    $writePermissions = $installer->checkWritePermissions($config['unp_settings']['installation_directories']['writeable']);
    $responseData['messages'] = $installer->getMessages();
    $installer->resetMessages();
    if(!$writePermissions){
      $responseData['messages'][] = array(
          'message'=>'Not all important directories are writeable. Please use chmod from the console to set the necessary write permissions.',
          'namespace'=>'error'
      );
      $response->setContent(\Zend\Json\Json::encode($responseData));
      return $response;
    }
    $responseData['success'] = $installer->installDirectories($config['unp_settings']['installation_directories']['create']);
    $responseData['messages'] = array_merge($responseData['messages'], $installer->getMessages());
    $response->setContent(\Zend\Json\Json::encode($responseData));

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

    $installer = $this->createInstaller();
    $connectionData = array('params'=>$post, 'driverClass'=>'Doctrine\DBAL\Driver\PDOMySql\Driver');
    $hasConnection = $installer->checkDatabaseConnection($connectionData);
    $responseData['messages'] = $installer->getMessages();
    if($hasConnection){
      $defaultConfig = include __DIR__ . '/../../resources/example-settings.local.php';
      $newConfigData = array('doctrine'=>array('connection'=>array('orm_default'=>$connectionData)));
      $config = array_replace_recursive($defaultConfig, $newConfigData);
      $installer->createConfigFile('config/autoload/settings.local.php', $config, true);
    }

    $responseData['success'] = $hasConnection;
    $response->setContent(\Zend\Json\Json::encode($responseData));

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

      $installer = $this->createInstaller();
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
      $responseData['success'] = $installer->createConfigFile('config/autoload/settings.local.php', $config, true);
      $responseData['messages'] = $installer->getMessages();
    }else{
      $responseData['messages'] = array(array('message'=>'There was a problem connecting to the database.', 'namespace'=>'error'));
    }

    $response->setContent(\Zend\Json\Json::encode($responseData));
    return $response;
  }

  public function installAdminAction(){
    $response = $this->getResponse();
    $responseData = array('success'=>false);

    if($this->em){
      $installer = $this->createInstaller();
      $installer->updateDatabaseSchema($this->em);
      
      $post = filter_var_array($this->params()->fromPost(), array(
          'adminPassword'=>FILTER_UNSAFE_RAW,
          'adminUsername'=>FILTER_SANITIZE_NUMBER_INT,
          'adminEmail'=>FILTER_UNSAFE_RAW,
              ));

      $adminCreated = $installer->createAdmin($this->em, $post['adminUsername'], $post['adminEmail'], $post['adminPassword']);
      $responseData['messages'] = $installer->getMessages();
      $responseData['success'] = $adminCreated;
    }else{
      $responseData['messages'] = array(array('message'=>'There was a problem connecting to the database.', 'namespace'=>'error'));
    }
    $response->setContent(\Zend\Json\Json::encode($responseData));

    return $response;
  }

}
