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

use UnpInstaller\Controller\InstallerController;
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
      //$serviceManager = $e->getApplication()->getServiceManager();
      //$this->initDoctrine($serviceManager);
    }catch(ExceptionInterface $e){
      echo "Sorry, there seems to be a problem with our database server, which couldn't be resolved.";
    }
  }

  /**
   * Injects Doctrines entitymanager into every created controller.
   * 
   * @param ServiceManager $serviceManager
   */
  private function initDoctrine(ServiceManager $serviceManager){
    $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
    $controllerLoader = $serviceManager->get('ControllerLoader');
    $controllerLoader->addInitializer(function ($controller) use ($entityManager){
              if($controller instanceof InstallerController){
                $controller->setEntityManager($entityManager);
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
                __NAMESPACE__=>__DIR__ . '/src/' . __NAMESPACE__,
            )
        )
    );
  }

  public function getConfig(){
    return include __DIR__ . '/config/module.config.php';
  }

  public function getConsoleUsage(AdapterInterface $console){
    return array(
        // Describe available commands
        '--install'=>'Installs the application',
        '--update-schema'=>'Updates the database schema from the model files. Keeps existing data or fails.',
        '--reset-databse'=>'Deletes the database completely and resets it to the initial state',
        '--test-data'=>'Adds a basic set of test data to the database',
        // Describe expected parameters
        array('--verbose|-v', '(optional) turn on verbose mode'),
    );
  }

  public function getConsoleBanner(AdapterInterface $console){
    return 'Unplagged Console interface';
  }

}
