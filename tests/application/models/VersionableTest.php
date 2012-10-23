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
class VersionableTest extends PHPUnit_Framework_TestCase {
  
  private $object;
  
  public function setUp() {
    $stub = $this->getMockForAbstractClass('Application_Model_Versionable');
    $stub->expects($this->any())
            ->method('toVersionArray')
            ->will($this->returnValue(TRUE));
    $this->object = $stub;
  }
  
  public function testVersionIsChangeable(){
    $this->object->setVersion(1.0);
    $this->assertEquals(2.0, $this->object->getCurrentVersion());
  }
  
  public function testGetAuditLog(){
    $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $this->object->getAuditLog());
  }
}