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
 * This class is the starting point for the Unplagged application and initalizes 
 * all base components.
 *
 * @author Unplagged
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap{

  /**
   * Initialize auto loader of Doctrine to get the database connection.
   * 
   * @author Jan Oliver Oelerich (http://www.oelerich.org/?p=193)
   * @return Doctrine_Manager
   */
  public function _initDoctrine(){
    require_once('Doctrine' . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR . 'ClassLoader.php');

    $doctrineConfig = $this->getOption('doctrine');

    $classLoader = new \Doctrine\Common\ClassLoader('Doctrine', BASE_PATH . DIRECTORY_SEPARATOR . 'library');
    $classLoader->register();

    $classLoader = new \Doctrine\Common\ClassLoader('models', APPLICATION_PATH);
    $classLoader->register();

    $classLoader = new \Doctrine\Common\ClassLoader('proxies', BASE_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR);
    $classLoader->register();

    $config = new \Doctrine\ORM\Configuration();
    $driverImpl = $config->newDefaultAnnotationDriver(APPLICATION_PATH . "/models");
    $config->setMetadataDriverImpl($driverImpl);

    //$cache = new \Doctrine\Common\Cache\ArrayCache;
    //$config->setMetadataCacheImpl($cache);
    //$config->setQueryCacheImpl($cache);

    $config->setProxyDir(BASE_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR . 'proxies');
    $config->setProxyNamespace('Proxies');

    $connectionOptions = array(
      'driver'=>$doctrineConfig['conn']['driv'],
      'user'=>$doctrineConfig['conn']['user'],
      'password'=>$doctrineConfig['conn']['pass'],
      'dbname'=>$doctrineConfig['conn']['dbname'],
      'host'=>$doctrineConfig['conn']['host']
    );

    $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

    $registry = Zend_Registry::getInstance();
    $registry->entitymanager = $em;

    return $em;
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
   * Initalizes the flash messenger.
   */
  protected function _initMessenger(){
    $flashMessenger = new Zend_Controller_Action_Helper_FlashMessenger();
    Zend_Controller_Action_HelperBroker::addHelper($flashMessenger);

    $messages = $flashMessenger->getMessages();
    $this->bootstrap('layout');
    $view = $this->getResource('layout')->getView();

    $view->assign('messages', $messages);
  }

  /**
   * If no user is logged in, the guest user is set as default.
   */
  protected function _initGuest(){
    
    $defaultNamespace = new Zend_Session_Namespace('Default');
    
    if(!$defaultNamespace->user){
      //we need the entity manager, so make sure this is created prior
      $this->bootstrap('doctrine');
      $registry = Zend_Registry::getInstance();
      $guestRole = $registry->entitymanager->getRepository('Application_Model_User_GuestRole')->findOneByRoleId('guest');
      
      //store the user in the session
      $defaultNamespace->user = new Application_Model_User(array('role'=>$guestRole));
    }
  }

  /**
   * 
   */
  protected function _initAccessControl(){
    //make sure at least the rights for the guest user are set if nobody logged in yet
    $this->bootstrap('guest');
    //we also need the entity manager, so make sure this is created prior
    $this->bootstrap('doctrine');
    
    //initalize the current users ACL
    $defaultNamespace = new Zend_Session_Namespace('Default');
    $registry = Zend_Registry::getInstance();
    $acl = new Unplagged_Acl($defaultNamespace->user, $registry->entitymanager);
    $accessControl = new Unplagged_AccessControl($acl, $defaultNamespace->user);

    //make sure front controller is initalized, so that we can register the authorization plugin
    $this->bootstrap('FrontController');
    $frontController = $this->getResource('FrontController');
    $frontController->registerPlugin($accessControl);
  }

  /**
   * Registers the plugin that stores the last visited url. 
   */
  protected function _initHistory(){
    $frontController = $this->getResource('FrontController');
    $frontController->registerPlugin(new Unplagged_UrlHistory());
  }

  /**
   * Initalize the view.
   * @author Dennis De Cock
   */
  protected function _initView(){
    $defaultConfig = $this->getOption('default');

    $view = new Zend_View();

    $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
    $view->headTitle()->setSeparator(' - ')->append($defaultConfig['applicationName']);
  }

  /**
   * Generate registry and initalize language support.
   * 
   * @return Zend_Registry
   */
  protected function _initTranslate(){
    $registry = Zend_Registry::getInstance();
    $locale = new Zend_Locale('de_DE');

    $registry->set('Zend_Locale', $locale);

    $translate = new Zend_Translate('csv', BASE_PATH . '/data/languages/de.csv', 'de');
    //$translate->addTranslation(APPLICATION_PATH . '/../languages/de.csv', 'de'); //TODO: add automatically lang support

    $registry->set('Zend_Translate', $translate);

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
    $writer = new Zend_Log_Writer_Stream(BASE_PATH . "/data/logs/unplagged.log");
    $logger = new Zend_Log($writer);
    //   $logger = $this->getResource('Log');
    // assert($logger != null);
    Zend_Registry::set('Log', $logger);
  }

  /**
   * 
   */
  protected function _initNavigation(){
    //$this->bootstrap('doctrine');
    
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
        'pages'=>array(
          array(
            'label'=>'Cases',
            'title'=>'Cases',
            'module'=>'default',
            'controller'=>'case',
            'action'=>'list'
          )
        )
      )
    );

    $container = new Zend_Navigation($config);
    $this->bootstrap('layout');
    $layout = $this->getResource('layout');
    $view = $layout->getView();
    $defaultNamespace = new Zend_Session_Namespace('Default');
    $registry = Zend_Registry::getInstance();
    $acl = new Unplagged_Acl($defaultNamespace->user, $registry->entitymanager);
    $view->navigation($container)->setAcl($acl)->setRole($defaultNamespace->user->getRole());

    Zend_Registry::set('Zend_Navigation', $container);
  }

  protected function setConstants($constants){
    foreach($constants as $key=>$value){
      if(!defined($key)){
        define($key, $value);
      }
    }
  }

  protected function _initAutoloadCrons(){
    $autoloader = new Zend_Loader_Autoloader_Resource(array(
          'namespace'=>'Cron_',
          'basePath'=>APPLICATION_PATH . '/../scripts/jobs/',
        ));
  }

}
