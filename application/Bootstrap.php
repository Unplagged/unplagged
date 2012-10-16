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
 * all dependencies in methods that need to deviate from this order.
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
    $classLoaderDoctrine = new ClassLoader('Doctrine', BASE_PATH . DIRECTORY_SEPARATOR . 'library');
    $classLoaderDoctrine->register();

    $config = new \Doctrine\ORM\Configuration();
    $driverImpl = $config->newDefaultAnnotationDriver(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'models');
    $config->setMetadataDriverImpl($driverImpl);

    if(APPLICATION_ENV === 'production' && extension_loaded('apc') && ini_get('apc.enabled')){
      $cache = new \Doctrine\Common\Cache\ApcCache;
    }else{
      $cache = new \Doctrine\Common\Cache\ArrayCache;
    }
    $config->setMetadataCacheImpl($cache);
    $config->setQueryCacheImpl($cache);

    $config->setProxyDir(TEMP_PATH);
    $config->setProxyNamespace('Proxies');

    $connectionOptions = $this->loadDatabaseConnectionCredentials();
    $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

    try {
      @$em->getConnection()->connect();
    }catch(Exception $e) {
      die('Sorry, there seems to be a problem with our database server.');
    }

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
    $registry = Zend_Registry::getInstance();
    $defaultNamespace = new Zend_Session_Namespace('Default');

    if(!$defaultNamespace->userId || $defaultNamespace->userId === 'guest'){
      $guestId = $registry->entitymanager->getRepository('Application_Model_Setting')->findOneBySettingKey('guest-id');
      $guest = $registry->entitymanager->getRepository('Application_Model_User')->findOneById($guestId->getValue());

      $caseId = $defaultNamespace->case;
      if($caseId){
        $case = $registry->entitymanager->getRepository('Application_Model_Case')->findOneById($caseId);
        $guest->setCurrentCase($case);
      }

      $registry->user = $guest;
      $defaultNamespace->userId = 'guest';
    }else{
      $registry->user = $registry->entitymanager->getRepository('Application_Model_User')->findOneById($defaultNamespace->userId);
    }

    //if we don't have a user something went wrong with the session, so clear everything and force new login
    if(!$registry->user){
      session_unset();
      header('Location: ');
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
   * Sets the logger in the registry, so that it can be called via:
   * 
   * <code>Zend_Registry::get('Log')->crit('Critical');</code>
   * 
   * The settings are provided inside the log.ini file.
   * 
   * This function can not be named _initLog, because this creates a conflict with Zend.
   */
  protected function _initLogger(){
    $log = null;

    //enable the logs as provided in the log.ini
    if($this->hasPluginResource('log')){
      $this->bootstrap('log');
      $log = $this->getResource('log');
    }else{
      //if no logger was provided within the config, stub it out so no errors get thrown when logging is called
      $writer = new Zend_Log_Writer_Null;
      $log = new Zend_Log($writer);
    }
    Zend_Registry::getInstance()->Log = $log;
    //Zend_Registry::get('Log')->err('Hi');
  }

  /**
   * 
   */
  protected function _initNavigation(){
    $config = new Zend_Config_Xml(BASE_PATH . '/data/navigation.xml', 'nav');
    $container = new Zend_Navigation($config);
    
    //show case files if a case is selected
    $user = Zend_Registry::get('user');
    if($user->getCurrentCase()){
      $caseFiles = $container->findOneBy('title', 'Case Files');
      $caseFiles->setVisible();
    }
    
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
    }else{
      //init an empty Zend_Translate to always have an object
      $registry->set('Zend_Translate', new Zend_Translate('csv'));
    }

    //translate standard zend framework messages and supress errors which occur when language was not found
    //should default to english
    $translator = @new Zend_Translate(
                    array(
                        'adapter'=>'array',
                        'content'=>BASE_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'languages',
                        'locale'=>$locale,
                        'scan'=>Zend_Translate::LOCALE_FILENAME
                    )
    );
    Zend_Validate_Abstract::setDefaultTranslator($translator);

    //log untranslated strings
    $untranslatedWriter = new Zend_Log_Writer_Stream(BASE_PATH . '/data/logs/untranslated.log');
    $untranslatedLog = new Zend_Log($untranslatedWriter);

    $translator->setOptions(array(
        'log'=>$untranslatedLog,
        'logUntranslated'=>true)
    );

    if(!empty($translate)){
      $translate->setOptions(array(
          'log'=>$untranslatedLog,
          'logUntranslated'=>true)
      );
    }
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

}