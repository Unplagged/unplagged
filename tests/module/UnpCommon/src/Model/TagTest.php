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
namespace UnpCommonTest\Model;

use PHPUnit_Framework_TestCase;
use UnpCommon\Model\Tag;

class TagTest extends PHPUnit_Framework_TestCase {

  protected $object;

  public function setUp() {
    $this->object = new Tag(array('title'=>'a-title'));
  }

  public function testStandardIdIsNullByDefault() {
    $this->assertNull($this->object->getId());
  }

  public function testGetDirectName(){
    $this->assertEquals('tag', $this->object->getDirectName());
  }
  
  public function testGetDirectLink(){
    $this->assertEquals('/tag/show/id/', $this->object->getDirectLink());
  }
  
  public function testTitleIsChangeable(){
    $this->object->setTitle('a-different-title');
    $this->assertEquals('a-different-title', $this->object->getTitle());
  }
  
  public function testGetIconClass(){
    $this->assertEquals('icon-tag', $this->object->getIconClass());
  }
}
