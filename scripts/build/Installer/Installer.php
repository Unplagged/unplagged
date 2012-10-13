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

defined('INSTALLATION_PATH')
        || define('INSTALLATION_PATH', BASE_PATH . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR . 'Installer' . DIRECTORY_SEPARATOR);

require_once INSTALLATION_PATH . 'TemplateParser.php';
require_once(BASE_PATH . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'Doctrine' . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR . 'ClassLoader.php');

use \Doctrine\Common\ClassLoader;

/**
 * Installs all necessary components of the Unplagged application.
 */
class Installer {

  private $configFilePath = '';
  private $setupType;
  private $writeableDirectories = array();
  private $installationDirectories = array();
  private $response = array();

  public function __construct($configFilePath = '', $setupType = 'gui') {
    $this->configFilePath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'unplagged-config.ini';
    $this->setupType = $setupType;

    define('UNP_EOL', $this->setupType == 'gui' ? '<br />' . PHP_EOL : PHP_EOL);

    $this->writeableDirectories = array(
        'data',
        'temp',
        'application' . DIRECTORY_SEPARATOR . 'configs'
    );

    $this->installationDirectories = array(
        'data' . DIRECTORY_SEPARATOR . 'uploads',
        'data' . DIRECTORY_SEPARATOR . 'logs',
        'data' . DIRECTORY_SEPARATOR . 'cache',
        'data' . DIRECTORY_SEPARATOR . 'reports',
        'data' . DIRECTORY_SEPARATOR . 'doctrine',
        'data' . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR . 'proxies',
        'temp' . DIRECTORY_SEPARATOR . 'ocr',
        'temp' . DIRECTORY_SEPARATOR . 'imagemagick',
        'data' . DIRECTORY_SEPARATOR . 'avatars'
    );
  }

  /**
   * Checks all necessary indicators for whether the system is installed successfully.
   * 
   * @return boolean
   */
  public function isInstalled() {
    // checks whether the "unplagged-config.ini" exists
    if (!file_exists($this->configFilePath)) {
      return false;
    }

    return true;
  }

  /**
   * Executes all the steps required for installing unplagged.
   * @return type
   */
  public function install() {
    if (empty($_POST)) {
      $this->renderStartPage();
    } else {
      // 1) validate all input fields
      $data = filter_input_array(INPUT_POST);

      // 1) check write permissions on directories.
      $done = $this->checkWritePermissions();
      if (!$done) {
        $this->parseResponse();
        return;
      }

      // 2) check console scripts
      $done = $this->checkConsoleCommands($data);
      if (!$done) {
        $this->parseResponse();
        return;
      }

      // 3) validate db connection params
      $done = $this->checkDatabaseParams($data);
      if (!$done) {
        $this->parseResponse();
        return;
      }

      // 4) create config
      $done = $this->createConfig($data);
      if (!$done) {
        $this->parseResponse();
        return;
      }

      // 5) init directories
      $done = $this->initDirectories();
      if (!$done) {
        $this->parseResponse();
        return;
      }

      // 6) init db and permissions
      $this->initDatabase();

      // 7) create admin user
      $this->createAdmin($data);

      $this->parseResponse();
    }
  }

  private function renderStartPage() {
    $parser = new TemplateParser(INSTALLATION_PATH . 'tpl' . DIRECTORY_SEPARATOR);
    $data = array('welcome.title' => 'Installation wizard');
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

    return $success;
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
    
    $tessractParser = new Unplagged_Parser_Page_TesseractAdapter();
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

    return $success;
  }

  /**
   * Checks if the database conncetion can be established with the given parameters.
   */
  private function checkDatabaseParams($data) {
    $classLoader = new ClassLoader('Doctrine', BASE_PATH . DIRECTORY_SEPARATOR . 'library');
    $classLoader->register();

    $config = new \Doctrine\ORM\Configuration();
    $driverImpl = $config->newDefaultAnnotationDriver(INSTALLATION_PATH);
    $config->setMetadataDriverImpl($driverImpl);
    $config->setProxyDir(INSTALLATION_PATH);
    $config->setProxyNamespace('Proxies');

    $this->response['steps'][] = array('type' => 'status', 'message' => 'Checking database connection params...');

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
        , 'imprint.telephone' => $data['imprintTelephone']
        , 'imprint.email' => $data['imprintEmail']
        , 'parser.tesseractPath' => $data['tesseractPath']
        , 'parser.imagemagickPath' => $data['imagemagickPath']
        , 'parser.ghostscriptPath' => $data['ghostscriptPath']
    );

    $parser = new TemplateParser(INSTALLATION_PATH);
    $response = $parser->parseFile('unplagged-config-sample.ini', $params);

    $this->response['steps'][] = array('type' => 'success', 'message' => 'Config file created successfully.');

    return (bool) file_put_contents($this->configFilePath, $response);
  }

  /**
   * Creates all necessary directories 
   */
  private function initDirectories() {
    //add directories that need to be created here
    //make sure to include them in the right order, so that dependencies occur beforehand
    //i. e. /data -> /data/uploads

    $this->response['steps'][] = array('type' => 'status', 'message' => 'Creating directories...');

    foreach ($this->installationDirectories as $directory) {
      $fullPath = BASE_PATH . DIRECTORY_SEPARATOR . $directory;
      if ($this->createDirectory($fullPath)) {
        $this->response['steps'][] = array('type' => 'success', 'message' => 'Creating directory ' . $fullPath);
      } else {
        if (!is_dir($fullPath)) {
          $this->response['steps'][] = array('type' => 'error', 'message' => 'Creating directory ' . $fullPath);
        }
      }
    }

    return true;
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

      chmod($directory, 0777);
      return true;
    }

    //use chmod even if the directory already existed, to make sure the directory can be accessed later on
    chmod($directory, 0777);

    return false;
  }

  /**
   * Initializes the database.
   */
  private function initDatabase() {
    require_once(INSTALLATION_PATH . '..' . DIRECTORY_SEPARATOR . 'doctrine.php');
    require_once(INSTALLATION_PATH . '..' . DIRECTORY_SEPARATOR . 'initdb.php');
    require_once(INSTALLATION_PATH . '..' . DIRECTORY_SEPARATOR . 'initpermissions.php');
  }

  /**
   * Creates an admin user with all rights.
   * 
   * @param type $formData
   * @return boolean
   */
  private function createAdmin($formData) {
    $this->response['steps'][] = array('type' => 'status', 'message' => 'Creating admin user...');

    require INSTALLATION_PATH . '..' . DIRECTORY_SEPARATOR . 'initbase.php';

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