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
require_once '../application/models/BibTex.php';

/**
 * Description of BibtexTest
 */
class BibtexTest extends PHPUnit_Framework_TestCase {
  private $object;
  
  public function setUp(){ 
    $this->object = new Application_Model_BibTex(array());
  }
  
  public function testGetDirectLink(){
    $this->assertNull($this->object->getDirectLink());
  }
  
  public function testGetDirectName(){
    $this->assertNull($this->object->getDirectName());
  }
  
  public function testSourceTypeCanBeChanged(){
    $this->object->setSourceType('the-source-type');
    
    $this->assertEquals('the-source-type', $this->object->getSourceType());
  }
  
  public function testDocumentCanBeSet(){
    $document = new Application_Model_Document();
    $this->object->setDocument($document);
    
    $this->assertEquals($document, $this->object->getDocument());   
  }
  
  public function testEmptyFieldReturnsEmptyString(){
    $this->object->setContent(null, 'anEmptyField');
    
    $this->assertEquals('', $this->object->getContent('anEmptyField'));
  }
  
  public function testFieldCanBeSetAndRetrieved(){
    $this->object->setContent('the-value', 'aBibtexField');
    
    $this->assertEquals('the-value', $this->object->getContent('aBibtexField'));
  }
}