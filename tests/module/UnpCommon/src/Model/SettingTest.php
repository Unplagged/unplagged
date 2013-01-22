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

/**
 * 
 */
class SettingTest extends PHPUnit_Framework_TestCase {

  protected $object;

  protected function setUp() {
    $this->object = new \UnpCommon\Model\Setting('a-key', 'a-value', 'a-label');
  }

  public function testGetSettingKey() {
    $this->assertEquals('a-key', $this->object->getSettingKey());
  }

  public function testGetValue() {
    $this->assertEquals('a-value', $this->object->getValue());
  }

  public function testGetLabel() {
    $this->assertEquals('a-label', $this->object->getLabel());
  }

  public function testLabelCanBeChanged() {
    $this->object->setLabel('a-different-label');
    $this->assertEquals('a-different-label', $this->object->getLabel());
  }

  public function testValueCanBeChanged() {
    $this->object->setValue('a-different-value');
    $this->assertEquals('a-different-value', $this->object->getValue());
  }

}