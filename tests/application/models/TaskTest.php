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

/**
 * Description of TaskTest
 */
class TaskTest extends PHPUnit_Framework_TestCase {
  
  private $object;
  
  public function setUp(){
    $this->object = new Application_Model_Task(array('initiator'=>'the-initiator', 'resource'=>'the-resource', 'action'=>'the-action'));
  }
  
  public function testGetDirectLink(){
    $this->assertNull($this->object->getDirectLink());
  }
  
  public function testGetDirectName(){
    $this->assertNull($this->object->getDirectName());
  }
  
  public function testGetInitiator(){
    $this->assertEquals('the-initiator', $this->object->getInitiator());
  }
  
  public function testGetResource(){
    $this->assertEquals('the-resource', $this->object->getResource());
  }
  
  public function testGetAction(){
    $this->assertEquals('the-action', $this->object->getAction());
  }
  
  public function testEndDateCanBeChanged(){
    $this->object->setEndDate('the-end-date');
    
    $this->assertEquals('the-end-date', $this->object->getEndDate());
  }
  
  public function testLogCanBeChanged(){
    $this->object->setLog('the-log');
    
    $this->assertEquals('the-log', $this->object->getLog());
  }
  
  public function testProgressPercentage(){
    $this->object->setProgressPercentage('the-percentage');
    
    $this->assertEquals('the-percentage', $this->object->getProgressPercentage());
  }
}