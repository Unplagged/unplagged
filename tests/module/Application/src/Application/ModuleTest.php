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
namespace ApplicationTest;

use Zend\EventManager\EventManager;
use Zend\Http\PhpEnvironment;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Application;
use Zend\ServiceManager\ServiceManager;
use Application\Module;

/**
 * 
 */
class ModuleTest extends \PHPUnit_Framework_TestCase{

  private $object;

  protected function setUp(){
    $this->object = new Module();
  }

  public function testGetAutoloaderConfigReturnsArray(){
    $this->assertInternalType('array', $this->object->getAutoloaderConfig());
  }

  public function testGetConfig(){
    $this->assertInstanceOf('\Zend\Config\Config', $this->object->getConfig());
  }

  public function testGetViewHelperConfig(){
    $this->assertInternalType('array', $this->object->getViewHelperConfig());
  }

  public function testOnBootstrap(){
    //simply test whether the method runs without bugging out
    try{


      $config = include __DIR__ . '/../../../../TestConfig.php.dist';

      $serviceManager = new ServiceManager();
      $serviceManager->setService('EventManager', new EventManager());
      $serviceManager->setService('ModuleManager', new ModuleManager($config['modules']));
      $serviceManager->setService('Request', new PhpEnvironment\Request());
      $serviceManager->setService('Response', new PhpEnvironment\Response());

      $application = new Application($config, $serviceManager);
      $event = $this->getMock('Zend\EventManager\Event', array('getApplication')); 
      $event->expects($this->any())
              ->method('getApplication')
              ->will($this->returnValue($application));
      $this->object->onBootstrap($event);
    }catch(Exception $e){
      $this->fail();
    }
  }

}