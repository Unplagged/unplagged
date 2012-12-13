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
namespace Application;

use Zend\EventManager\EventInterface;
use Zend\Config\Factory;
use Zend\Config\Config;
use Zend\ServiceManager\ServiceManager;

/**
 * This class is the starting point for the Unplagged application and initalizes 
 * all base components.
 */
class Module{

  /**
   * Initalizes the application during the bootstrapping process.
   * 
   * @param \Zend\EventManager\EventInterface $e
   */
  public function onBootstrap(EventInterface $e){
    $serviceManager = $e->getApplication()->getServiceManager();

    $this->initDoctrine($serviceManager);
  }

  /**
   * Injects Doctrines entitymanager into every created controller.
   * 
   * @param \Zend\ServiceManager\ServiceManager $serviceManager
   */
  private function initDoctrine(ServiceManager $serviceManager){
    $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
    $controllerLoader = $serviceManager->get('ControllerLoader');
    $controllerLoader->addInitializer(function ($controller) use ($entityManager){
              if($controller instanceof Controller\BaseController){
                $controller->setEntityManager($entityManager);
              }
            });
  }

  public function getViewHelperConfig(){
    return array(
        'factories'=>array(
            'flashMessages'=>function($sm){

              $flashmessenger = $sm->getServiceLocator()
                      ->get('ControllerPluginManager')
                      ->get('flashmessenger');

              $message = new \Application\Helper\FlashMessages();
              $message->setFlashMessenger($flashmessenger);

              return $message;
            }
        ),
    );
  }

  /**
   * This method provides all configuration information of this module. It is expected by ZEND2, so it will be called
   * when the configuration is needed.
   * 
   * @return \Zend\Config\Config
   */
  public function getConfig(){
    $defaultConfig = new Config(include __DIR__ . '/config/module.config.php');
    $navigationConfig = Factory::fromFile(__DIR__ . '/config/navigation.xml', true);

    $defaultConfig->merge($navigationConfig);
    return $defaultConfig;
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

}
