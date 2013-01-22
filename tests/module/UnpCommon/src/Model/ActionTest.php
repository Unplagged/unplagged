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
use UnpCommon\Model\Action;

/**
 *
 */
class ActionTest extends PHPUnit_Framework_TestCase {

  /**
   * @var Application_Model_Action
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp() {
    $this->object = new Action('a-name', 'a-title', 'a-description');
  }

  public function testCheckDefaultIdIsNull() {
    $this->assertNull($this->object->getId());
  }

  public function testGetName() {
    $this->assertEquals('a-name', $this->object->getName());
  }

  public function testGetDescription() {
    $this->assertEquals('a-description', $this->object->getDescription());
  }

  public function testGetTitle() {
    $this->assertEquals('a-title', $this->object->getTitle());
  }
}