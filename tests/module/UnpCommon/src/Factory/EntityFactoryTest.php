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

use UnpCommon\Factory\EntityFactory;
use UnplaggedTest\Bootstrap;

/**
 * 
 */
class EntityFactoryTest extends \PHPUnit_Framework_TestCase{
  
  /**
   * @expectedException \InvalidArgumentException
   */
  public function testCreateBaseEntityWithoutValidClass(){
    $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
    EntityFactory::createBaseEntity($em);
  }
  
}