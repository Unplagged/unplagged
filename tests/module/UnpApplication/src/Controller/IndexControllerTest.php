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
namespace UnpApplicationTest\Controller;

use UnplaggedTest\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use UnpApplication\Controller\IndexController;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

/**
 * 
 */
class IndexControllerTest extends \PHPUnit_Framework_TestCase{

  protected $controller;
  protected $request;
  protected $response;
  protected $routeMatch;
  protected $event;

  protected function setUp(){
    $serviceManager = Bootstrap::getServiceManager();
    $this->controller = new IndexController();
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

  public function testImprintActionGetsDispatchedWhenEnabled(){
    $serviceManager = Bootstrap::getServiceManager();
    $config = $serviceManager->get('Config');
    $config['unp_settings']['imprint_enabled'] = true;
    $config['contact'] = array(
        'address'=>array(
            'street'=>'Street 123',
            'zip'=>'12345',
            'city'=>'Berlin',
            'telephone'=>'+49 30 123 45 67'
        ),
        'telephone'=>'',
        'email'=>'info@example.com',
        'lastname'=>'Doe',
        'firstname'=>'John'
    );
    $serviceManager->setService('Config', $config);

    $this->routeMatch->setParam('action', 'imprint');
    $this->controller->dispatch($this->request);
    $response = $this->controller->getResponse();

    $this->assertEquals(200, $response->getStatusCode());
  }

  public function testImprintActionDelivers404WhenNotEnabled(){
    $serviceManager = Bootstrap::getServiceManager();
    $config = $serviceManager->get('Config');
    $config['unp_settings']['imprint_enabled'] = false;
    $serviceManager->setService('Config', $config);

    $this->routeMatch->setParam('action', 'imprint');

    $result = $this->controller->dispatch($this->request);
    $response = $this->controller->getResponse();

    $this->assertEquals(404, $response->getStatusCode());
  }

  public function testIndexActionGetsDispatched(){
    $this->routeMatch->setParam('action', 'index');

    $result = $this->controller->dispatch($this->request);
    $response = $this->controller->getResponse();

    $this->assertEquals(200, $response->getStatusCode());
  }

  public function testIndexActionReturnsBarcodes(){
    $this->routeMatch->setParam('action', 'index');

    $result = $this->controller->dispatch($this->request);

    $this->assertTrue(array_key_exists('barcodes', $result));
  }

}