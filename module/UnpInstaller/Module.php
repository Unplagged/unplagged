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

use UnpCommon\Controller\BaseController;
use Zend\Console\Adapter\AdapterInterface;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\ServiceManager\Exception\ExceptionInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Installs all necessary components of the Unplagged application.
 *
 * @todo check max file upload size, php version, check for apc cache, create doctrine proxies, checkbox for develompent mode
 */
class Module implements
ConsoleUsageProviderInterface, ConsoleBannerProviderInterface, AutoloaderProviderInterface, BootstrapListenerInterface, ConfigProviderInterface{

  /**
   * Initalizes the application during the bootstrapping process.
   * 
   * @param EventInterface $e
   */
  public function onBootstrap(EventInterface $e){
    try{
      $serviceManager = $e->getApplication()->getServiceManager();
      $this->initControllerInitalizer($serviceManager);
    }catch(ExceptionInterface $e){
      //it's ok if we run into an exception, happens if Unplagged is not installed
      //just the most convenient way to set the entitymanager if already possible
    }
  }

  /**
   * Injects Doctrines entitymanager into every created controller.
   * 
   * @param ServiceManager $serviceManager
   */
  private function initControllerInitalizer(ServiceManager $serviceManager){
    $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
    $controllerLoader = $serviceManager->get('ControllerLoader');
    $config = $serviceManager->get('Config');
    
    //something is badly broken if this is not set, so we don't need to check, just let the error do it's work
    $configFilePath = $config['unp_settings']['installer_config_file'];
    
    $controllerLoader->addInitializer(function ($controller) use ($entityManager, $configFilePath){
              if($controller instanceof BaseController){
                $controller->setEntityManager($entityManager);
              }
              if(method_exists($controller, 'setConfigFilePath')){
                $controller->setConfigFilePath($configFilePath);
              }
            });
  }

  /**
   * Provides information about all modules and libraries that need to be loaded for this module.
   * 
   * @return array The autoloader configuration.
   */
  public function getAutoloaderConfig(){
    return array(
        'Zend\Loader\StandardAutoloader'=>array(
            'namespaces'=>array(
                __NAMESPACE__=>__DIR__ . '/src',
            )
        )
    );
  }

  /**
   * Loads the config file and returns it's content.
   * 
   * @return array
   */
  public function getConfig(){
    return include __DIR__ . '/config/module.config.php';
  }

  /**
   * Provides information on how to use the Unplagged console interface.
   * 
   * @param AdapterInterface $console
   * @return array
   */
  public function getConsoleUsage(AdapterInterface $console){
    return array(
        // Describe available commands
        //'--install'=>'Installs the application',
        '--update-db-schema'=>'Updates the database schema from the model files.',
        '--delete-db-schema'=>'Deletes the database completely and resets it to the initial state',
        //'--test-data'=>'Adds a basic set of test data to the database',
        '--uninstall'=>'(Re)Enables the webinstaller.',
        '--check-db-connection'=>'Queries all important parameters and tries to connect to the database'
            // Describe expected parameters
            //array('--verbose|-v', '(optional) turn on verbose mode'),
    );
  }

  /**
   * The first strig that gets displayed when Unplagged is called via the command line. Simply
   * identifies the console interface for the user.
   * 
   * @param AdapterInterface $console
   * @return string
   */
  public function getConsoleBanner(AdapterInterface $console){
    return 'Unplagged Console interface';
  }

}