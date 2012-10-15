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
 * 
 */
class ParserTest extends PHPUnit_Framework_TestCase{
  
  private $object;
  
  public function setUp(){
    $entityManager = Zend_Registry::getInstance()->entitymanager;
    $this->object = new Unplagged_Cron_Document_Parser($entityManager);
  }
  
  public function testStart(){
    $result = $this->object->start();
    
    //$this->assertTrue($result);
  }
  
  public function testRunTimeIsInitiallyZero(){
    $this->assertEquals(0, $this->object->getRunTime());
  }
  
  public function testMemoryIsInitiallyZero(){
    $this->assertEquals(0, $this->object->getUsedMemory());
  }
  
  public function testBenchmarkIsPrinted(){
    $this->expectOutputRegex('/Time/');
    $this->object->printBenchmark();
  }
}