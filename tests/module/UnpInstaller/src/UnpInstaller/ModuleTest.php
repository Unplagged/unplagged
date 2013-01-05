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
namespace UnpInstallerTest;

use Exception;
use PHPUnit_Framework_TestCase;
use UnpInstaller\Module;
use UnplaggedTest\Bootstrap;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;

/**
 * 
 */
class ModuleTest extends PHPUnit_Framework_TestCase{
  private $object;

  protected function setUp(){
    $this->object = new Module();
  }

  public function testOnBootstrap(){
    //simply test whether the method runs without bugging out
    try{
      $serviceManager = Bootstrap::getServiceManager();
      $config = $serviceManager->get('Config');

      $application = new Application($config, $serviceManager);
      $event = new MvcEvent();
      $event->setApplication($application);
      $this->object->onBootstrap($event);
    }catch(Exception $e){
      $this->fail();
    }
  }

  
  public function testGetAutoloaderConfigReturnsArray(){
    $this->assertInternalType('array', $this->object->getAutoloaderConfig());
  }

  public function testGetConfig(){
    $this->assertInternalType('array', $this->object->getConfig());
  }

  public function testGetConsoleUsage(){
    $console = $this->getMock('\Zend\Console\Adapter\AdapterInterface');
    $this->assertInternalType('array', $this->object->getConsoleUsage($console));
  }
  
  public function testGetConsoleBanner(){
    $console = $this->getMock('\Zend\Console\Adapter\AdapterInterface');
    $this->assertInternalType('string', $this->object->getConsoleBanner($console));
  }
}