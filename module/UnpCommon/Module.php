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
namespace UnpCommon;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * This module contains all of Unplaggeds classes that are common to the different modules.
 */
class Module implements AutoloaderProviderInterface, ConfigProviderInterface{

  public function onBootstrap(\Zend\Mvc\MvcEvent $e){
    $eventManager = $e->getApplication()->getEventManager();
    $eventManager->attach(\Zend\Mvc\MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'));
  }

  public function onDispatch(\Zend\Mvc\MvcEvent $e){
    $request = $e->getRequest();

    //make sure to not store AJAX requests, because they tend to make no sense when using the back button
    if($request instanceof \Zend\Http\Request && !$request->isXmlHttpRequest()){
      $historySessionNamespace = new \Zend\Session\Container('history');
      $historySessionNamespace->last = $request->getRequestUri();
    }
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

}
