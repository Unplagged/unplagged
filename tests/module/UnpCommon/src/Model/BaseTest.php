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

use PHPUnit_Framework_TestCase;
use UnpCommon\Model\State;

/**
 * 
 */
class BaseTest extends PHPUnit_Framework_TestCase {

  private $object;
  
  public function setUp() {
    $stub = $this->getMockForAbstractClass('\UnpCommon\Model\Base');
    $this->object = $stub;
  }
  
  public function testStateCanBeSet(){
    $state = new State();
    $this->object->setState($state);
    
    $this->assertEquals($state, $this->object->getState());
  }
  
  public function testDefaultIdIsNull(){
    $this->assertNull($this->object->getId());
  }
  
  public function testCreatedSetsTheDatetime(){
    $this->assertNull($this->object->getCreated());
    
    $this->object->created();
    
    $this->assertInstanceOf('\DateTime', $this->object->getCreated());
  }

}