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
use UnpInstaller\Controller\ConsoleInstallerController;
use UnplaggedTest\Bootstrap;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Mvc\Router\RouteMatch;

/**
 * 
 */
class ConsoleInstallerControllerTest extends PHPUnit_Framework_TestCase{
  protected $controller;
  protected $request;
  protected $response;
  protected $routeMatch;
  protected $event;

  protected function setUp(){
    $serviceManager = Bootstrap::getServiceManager();
    $this->controller = new ConsoleInstallerController();
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
  
  /**
   * Redirects output into a file.
   */
  public function testUpdateSchemaCanBeExecuted(){
    $entityManager = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
    $this->controller->setEntityManager($entityManager);
    $filePath = __DIR__ . '/../../../../../resources/tmp/output-test.txt';
    $stream = fopen($filePath, 'w+t');
    $this->controller->setOutputStream($stream);
    $this->controller->updateDatabaseSchemaAction(); 
    fclose($stream);
    $this->assertTrue(file_exists($filePath));
    //unlink(__DIR__ . '/../../../../../resources/output-test.txt');
  }
  
  /*
   * Seems untestable because it hangs and waits for input which can not be given. 
   */
  /*
  public function testCheckDatabaseConnectionActionCanBeExecuted(){
    $entityManager = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
    $this->controller->setEntityManager($entityManager);
    $filePath = __DIR__ . '/../../../../../resources/tmp/output-test.txt';
    $stream = fopen($filePath, 'w+t');
    $this->controller->setOutputStream($stream);
    $this->controller->checkDatabaseConnectionAction();
    echo '1';
    fclose($stream);
    $this->assertTrue(file_exists($filePath));
  }*/
  
}