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

use Zend\Mvc\ModuleRouteListener;
use Zend\EventManager\EventInterface;

/**
 * This class is the starting point for the Unplagged application and initalizes 
 * all base components.
 */
class Module{

  public function onBootstrap(EventInterface $e){
    $serviceManager = $e->getApplication()->getServiceManager();
    $eventManager = $e->getApplication()->getEventManager();

    $this->initTranslator($eventManager, $serviceManager);
    $this->initDoctrineDependencyInjection($serviceManager);
  }

  private function initTranslator($eventManager, $serviceManager){
    $serviceManager->get('translator');
    $moduleRouteListener = new ModuleRouteListener();
    $moduleRouteListener->attach($eventManager);
  }

  private function initDoctrineDependencyInjection($serviceManager){
    $controllerLoader = $serviceManager->get('ControllerLoader');

    // Add initializer to Controller Service Manager that check if controllers needs entity manager injection
    $controllerLoader->addInitializer(function ($instance) use ($serviceManager){
              if(method_exists($instance, 'setEntityManager')){
                $instance->setEntityManager($serviceManager->get('doctrine.entitymanager.unplagged_orm'));
              }
            });
  }

  public function getConfig(){
    return include __DIR__ . '/config/module.config.php';
  }

  public function getAutoloaderConfig(){
    return array(
        'Zend\Loader\StandardAutoloader'=>array(
            'namespaces'=>array(
                __NAMESPACE__=>__DIR__ . '/src/' . __NAMESPACE__,
            ),
        ),
    );
  }

}
