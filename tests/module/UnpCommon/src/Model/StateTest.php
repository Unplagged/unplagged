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
use UnpCommon\Model\State;

class StateTest extends PHPUnit_Framework_TestCase {

  protected $state;

  public function setUp() {
    parent::setUp();
    $this->state = new State(array(
                'title' => 'Titel',
                'name' => 'a-name',
                'description' => 'Beschreibung'
            ));
  }

  public function testStandardIdIsNullByDefault() {
    $this->assertNull($this->state->getId());
  }

  public function testTitleWasSet() {
    $testTitle = "Titel";

    $this->assertEquals($this->state->getTitle(), $testTitle);
  }

  public function testDescriptionWasSet() {
    $testDescription = "Beschreibung";

    $this->assertEquals($this->state->getDescription(), $testDescription);
  }
  
  public function testGetName(){
    $this->assertEquals('a-name', $this->state->getName());
  }
  
  public function testDescriptionIsChangeable(){
    $this->state->setDescription('a-different-description');
    $this->assertEquals('a-different-description', $this->state->getDescription());
  }
  
  public function testTitleIsChangeable(){
    $this->state->setTitle('a-different-title');
    $this->assertEquals('a-different-title', $this->state->getTitle());
  }
}