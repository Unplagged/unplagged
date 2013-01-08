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
namespace UnpInstallerTest\Controller;

use PHPUnit_Framework_TestCase;
use UnpInstaller\Controller\InstallerController;
use UnplaggedTest\Bootstrap;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Mvc\Router\RouteMatch;

/**
 * 
 */
class InstallerControllerTest extends PHPUnit_Framework_TestCase{
  protected $controller;
  protected $request;
  protected $response;
  protected $routeMatch;
  protected $event;

  protected function setUp(){
    $serviceManager = Bootstrap::getServiceManager();
    $this->controller = new InstallerController();
    $this->request = new Request();
    $this->routeMatch = new RouteMatch(array('controller'=>'index'));
    $this->event = new MvcEvent();
    $config = $serviceManager->get('Config');
    $routerConfig = isset($config['router']) ? $config['router'] : array();
    $router = HttpRouter::factory($routerConfig);

    $this->event->setRouter($router);
    $this->event->setRouteMatch($this->routeMatch);
    $this->controller->setEvent($this->event);
    $this->controller->setServiceLocator($serviceManager);
  }

  public function testIndexActionGetsDispatched(){
    $this->routeMatch->setParam('action', 'index');

    $result = $this->controller->dispatch($this->request);
    $response = $this->controller->getResponse();

    $this->assertEquals(200, $response->getStatusCode());
  }
  
  public function testIndexActionWithoutEnabledInstaller(){
    $serviceManager = Bootstrap::getServiceManager();
    $this->controller = new InstallerController();
    $this->request = new Request();
    $this->routeMatch = new RouteMatch(array('controller'=>'index'));
    $this->event = new MvcEvent();
    $config = $serviceManager->get('Config');
    $config['unp_settings']['installer_enabled'] = false;
    $serviceManager->setService('Config', $config);
    $routerConfig = isset($config['router']) ? $config['router'] : array();
    $router = HttpRouter::factory($routerConfig);

    $this->event->setRouter($router);
    $this->event->setRouteMatch($this->routeMatch);
    $this->controller->setEvent($this->event);
    $this->controller->setServiceLocator($serviceManager);
    
    $this->routeMatch->setParam('action', 'index');

    $result = $this->controller->dispatch($this->request);
    $response = $this->controller->getResponse();

    $config['unp_settings']['installer_enabled'] = true;
    $serviceManager->setService('Config', $config);
    $this->assertEquals(403, $response->getStatusCode());
  }
  
  public function testInstallAdminActionGetsDispatched(){
    $this->routeMatch->setParam('action', 'installAdmin');

    $this->controller->dispatch($this->request);
    $response = $this->controller->getResponse();

    $this->assertEquals(200, $response->getStatusCode());
  }
  
}