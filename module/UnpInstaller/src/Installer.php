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
namespace UnpInstaller;

use Application_Model_User;
use Application_Model_User_Role;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use PDOException;
use Unplagged_Helper;
use Zend\I18n\Translator\Translator;

/**
 * Installs all necessary components of the Unplagged application.
 *
 * @todo check max file upload size, php version, check for apc cache, create doctrine proxies, checkbox for develompent mode
 */
class Installer implements Messenger{

  private $baseDirectory = '';
  private $outputStream = null;
  private $messages = array();
  private $translator = null;

  /**
   * @param string $baseDirectory The directory which should be used as the relative starting point for all installing processes.
   * @param Translator $translator A translator to translate the created messages.
   * @param stream $outputStream If this is provided, messages that are created during the methods will be printed to this
   * output.
   */
  public function __construct($baseDirectory = '', Translator $translator = null, $outputStream = null){
    $this->outputStream = $outputStream;
    $this->baseDirectory = $baseDirectory;
    $this->translator = $translator;
  }

  /**
   * Checks all necessary indicators for whether the application was installed successfully.
   *
   * @param array $config The complete config as it would be used if the real application would be tried to be started.
   * @return boolean
   */
  public function isInstalled($config){
    if($this->composerWasRun() &&
            $this->databaseSettingIsPresent($config, 'user') &&
            $this->databaseSettingIsPresent($config, 'password')){
      return true;
    }

    return false;
  }

  /**
   * Checks whether the composer.lock file is present.
   * 
   * This is probably not a really reliable test for whether Composer really ran successfully, but it's
   * very simple and therefore probably fast.
   * 
   * @return boolean
   */
  public function composerWasRun(){
    if(is_file($this->baseDirectory . '/composer.lock')){
      return true;
    }
    return false;
  }

  /**
   * @param array $config
   * @param string $key
   * @return boolean
   */
  private function databaseSettingIsPresent(array $config, $key){
    if(isset($config['doctrine']['connection']['orm_default']['params'][$key])){
      return true;
    }
    return false;
  }

  /**
   * Updates the bundled Composer executable if possible and runs it for Unplagged.
   * 
   * @return type
   */
  public function runComposer(){
    $result = true;
    $this->output('Updating composer..');
    $composerPath = $this->baseDirectory . '/composer.phar';

    if(is_executable($composerPath)){
      //first self update
      exec($composerPath . ' selfupdate', $composerSelfupdateOutput, $selfupdateStatus);
      if($selfupdateStatus === 0){
        $this->outputCollected($composerSelfupdateOutput);

        //then download dependencies as in composer.json
        exec($composerPath . ' update --working-dir ' . $this->baseDirectory, $composerUpdateOutput, $updateStatus);
        $this->outputCollected($composerUpdateOutput);
      }
    }else{
      $this->output('Sorry, Composer could not be found or is not executable. Please check the permissions for %s.', 'error', array($composerPath));
      $result = false;
    }

    return $result;
  }

  /**
   * Uses the translator if provided and adds it the messages to the messages array.
   * 
   * If an output stream is provided to the constructor, the message gets also printed there.
   * 
   * @param string $message A message in vsprintf() format.
   * @param string $namespace A namespace for styling purposes, that for example could be used as a class name within the HTML.
   * @param array $variables The variables that should be provided to vsprintf().
   */
  private function output($message = '', $namespace = 'status', array $variables = array()){
    if($this->translator){
      $message = $this->translator->translate($message);
    }
    $message = vsprintf($message, $variables);

    if($this->outputStream){
      fwrite($this->outputStream, $message . '' . PHP_EOL);
    }
    $this->messages[] = array('message'=>$message, 'namespace'=>$namespace);
  }

  /**
   * Takes an array and simply ouputs everything inside it.
   * 
   * @param array $output
   */
  private function outputCollected(array $output){
    foreach($output as $outputLine){
      $this->output($outputLine);
    }
  }

  /**
   * Returns all currently stored messages.
   */
  public function getMessages(){
    return $this->messages;
  }

  /**
   * Deletes all currently stored messages.
   */
  public function resetMessages(){
    $this->messages = array();
  }

  /**
   * Checks all the given directories for write permissions.
   *
   * @return boolean Whether all permissions are as required or not.
   */
  public function checkWritePermissions($directories = array()){
    $result = true;
    $this->output('Checking permissions on installation directories...', 'status');

    foreach($directories as $directory){
      $directory = $this->baseDirectory . '/' . $directory;
      $writeable = is_writeable($directory);

      if($writeable){
        $this->output('The directory %s is writeable', 'success', array($directory));
      }else{
        $this->output('The directory %s is not writeable', 'error', array($directory));
        $result = false;
      }
    }

    return $result;
  }

  /**
   * Checks if the specified console scripts are working.
   */
  public function checkConsoleCommands(&$data){
    $scripts['tesseract'] = $data['tesseractPath'];
    $scripts['ghostscript'] = $data['ghostscriptPath'];
    $scripts['imagemagick'] = $data['imagemagickPath'];

    $this->response['steps'][] = array('type'=>'status', 'message'=>'Checking availability of console commands...');

    $success = true;

    // $tessractParser = new Unplagged_Parser_Page_TesseractAdapter(); @todo: make class loadable
    foreach($scripts as $name=> $call){
      if(!empty($call)){
        exec($call, $output, $returnVal);
        $this->response['steps'][] = array('type'=>'success', 'message'=>$output);
        $this->response['steps'][] = array('type'=>'success', 'message'=>$call);
        if($returnVal == 0){
          $this->response['steps'][] = array('type'=>'success', 'message'=>$call . ' can be used within the system.');

          switch($name){
            case 'ghostscript':
              $data['ghostscriptPath'] .= ' -o "%s" -sDEVICE=tiffg4 "%s"';
              break;
            case 'imagemagick':
              $data['imagemagickPath'] .= ' -compress None -quiet +matte -depth 8 "%s" "%s"';
              break;
          }
        }else{
          $this->response['steps'][] = array('type'=>'error', 'message'=>$call . ' can not be executed through the PHP user.');
          //$success = false;
        }
      }
    }
  }

  /**
   * Checks if the database connection can be established with the given parameters.
   * 
   * @param $config Expects an array like the following: 
   *        array(
   *          'driverClass'=>'',
   *          'params'=>array(
   *            //the necessary Doctrine parameters for this driver
   *          )
   *        )
   */
  public function checkDatabaseConnection(array $config){
    $this->output('Checking database connection...');
    if(isset($config['driverClass'])){
      $driverName = $config['driverClass'];
      $driver = new $driverName();
    }else{
      return false;
    }

    try{
      //some dbs don't need those parameters, but to simplify connect call, we set those empty then
      if(!isset($config['params']['user'])){
        $config['params']['user'] = '';
      }
      if(!isset($config['params']['password'])){
        $config['params']['password'] = '';
      }

      $driver->connect($config['params'], $config['params']['user'], $config['params']['password']);
      $this->output('Database connection established.', 'success');
      return true;
    }catch(PDOException $e){
      $this->output('Database connection could not be established, please check your credentials.', 'error');
      $this->output('Exception: %s', 'error', array($e->getMessage()));
      return false;
    }
  }

  /**
   * Takes the given data to write a config file to the
   * 
   * @param string $configFilePath The config file path relative to the base path given to the constructor.
   * @param array $configData
   * @param bool $merge If the true the file is loaded and merged together with the new data.
   * @return bool
   */
  public function createConfigFile($configFilePath, array $configData = array(), $merge = false){
    $fullConfigPath = $this->baseDirectory . '/' . $configFilePath;
    $success = false;
    $config = $configData;
    if(is_readable($fullConfigPath) && $merge){
      $config = array_replace_recursive(include $fullConfigPath, $config);
    }

    if(!is_file($fullConfigPath) || is_writeable($fullConfigPath)){
      $output = file_get_contents(__DIR__ . '/../resources/config-header.txt') . var_export($config, true) . ';';
      $success = (bool) file_put_contents($fullConfigPath, $output);
    }

    if($success){
      $this->output('Config file created successfully.', 'success');
    }
    return $success;
  }

  /**
   * Creates the given directories relative to the base directory given to the constructor. You should check whether all
   * parent directories are writeable with checkWritePermissions() first.
   */
  public function installDirectories(array $directories){
    $this->output('Creating directories');
    $result = true;

    foreach($directories as $directory){
      $fullPath = $this->baseDirectory . '/' . $directory;
      if($this->createDirectory($fullPath)){
        $this->output('Creating directory %s', 'success', array($fullPath));
      }else{
        if(!is_dir($fullPath)){
          $this->output('Creating directory %s failed.', 'error', array($fullPath));
          $result = false;
        }
      }
    }
    return $result;
  }

  /**
   * Creates the given directory if it didn't exist and sets the Linux permissions to 755.
   *
   * @param string $directory The full path of the directory to create.
   * @return bool A boolean indicating whether the directory was created. False probably just means, that the directory
   * already existed, but could also mean that no write access was there.
   */
  private function createDirectory($directory){
    if(!is_dir($directory)){
      @mkdir($directory);

      if(is_dir($directory)){
        @chmod($directory, 0755);
        return true;
      }
    }

    //use chmod even if the directory already existed, to make sure the directory can be accessed later on
    @chmod($directory, 0755);

    return false;
  }

  /**
   * Uses the model classes to update the database schema.
   * 
   * @param EntityManager $entityManager
   */
  public function updateDatabaseSchema(EntityManager $entityManager){
    $this->output('Reading Model classes');
    $schemaTool = new SchemaTool($entityManager);
    $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
    $this->output('Updating database schema');
    $schemaTool->updateSchema($metadata);
    $this->output('Finished updating database schema');
  }

  /**
   * 
   * @param EntityManager $entityManager
   */
  public function deleteDatabaseSchema(EntityManager $entityManager){
    $schemaTool = new SchemaTool($entityManager);
    $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
    $schemaTool->dropSchema($metadata);
  }

  /**
   * Updates the schema and creates all necessary default data.
   * 
   * @todo complete later on
   */
  public function initDatabaseData(EntityManager $entityManager){
    $this->updateDatabaseSchema($entityManager);
    require_once 'initdb.php';
    require_once 'initpermissions.php';
  }

  /**
   * Creates an admin user with all rights.
   *
   * @param type $formData
   * @return boolean
   * 
   * @todo dummy function for now
   */
  public function createAdmin(EntityManager $entityManager, $data){

    /*$data['username'] = $formData['adminUsername'];
    $data['password'] = Unplagged_Helper::hashString($formData['adminPassword']);
    $data['email'] = $formData['adminEmail'];
    $data['verificationHash'] = Unplagged_Helper::generateRandomHash();
    $data['state'] = $entityManager->getRepository('Application_Model_State')->findOneByName('activated');

    $roleTemplate = $em->getRepository('Application_Model_User_Role')->findOneBy(array('roleId'=>'admin', 'type'=>'global'));
    $role = new Application_Model_User_Role();
    $role->setType('user');
    foreach($roleTemplate->getPermissions() as $permission){
      $role->addPermission($permission);
    }

    $em->persist($role);
    $data['role'] = $role;
    $user = new Application_Model_User($data);

    // write back to persistence manager and flush it
    $em->persist($user);

    $em->flush();
    $role->setRoleId($user->getId());
    $em->persist($role);
    $em->flush();*/

    return true;
  }
  
  /**
   * 
   * @return boolean
   * 
   * @todo dummy function for now
   */
  public function adminCreated(){
    
    return true;
  }

}
