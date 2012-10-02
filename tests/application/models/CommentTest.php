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

require_once('../application/models/Comment.php');

class Application_Model_CommentTest extends PHPUnit_Framework_TestCase {

  private $object;
  private $document;
  private $author;

  public function setUp() {
    $this->document = new Application_Model_Document();
    $this->author = new Application_Model_User();
    $this->object = new Application_Model_Comment(array('author'=>$this->author, 'source'=>$this->document, 'title'=>'a-title', 'text'=>'the-text'));
  }

  public function testStandardIdIsNullByDefault() {
    $this->assertNull($this->object->getId());
  }

  public function testGetDirectName(){
   $this->assertEquals('', $this->object->getDirectName());
  }
  
  public function testGetDirectLink(){
    $this->assertEquals('/document_page/list/id/', $this->object->getDirectLink());
  }
  
  public function testGetAuthor(){
    $this->assertEquals($this->author, $this->object->getAuthor());
  }
  
  public function testGetSource(){
    $this->assertEquals($this->document, $this->object->getSource());
  }
  
  public function testGetText(){
    $this->assertEquals('the-text', $this->object->getText());
  }
  
  public function testGetTitle(){
    $this->assertEquals('a-title', $this->object->getTitle());
  }
  
  public function testToArrayAlwaysReturnsArray(){
    $this->object->created();
    $this->assertInternalType('array', $this->object->toArray());
  }
}