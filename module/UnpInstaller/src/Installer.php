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
use Doctrine\Common\ClassLoader;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Unplagged_Helper;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use ZendTest\XmlRpc\Server\Exception;

/**
 * Installs all necessary components of the Unplagged application.
 *
 * @todo check max file upload size, php version, check for apc cache, create doctrine proxies, checkbox for develompent mode
 */
class Installer{

  private $baseDirectory = '';
  private $outputStream = null;
  private $flashMessenger = null;
  private $translator = null;

  /**
   * @param string $baseDirectory
   * @param FlashMessenger| $flashMessenger
   * @param Translator $translator
   */
  public function __construct($baseDirectory = '', Translator $translator = null, FlashMessenger $flashMessenger = null, $outputStream = null){
    $this->outputStream = $outputStream;
    $this->baseDirectory = $baseDirectory;
    $this->flashMessenger = $flashMessenger;
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
  private function composerWasRun(){
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

    if(is_readable($composerPath)){
      //first self update
      exec($composerPath . ' selfupdate', $composerSelfupdateOutput, $selfupdateStatus);
      if($selfupdateStatus === 0){
        $this->outputCollected($composerSelfupdateOutput);

        //then download dependencies as in composer.json
        exec($composerPath . ' update --working-dir ' . $this->baseDirectory, $composerUpdateOutput, $updateStatus);
        $this->outputCollected($composerUpdateOutput);
      }
    }else{
      $this->output('Sorry, Composer could not be found.', 'error');
      $result = false;
    }

    return $result;
  }

  /**
   * Uses the flash messenger and translator if provided or simply echoes the given message.
   * 
   * @param string $message
   * @param string $namespace
   */
  private function output($message = '', $namespace = 'status', $variables = array()){
    if($this->translator){
      $message = $this->translator->translate($message);
    }
    $message = vsprintf($message, $variables);

    if($this->flashMessenger){
      //we need to wrap the message into html here, because the reults should be in order
      //and styled, which is not possible when using flash messengers namespaces
      $wrappedMessage = '<p class="' . $namespace . '">' . $message . '</p>';
      $this->flashMessenger->addMessage($wrappedMessage);
    }else{
      fwrite($this->outputStream, $message . '' . PHP_EOL);
    }
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
    $driverName = $config['driverClass'];
    $driver = new $driverName();

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
    }catch(\PDOException $e){
      $this->output('Database connection could not be established, please check your credentials.', 'error');
      $this->output('Exception: %s', 'error', array($e->getMessage()));
      return false;
    }
  }

  /**
   * Takes the given data to write a config file to the
   * 
   * @param type $data
   * @return type
   */
  public function createConfigFile($data, $overwrite = false){
    $this->output('Creating config file', 'status');
    $success = false;

    if(!is_file($this->configFilePath) || $overwrite){
      $defaultConfig = require __DIR__ . '/../../resources/example-settings.local.php';
      $config = array(
          'unp-settings'=>array(
              'tesseract'=>array(
                  'tesseract_call'=>$data['tesseractCall'],
                  'available_languages'=>array('en')
              ),
              'ghostscript'=>array(
                  'ghostscript_call'=>$data['ghostscriptCall']
              ),
              'imagemagick'=>array(
                  'imagemagick_call'=>$data['imagemagickCall']
              ),
              'imprint_enabled'=>false,
              'mailer'=>array(
                  'sender_name'=>$data['senderName'],
                  'sender_mail'=>$data['senderMail'],
              ),
          ),
          'contact'=>array(
              'address'=>array(
                  'street'=>$data['imprintAddress'],
                  'zip'=>$data['imprintZip'],
                  'city'=>$data['imprintCity'],
                  'telephone'=>$data['imprintPhone'],
                  'email'=>$data['imprintEmail'],
                  'lastname'=>$data['imprintLastname'],
                  'firstname'=>$data['imprintFirstname']
              ),
          ),
          'doctrine'=>array(
              'connection'=>array(
                  'orm_default'=>array(
                      'params'=>array(
                          'host'=>$data['dbHost'],
                          'port'=>$data['dbPort'],
                          'user'=>$data['dbUser'],
                          'password'=>$data['dbPassword'],
                          'dbname'=>$data['dbName'],
                      ),
                  ),
              ),
          ),
      );
      $mergedConfig = array_merge_recursive($defaultConfig, $config);
      $output = file_get_contents(require __DIR__ . '/../../resources/config-header.txt') . var_export($mergedConfig);
      $success = (bool) file_put_contents($this->configFilePath, $output);
    }

    if($success){
      $this->output('Config file created successfully.', 'success');
    }else{
      $this->output('An error occured during the creation of the config file.', 'error');
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
   * @param \Doctrine\ORM\EntityManager $entityManager
   */
  public function updateDatabaseSchema(\Doctrine\ORM\EntityManager $entityManager){
    $this->output('Reading Model classes');
    $schemaTool = new SchemaTool($entityManager);
    $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
    $this->output('Updating database schema');
    $schemaTool->updateSchema($metadata);
    $this->output('Finished updating database schema');
  }

  /**
   * 
   * @param \Doctrine\ORM\EntityManager $entityManager
   */
  public function deleteDatabaseSchema(\Doctrine\ORM\EntityManager $entityManager){
    $schemaTool = new SchemaTool($entityManager);
    $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
    $schemaTool->dropSchema($metadata);
  }

  /**
   * Updates the schema and creates all necessary default data.
   */
  public function initDatabaseData(\Doctrine\ORM\EntityManager $entityManager){
    $this->updateDatabaseSchema($entityManager);
    require_once 'initdb.php';
    require_once 'initpermissions.php';
  }

  /**
   * Creates an admin user with all rights.
   *
   * @param type $formData
   * @return boolean
   */
  public function createAdmin($formData){
    $this->response['steps'][] = array('type'=>'status', 'message'=>'Creating admin user...');

    require BUILD_PATH . DIRECTORY_SEPARATOR . 'initbase.php';

    $data = array();
    $data['username'] = $formData['adminUsername'];
    $data['password'] = Unplagged_Helper::hashString($formData['adminPassword']);
    $data['email'] = $formData['adminEmail'];
    $data['verificationHash'] = Unplagged_Helper::generateRandomHash();
    $data['state'] = $em->getRepository('Application_Model_State')->findOneByName('activated');

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
    $em->flush();

    return true;
  }

}
