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

defined('INSTALLER_PATH')
        || define('INSTALLER_PATH', BUILD_PATH . DIRECTORY_SEPARATOR . 'Installer');

use \Doctrine\Common\ClassLoader;
use \UnpInstaller\TemplateParser;


/**
 * Installs all necessary components of the Unplagged application.
 *
 * @todo check max file upload size, php version, check for apc cache, create doctrine proxies, checkbox for develompent mode
 */
class Installer {

  private $configFilePath = '';
  private $writeableDirectories = null;
  private $installationDirectories = null;
  private $response = array();

  public function __construct($configFilePath) {
    $this->configFilePath = $configFilePath;

    $this->writeableDirectories = array(
        'data',
        'data'. DIRECTORY_SEPARATOR . 'temp',
        'configs',
    );

    $this->installationDirectories = array(
        'data' . DIRECTORY_SEPARATOR . 'uploads',
        'data' . DIRECTORY_SEPARATOR . 'logs',
        'data' . DIRECTORY_SEPARATOR . 'cache',
        'data' . DIRECTORY_SEPARATOR . 'reports',
        'data' . DIRECTORY_SEPARATOR . 'avatars',
        'data' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'proxies',
        'data' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'ocr',
        'data' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'imagemagick',
    );
  }

  /**
   * Checks all necessary indicators for whether the system is installed successfully.
   *
   * @return boolean
   */
  public function isInstalled() {
    if (is_readable($this->configFilePath)) {
      return true;
    }

    return false;
  }

  private function validateInputData(){
    $data = filter_input_array(INPUT_POST);
    
    return $data;
  }
  
  /**
   * Executes all the steps required for installing unplagged.
   * @return type
   */
  public function install() {
    if (empty($_POST)) {
      $this->renderStartPage();
    } else {
      $data = $this->validateInputData();

      $this->initDirectories();
      $this->checkWritePermissions();
      $this->checkConsoleCommands($data);
      if($this->checkDatabaseParams($data)){
        if($this->createConfig($data)){
          $this->initDatabase();
          $this->createAdmin($data);
        }
      }

      $this->parseResponse();
    }
  }

  private function renderStartPage() {
    $parser = new TemplateParser(INSTALLER_PATH . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR);
    $data = array('welcome.title' => 'Setup Wizard');
    echo $parser->parseFile('header.tpl', $data);
    echo $parser->parseFile('install.tpl', null);
    echo $parser->parseFile('footer.tpl', null);
  }

  private function parseResponse() {
    $hasError = false;
    foreach ($this->response['steps'] as $step) {
      if ($step['type'] == 'error') {
        $hasError = true;
        break;
      }
    }

    if ($hasError) {
      $this->response['summary'] = array('type' => 'error', 'message' => 'There are errors, please fix them and start again.');
    } else {
      $this->response['summary'] = array('type' => 'success', 'message' => 'Installation successful. Please reload the page and you are done.');
    }
    //set correct json headers necessary for some browsers
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');
    echo json_encode($this->response);
  }

  /**
   * Checks all the directories that need to be writeable.
   *
   * @return boolean Whether all permissions are as required or not.
   */
  private function checkWritePermissions() {
    $success = true;

    $this->response['steps'][] = array('type' => 'status', 'message' => 'Checking permissions on installation directories...');

    foreach ($this->writeableDirectories as $directory) {
      $directory = BASE_PATH . DIRECTORY_SEPARATOR . $directory;
      $writeable = is_writeable($directory);

      if ($writeable) {
        $this->response['steps'][] = array('type' => 'success', 'message' => $directory . ' is writeable');
      } else {
        $this->response['steps'][] = array('type' => 'error', 'message' => $directory . ' not writeable');
        $success = false;
      }
    }

    if (!$success) {
      $this->response['steps'][] = array('type' => 'error', 'message' => 'Some directories are not writeable, please change the permissions on them and start again.');
    }
  }

  /**
   * Checks if the specified console scripts are working.
   */
  private function checkConsoleCommands(&$data) {
    $scripts['tesseract'] = $data['tesseractPath'];
    $scripts['ghostscript'] = $data['ghostscriptPath'];
    $scripts['imagemagick'] = $data['imagemagickPath'];

    $this->response['steps'][] = array('type' => 'status', 'message' => 'Checking availability of console commands...');

    $success = true;

   // $tessractParser = new Unplagged_Parser_Page_TesseractAdapter(); @todo: make class loadable
    foreach ($scripts as $name => $call) {
      if (!empty($call)) {
        exec($call, $output, $returnVal);
        $this->response['steps'][] = array('type' => 'success', 'message' => $output);
        $this->response['steps'][] = array('type' => 'success', 'message' => $call);
        if ($returnVal == 0) {
          $this->response['steps'][] = array('type' => 'success', 'message' => $call . ' can be used within the system.');

          switch ($name) {
            case 'ghostscript':
              $data['ghostscriptPath'] .= ' -o "%s" -sDEVICE=tiffg4 "%s"';
              break;
            case 'imagemagick':
              $data['imagemagickPath'] .= ' -compress None -quiet +matte -depth 8 "%s" "%s"';
              break;
          }
        } else {
          $this->response['steps'][] = array('type' => 'error', 'message' => $call . ' can not be executed through the PHP user.');
          //$success = false;
        }
      }
    }
  }

  /**
   * Checks if the database connection can be established with the given parameters.
   */
  private function checkDatabaseParams($data) {
    $classLoader = new ClassLoader('Doctrine', LIBRARY_PATH);
    $classLoader->register();

    $config = new \Doctrine\ORM\Configuration();
    $driverImpl = $config->newDefaultAnnotationDriver(INSTALLER_PATH);
    $config->setMetadataDriverImpl($driverImpl);
    $config->setProxyDir(INSTALLER_PATH);
    $config->setProxyNamespace('Proxies');

    $this->response['steps'][] = array('type' => 'status', 'message' => 'Checking database connection...');

    $connectionOptions = array(
        'driver' => 'pdo_mysql',
        'user' => $data['dbUser'],
        'password' => $data['dbPassword'],
        'dbname' => $data['dbName'],
        'host' => $data['dbHost']
    );
    $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

    try {
      @$em->getConnection()->connect();
    } catch (Exception $e) {
      $this->response['steps'][] = array('type' => 'error', 'message' => 'Database connection could not be established.');
      return false;
    }

    $this->response['steps'][] = array('type' => 'success', 'message' => 'Database connection established.');
    return true;
  }

  /**
   * Writes the previousely set settings to a config file in the file system.
   * 
   * @param type $data
   * @return type
   */
  private function createConfig($data) {
    $this->response['steps'][] = array('type' => 'status', 'message' => 'Creating config file...');

    $params = array(
        'conn.host' => $data['dbHost']
        , 'conn.user' => $data['dbUser']
        , 'conn.pass' => $data['dbPassword']
        , 'conn.driv' => 'pdo_mysql'
        , 'conn.dbname' => $data['dbName']
        , 'default.applicationName' => $data['defaultName']
        , 'default.senderName' => $data['defaultSender']
        , 'default.senderMail' => $data['defaultEmail']
        , 'imprint.address' => $data['imprintAddress']
        , 'imprint.telephone' => $data['imprintPhone']
        , 'imprint.email' => $data['imprintEmail']
        , 'parser.tesseractPath' => $data['tesseractPath']
        , 'parser.imagemagickPath' => $data['imagemagickPath']
        , 'parser.ghostscriptPath' => $data['ghostscriptPath']
    );

    $parser = new TemplateParser(INSTALLER_PATH);
    $response = $parser->parseFile('unplagged-config-sample.ini', $params);

    $result = false;
    $result = (bool) file_put_contents($this->configFilePath, $response);
    if($result){
      $this->response['steps'][] = array('type' => 'success', 'message' => 'Config file created successfully.');
    }else {
      $this->response['steps'][] = array('type' => 'error', 'message' => 'An error occured during the creation of the config file.');  
    }
    return $result;
  }

  /**
   * Creates all necessary directories.
   */
  private function initDirectories() {
    $this->response['steps'][] = array('type' => 'status', 'message' => 'Creating directories...');
    $error = false;
    
    foreach ($this->installationDirectories as $directory) {
      $fullPath = BASE_PATH . DIRECTORY_SEPARATOR . $directory;
      if ($this->createDirectory($fullPath)) {
        $this->response['steps'][] = array('type' => 'success', 'message' => 'Creating directory ' . $fullPath);
      } else {
        if (!is_dir($fullPath)) {
          $this->response['steps'][] = array('type' => 'error', 'message' => 'Creating directory ' . $fullPath);
          $error = true;
        }
      }
    }

    if(!$error){
      $this->response['steps'][] = array('type' => 'success', 'message' => 'All necessary directories were successfully created.');
    }
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
  private function createDirectory($directory) {
    if (!is_dir($directory)) {
      mkdir($directory);

      @chmod($directory, 0777);
      return true;
    }

    //use chmod even if the directory already existed, to make sure the directory can be accessed later on
    @chmod($directory, 0777);

    return false;
  }

  /**
   * Initializes the database.
   */
  private function initDatabase() {
    require_once 'updateDatabase.php';
    require_once 'initdb.php';
    require_once 'initpermissions.php';
  }

  /**
   * Creates an admin user with all rights.
   *
   * @param type $formData
   * @return boolean
   */
  private function createAdmin($formData) {
    $this->response['steps'][] = array('type' => 'status', 'message' => 'Creating admin user...');

    require BUILD_PATH . DIRECTORY_SEPARATOR . 'initbase.php';

    $data = array();
    $data['username'] = $formData['adminUsername'];
    $data['password'] = Unplagged_Helper::hashString($formData['adminPassword']);
    $data['email'] = $formData['adminEmail'];
    $data['verificationHash'] = Unplagged_Helper::generateRandomHash();
    $data['state'] = $em->getRepository('Application_Model_State')->findOneByName('activated');

    $roleTemplate = $em->getRepository('Application_Model_User_Role')->findOneBy(array('roleId' => 'admin', 'type' => 'global'));
    $role = new Application_Model_User_Role();
    $role->setType('user');
    foreach ($roleTemplate->getPermissions() as $permission) {
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
