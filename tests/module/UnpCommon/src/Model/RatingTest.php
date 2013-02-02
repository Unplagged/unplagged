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
use UnpCommon\Model\Rating;

/**
 *
 */
class RatingTest extends PHPUnit_Framework_TestCase {

  protected $object;

  protected function setUp() {
    $this->object = new Rating(null, null, 'the-title', 'the-comment');
  }
  
  public function testGetIconClass(){
    $this->assertEquals('fam-icon-star', $this->object->getIconClass());
  }
  
  public function testToArrayContainsCorrectType(){
    $array = $this->object->toArray();
    $this->assertEquals('rating', $array['type']);
  }
  
  public function testRatingCanBeSet(){
    $this->object->setRating(4);
    
    $this->assertEquals(4, $this->object->getRating());
  }
 
}