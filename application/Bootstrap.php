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
require_once('Doctrine' . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR . 'ClassLoader.php');

use \Doctrine\Common\ClassLoader;

/**
 * This class is the starting point for the Unplagged application and initalizes 
 * all base components.
 *
 * Please remember that all the '_init*' methods are called alphabetically, so make sure to explicitly bootstrap
 * all dependencies in methods that deviate from this order.
 * 
 * @author Unplagged
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap{

  /**
   * Makes sure that only authorized users can see certain parts of the application.
   * 
   * It's probably the best if this is called right up front, to make sure nobody can access
   * something accidentally somehow.
   */
  protected function _initAccessControl(){
    //we need the entity manager, so make sure this is created prior
    $this->bootstrap('doctrine');
    //make sure at least the guest user is set if nobody logged in yet
    $this->bootstrap('user');

    $registry = Zend_Registry::getInstance();

    //initalize the current users ACL
    $acl = new Unplagged_Acl($registry->user, $registry->entitymanager);
    $registry->acl = $acl;
    $accessControl = new Unplagged_AccessControl($acl, $registry->user);

    //make sure front controller is initalized, so that we can register the authorization plugin
    $this->bootstrap('FrontController');
    $frontController = $this->getResource('FrontController');
    $frontController->registerPlugin($accessControl);
  }

  /**
   * @todo does this do anything? 
   */
  protected function _initAutoloadCrons(){
    $autoloader = new Zend_Loader_Autoloader_Resource(array(
          'namespace'=>'Cron_',
          'basePath'=>APPLICATION_PATH . '/../scripts/jobs/',
        ));
  }

  /**
   * Loads the config and sets it in the registry.
   * 
   * @return \Zend_Config 
   */
  protected function _initConfig(){
    $config = new Zend_Config($this->getOptions(), true);
    Zend_Registry::set('config', $config);
    return $config;
  }

  /**
   * Initializes the Doctrine EntityManager.
   * 
   * Based on Jan Oliver Oelerich (http://www.oelerich.org/?p=193).
   * 
   * @todo enable Caching
   * @return EntityManager
   */
  public function _initDoctrine(){
    $classLoader = new ClassLoader('Doctrine', BASE_PATH . DIRECTORY_SEPARATOR . 'library');
    $classLoader->register();
    $classLoader = new ClassLoader('models', APPLICATION_PATH);
    $classLoader->register();
    $classLoader = new ClassLoader('proxies', BASE_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR);
    $classLoader->register();

    $config = new \Doctrine\ORM\Configuration();
    $driverImpl = $config->newDefaultAnnotationDriver(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'models');
    $config->setMetadataDriverImpl($driverImpl);

    $config->setProxyDir(BASE_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'doctrine');
    $config->setProxyNamespace('Proxies');

    $connectionOptions = $this->loadDatabaseConnectionCredentials();
    $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
    $registry = Zend_Registry::getInstance();
    $registry->entitymanager = $em;

    return $em;
  }

  /**
   * Loads the database connection credentials from the config file.
   */
  private function loadDatabaseConnectionCredentials(){
    $doctrineConfig = $this->getOption('doctrine');
    $connectionOptions = array(
      'driver'=>$doctrineConfig['conn']['driv'],
      'user'=>$doctrineConfig['conn']['user'],
      'password'=>$doctrineConfig['conn']['pass'],
      'dbname'=>$doctrineConfig['conn']['dbname'],
      'host'=>$doctrineConfig['conn']['host']
    );

    return $connectionOptions;
  }

  /**
   * Stores the current user in the registry.
   * 
   * If no user is logged in, the guest user is set as a default.
   */
  protected function _initUser(){
    $registry = $registry = Zend_Registry::getInstance();
    $defaultNamespace = new Zend_Session_Namespace('Default');

    if(!$defaultNamespace->userId || $defaultNamespace->userId === 'guest'){
      $guestId = $registry->entitymanager->getRepository('Application_Model_Setting')->findOneBySettingKey('guest-role-id');
      $guestRole = $registry->entitymanager->getRepository('Application_Model_User_Role')->findOneById($guestId->getValue());

      $registry->user = new Application_Model_User(array('role'=>$guestRole));
      $defaultNamespace->userId = 'guest';
    }else{
      $currentUser = $registry->entitymanager->getRepository('Application_Model_User')->findOneById($defaultNamespace->userId);
      $registry->set('user', $currentUser);
    }
  }

  /**
   * Registers the plugin that stores the last visited url. 
   */
  protected function _initHistory(){
    $frontController = $this->getResource('FrontController');
    $frontController->registerPlugin(new Unplagged_UrlHistory());
  }

  /**
   * Sets the logger inside the registry, so that it can be called via:
   * 
   * <code>Zend_Registry::get('log')->crit('Critical');</code>
   * 
   * The settings are provided inside the log.ini file.
   * 
   * This function can not be named _initLog, because this creates a conflict with Zend.
   * 
   * @todo rotate logfiles 
   */
  protected function _initLogger(){/*
    $this->bootstrap('Zend_Log');

    if (!$this->hasPluginResource('Zend_Log')) {
    throw new Zend_Exception('Log not enabled in config.ini');
    }

   */
    $writer = new Zend_Log_Writer_Stream(BASE_PATH . '/data/logs/unplagged.log');
    $logger = new Zend_Log($writer);
    //   $logger = $this->getResource('Log');
    // assert($logger != null);
    Zend_Registry::set('Log', $logger);
  }

  /**
   * 
   */
  protected function _initNavigation(){

    $config = array(
      array(
        //home icon gets set via js, because I didn't find a simple way to add a <span> here
        'label'=>'Home',
        'title'=>'Home',
        'module'=>'default',
        'controller'=>'index',
        'action'=>'index',
        'class'=>'home',
        'order'=>-100 // make sure home is the first page
      ), array(
        'label'=>'Activity',
        'title'=>'Activity',
        'module'=>'default',
        'controller'=>'notification',
        'action'=>'recent-activity',
        'resource'=>'notification_recent-activity'
      ), array(
        'label'=>'Files',
        'title'=>'Files',
        'module'=>'default',
        'controller'=>'file',
        'action'=>'upload',
        'resource'=>'file_upload',
        'pages'=>array(
          array(
            'label'=>'Case Files',
            'title'=>'Case Files',
            'module'=>'default',
            'controller'=>'case',
            'action'=>'files',
            'resource'=>'case_files'
          ),
          array(
            'label'=>'Public Files',
            'title'=>'Public Files',
            'module'=>'default',
            'controller'=>'file',
            'action'=>'list',
            'resource'=>'file_list'
          ),
          array(
            'label'=>'Personal Files',
            'title'=>'Personal Files',
            'module'=>'default',
            'controller'=>'user',
            'action'=>'files',
            'resource'=>'user_files'
          )
        )
      ), array(
        'label'=>'Documents',
        'title'=>'Documents',
        'module'=>'default',
        'controller'=>'document',
        'action'=>'list',
        'resource'=>'document_list'
      ), array(
        'label'=>'Fragments',
        'title'=>'Fragments',
        'module'=>'default',
        'controller'=>'document_fragment',
        'action'=>'list',
        'resource'=>'document_fragment_list'
      ), array(
        'label'=>'Administration',
        'title'=>'Administration',
        'uri'=>'#',
        'resource'=>'admin_index',
        'pages'=>array(
          array(
            'label'=>'Cases',
            'title'=>'Cases',
            'module'=>'default',
            'controller'=>'case',
            'action'=>'list',
            'resource'=>'case_list'
          ),
          array(
            'label'=>'Roles',
            'title'=>'Roles',
            'module'=>'default',
            'controller'=>'permission',
            'action'=>'list',
            'resource'=>'permission_list'
          )
        )
      )
    );

    $container = new Zend_Navigation($config);
    $this->bootstrap('layout');
    $layout = $this->getResource('layout');
    $view = $layout->getView();
    $registry = Zend_Registry::getInstance();
    $view->navigation($container)->setAcl($registry->acl)->setRole($registry->user->getRole());

    Zend_Registry::set('Zend_Navigation', $container);
  }

  /**
   * Generate registry and initalize language support.
   * 
   * The translation files are assumed to be in the /data/languages directory and named with the ISO language code and
   * an ending of '.csv', i. e. 'de.csv' for german.
   * 
   * @return Zend_Registry
   */
  protected function _initTranslate(){
    $registry = Zend_Registry::getInstance();
    //takes the browser language as default
    $locale = new Zend_Locale();
    $registry->set('Zend_Locale', $locale);

    $languageString = $locale->getLanguage();
    $translationFilePath = BASE_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . $languageString . '.csv';

    //try to load the language file
    if(file_exists($translationFilePath)){
      $translate = new Zend_Translate('csv', $translationFilePath, $languageString);
      $registry->set('Zend_Translate', $translate);
    }

    // translate standard zend framework messages
    $translator = new Zend_Translate(
            array(
              'adapter'=>'array',
              'content'=>BASE_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'languages',
              'locale'=>$locale,
              'scan'=>Zend_Translate::LOCALE_FILENAME
            )
    );
    Zend_Validate_Abstract::setDefaultTranslator($translator);

    return $registry;
  }

  /**
   * Initalizes the view.
   *
   * As the default resource plugin is overkill, simply overwrite it here with a smaller method.
   */
  protected function _initView(){
    $view = new Zend_View();

    $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
    $defaultConfig = $this->getOption('default');
    $view->headTitle()->setSeparator(' - ')->append($defaultConfig['applicationName']);
    return $view;
  }

  /**
   * @todo seems unused
   */
  protected function setConstants($constants){
    foreach($constants as $key=>$value){
      if(!defined($key)){
        define($key, $value);
      }
    }
  }

}