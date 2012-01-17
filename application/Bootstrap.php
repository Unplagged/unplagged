<?php
/**
 * File for class {@link Bootstrap}.
 */

/**
 * The class is the starting point for the application and initalizes base components.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap{

  protected function _initConfig(){
    $config = new Zend_Config($this->getOptions(), true);
    Zend_Registry::set('config', $config);
    return $config;
  }
  
  /**
   * Initalize the view.
   * @author Dennis De Cock
   */
  protected function _initView(){
    $defaultConfig = $this->getOption('default');

    $view = new Zend_View();
    //ZendX_JQuery::enableView($view);
    $viewrenderer = new Zend_Controller_Action_Helper_ViewRenderer();
    $viewrenderer->setView($view);
    Zend_Controller_Action_HelperBroker::addHelper($viewrenderer);
    $this->bootstrap('layout');
    $layout = $this->getResource('layout');
    $view = $layout->getView();

    $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
    $view->headTitle()->setSeparator(' - ');
    $view->headTitle($defaultConfig['portalName']);
  }

  
  
  /**
   * Generate registry and initalize language support
   * @return Zend_Registry
   */
  protected function _initTranslate(){
    $registry = Zend_Registry::getInstance();
    $locale = new Zend_Locale('de_DE');
    $translate = new Zend_Translate('csv', BASE_PATH . '/data/languages/de.csv', 'de');
    //$translate->addTranslation(APPLICATION_PATH . '/../languages/de.csv', 'de'); //TODO: add automatically lang support

    $registry->set('Zend_Locale', $locale);
    $registry->set('Zend_Translate', $translate);

    // translate standard zend framework messages
    $translator = new Zend_Translate(
            array(
              'adapter'=>'array',
              'content'=>BASE_PATH . '/data/resources/languages',
              'locale'=>$locale,
              'scan'=>Zend_Translate::LOCALE_DIRECTORY
            )
    );
    Zend_Validate_Abstract::setDefaultTranslator($translator);

    return $registry;
  }

  /**
   * Initialize auto loader of Doctrine to get the database connection.
   * @author: Jan Oliver Oelerich (http://www.oelerich.org/?p=193)
   *
   * @return Doctrine_Manager
   */
  public function _initDoctrine(){
    require_once('Doctrine/Common/ClassLoader.php');

    $doctrineConfig = $this->getOption('doctrine');

    $classLoader = new \Doctrine\Common\ClassLoader('Doctrine', APPLICATION_PATH . '/../library/');
    $classLoader->register();

    $classLoader = new \Doctrine\Common\ClassLoader('models', APPLICATION_PATH);
    $classLoader->register();

    $classLoader = new \Doctrine\Common\ClassLoader('proxies', APPLICATION_PATH);
    $classLoader->register();

    $config = new \Doctrine\ORM\Configuration();
    $driverImpl = $config->newDefaultAnnotationDriver(APPLICATION_PATH . "/models");
    $config->setMetadataDriverImpl($driverImpl);

    //$cache = new \Doctrine\Common\Cache\ArrayCache;
    //$config->setMetadataCacheImpl($cache);
    //$config->setQueryCacheImpl($cache);

    $config->setProxyDir(APPLICATION_PATH . '/proxies');
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
  protected function _initLogger() {
    $this->bootstrap('Zend_Log');

    if (!$this->hasPluginResource('Log')) {
      throw new Zend_Exception('Log not enabled in config.ini');
    }

    
    $logger = $this->getResource('Log');
    assert($logger !== null);
    Zend_Registry::set('log', $logger);
  }
  
  /**
   * 
   */
  protected function _initNavigation(){
    
    $config = array(
      array(
        //home icon gets set via js, because I didn't find a simple way to do add a <span> here
        'label' => 'Home',
        'title' => 'Home',
        'module' => 'default',
        'controller' => 'index',
        'action' => 'index',
        'class' => 'home',     
        'order' => -100 // make sure home is the first page
      ), array(
        'label' => 'Cases',
        'title' => 'Cases',
        'module' => 'default',
        'controller' => 'case',
        'action' => 'list',
        'pages' => array(
          array(
            'label' => 'Create Case',
            'title' => 'Create Case',
            'module' => 'default',
            'controller' => 'case',
            'action' => 'create'
          )
        )
      ), array(
        'label' => 'Files',
        'title' => 'Files',
        'module' => 'default',
        'controller' => 'file',
        'action' => 'list'
      ), array(
        'label' => 'Documents',
        'title' => 'Documents',
        'module' => 'default',
        'controller' => 'document',
        'action' => 'list'
      )
    );
    
    $container = new Zend_Navigation($config);
    
    //@todo doesn't work here for now still in layout.phtml
    /*if(Zend_Auth::getInstance()->hasIdentity()){
      $container->addPage(array(
        'label' => 'Edit profile',
        'title' => 'Edit profile',
        'module' => 'default',
        'controller' => 'user',
        'action' => 'edit/id/' . $defaultNamespace->user->getId()
      ));
    }*/
    
    Zend_Registry::set('Zend_Navigation', $container);
  }
}