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
use \UnpCommon\Model\Activity;
use \UnpCommon\Model\Comment;
use \UnpCommon\Model\File;
use \UnpCommon\Model\User;

/**
 *
 */
class ActivityTest extends PHPUnit_Framework_TestCase {

  /**
   * @var Application_Model_Action
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp() {
    $user = new User();
    $target = new File();
    $result = new File();
    $this->object = new Activity('the-message', $user, 'the-self-message', 'the-target-message', $target, $result);
  }

  public function testGetDirectName() {
    $this->assertEquals('activity', $this->object->getDirectName());
  }
  
  public function testGetEmptyDirectLink() {
    $this->assertEquals('/notification/show/id/', $this->object->getDirectLink());
  }
  
  public function testGetIconClass(){
    $this->assertEquals('fam-icon-clock', $this->object->getIconClass());
  }

  public function testGetMessage() {
    $this->assertEquals('the-message', $this->object->getMessage());
  }

  public function testGetActorMessage() {
    $this->assertEquals('the-self-message', $this->object->getActorMessage());
  }
  
  public function testGetTargetMessage() {
    $this->assertEquals('the-target-message', $this->object->getTargetMessage());
  }
  
  public function testToArray(){
    $this->assertInternalType('array', $this->object->toArray());
  }
  
  public function testActorCanBeRetrieved(){
    $this->assertInstanceOf('\UnpCommon\Model\User', $this->object->getActor());
  }
  
  public function testResultCanBeFound(){
    $this->assertInstanceOf('\UnpCommon\Model\File', $this->object->getResult());
  }
  
  public function testTargetCanBeFound(){
    $this->assertInstanceOf('\UnpCommon\Model\File', $this->object->getTarget());
  }
}