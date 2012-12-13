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

/**
 * 
 */
class DoctrineTest extends PHPUnit_Framework_TestCase{

  private $object;
  private $em;

  public function setUp(){
    //$this->em = $this->getEmMock();

    $this->object = new Unplagged_Auth_Adapter_Doctrine($this->em);
  }

  protected function getEmMock(){
    
    $emMock = $this->getMock('\Doctrine\ORM\EntityManager', array('getRepository', 'getClassMetadata', 'persist', 'flush'), array(), '', false);
    $emMock->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue(new FakeRepository()));
    $emMock->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue((object) array('name'=>'aClass')));
    $emMock->expects($this->any())
            ->method('persist')
            ->will($this->returnValue(null));
    $emMock->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(null));
    return $emMock;  // it tooks 13 lines to achieve mock!
  }

  public function testAuthenticate(){
    $this->assertNull(true);
  }

}