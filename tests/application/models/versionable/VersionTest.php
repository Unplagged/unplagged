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

require_once '../application/models/Base.php';
require_once '../application/models/Versionable.php';
require_once '../application/models/Versionable/Version.php';
require_once '../application/models/Document/Page.php';


/**
 * 
 */
class VersionTest extends PHPUnit_Framework_TestCase {
  
  private $object;
  
  public function setUp(){
    $versionable = new Application_Model_Document_Page();
    $this->object = new Application_Model_Versionable_Version($versionable);
  }
  
  public function testGetDirectName(){
    $this->assertEquals('', $this->object->getDirectName());
  }
  
  public function testGetDirectLink(){
    $this->assertEquals('', $this->object->getDirectLink());
  }
  
  public function testVersionableCanBeChanged(){
    $newVersionable = new Application_Model_Document_Page();
    $this->object->setVersionable($newVersionable);
    
    $this->assertEquals($newVersionable, $this->object->getVersionable());
  }
  
  public function testGetVersion(){
    $this->assertEquals(1, $this->object->getVersion());
  }
  
  public function testGetData(){
    $this->assertInternalType('array', $this->object->getData());
  }
  
  public function testToArray(){
    $this->assertNull($this->object->toArray());
  }
}