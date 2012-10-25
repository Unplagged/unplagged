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

require_once dirname(__FILE__) . '/../../../application/models/Action.php';

/**
 * Test class for Application_Model_Action.
 * Generated by PHPUnit on 2012-09-18 at 03:21:14.
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
    $this->object = new Application_Model_Action(array('name' => 'a-name', 'description' => 'a-description', 'title' => 'a-title'));
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

  public function testSetName() {
    $this->object->setName('a-new-name');
    $this->assertEquals('a-new-name', $this->object->getName());
  }

  public function testSetTitle() {
    $this->object->setTitle('a-new-title');
    $this->assertEquals('a-new-title', $this->object->getTitle());
  }

  public function testSetDescription() {
    $this->object->setDescription('a-new-description');
    $this->assertEquals('a-new-description', $this->object->getDescription());
  }
}