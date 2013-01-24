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
namespace UnpCommonTest;

use PHPUnit_Framework_TestCase;
use UnpCommon\Language;

/**
 * 
 */
class LanguageTest extends PHPUnit_Framework_TestCase{
  private $object;

  protected function setUp(){
    $this->object = new Language();
  }
  
  public function testValidLanguageCode(){
    $this->assertTrue($this->object->isValidLanguageCode('de'));
  }
  
  public function testInvalidLanguageCode(){
    $this->assertFalse($this->object->isValidLanguageCode('asdf'));
  }
  
  public function testgetAllLanguageCodes(){
    $this->assertInternalType('array', $this->object->getLanguages());
  }
  
  public function testGetLanguageName(){
    $this->assertEquals('German', $this->object->getLanguageName('de'));
  }
  
  public function testGetInvalidLanguageName(){
    $this->assertEquals('', $this->object->getLanguageName('adfg'));
  }
}