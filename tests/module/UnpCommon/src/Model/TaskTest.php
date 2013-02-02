<?php
/*
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
namespace UnpCommonTest;

use \PHPUnit_Framework_TestCase;
use \UnpCommon\Model\Action;
use \UnpCommon\Model\Task;
use \UnpCommon\Model\File;
use \UnpCommon\Model\User;

/**
 *
 */
class TaskTest extends PHPUnit_Framework_TestCase{

  /**
   * @var Application_Model_Action
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp(){
    $user = new User();
    $action = new Action(array('name'=>'', 'title'=>'', 'description'=>''));
    $resource = new File();
    $this->object = new Task($user, $action, $resource);
  }

  public function testGetProgressPercentage(){
    $this->object->setProgressPercentage(30);
    $this->assertEquals(30, $this->object->getProgressPercentage());
  }

  public function testGetAndSetLog(){
    $this->object->setLog('message');
    $this->assertEquals('message', $this->object->getLog());
  }

  public function testEnded(){
    $this->assertNull($this->object->getEndDate());

    $this->object->ended();

    $this->assertInstanceOf('\DateTime', $this->object->getEndDate());
  }

  public function testGetAction(){
    $this->assertInstanceOf('\UnpCommon\Model\Action', $this->object->getAction());
  }
  
  public function testGetResource(){
    $this->assertInstanceOf('\UnpCommon\Model\Base', $this->object->getResource());
  }
  
  public function testGetInitiator(){
    $this->assertInstanceOf('\UnpCommon\Model\User', $this->object->getInitiator());
  }

}