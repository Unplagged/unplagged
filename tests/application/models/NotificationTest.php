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

/**
 * Description of NotificationTest
 */
class NotificationTest extends PHPUnit_Framework_TestCase{
  private $object;
  
  public function setUp(){
    $this->object = new Application_Model_Notification(array('user'=>'the-user', 'action'=>'the-action', 'source'=>'the-source', 'permissionSource'=>'the-permission'));
  }
  
  public function testToArray(){
    $this->assertInternalType('array', $this->object->toArray());
  }
  
  public function testGetUser(){
    $this->assertEquals('the-user', $this->object->getUser());
  }
  
  public function testGetSource(){
    $this->assertEquals('the-source', $this->object->getSource());
  }
  
  public function testGetDirectName(){
    $this->assertEquals('notification', $this->object->getDirectName());
  }
  
  public function testGetDirectLink(){
    $this->assertEquals('/notification/show/id/', $this->object->getDirectLink());
  }
  
  public function testGetAction(){
    $this->assertEquals('the-action', $this->object->getAction());
  }
}