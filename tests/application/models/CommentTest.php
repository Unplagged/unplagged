<?php

require_once('../application/models/Comment.php');

class Application_Model_CommentTest extends ControllerTestCase {

  protected $object;

  public function setUp() {
    parent::setUp();
    $this->object = new Application_Model_Comment(array('author'=>'an-author', 'source'=>'a-source', 'title'=>'a-title', 'text'=>'the-text'));
  }

  public function testStandardIdIsNullByDefault() {
    $this->assertNull($this->object->getId());
  }

  public function testGetDirectName(){
   $this->assertEquals('', $this->object->getDirectName());
  }
  
  public function testGetDirectLink(){
    $this->assertEquals('', $this->object->getDirectLink());
  }
  
  public function testGetAuthor(){
    $this->assertEquals('an-author', $this->object->getAuthor());
  }
  
  public function testGetSource(){
    $this->assertEquals('a-source', $this->object->getSource());
  }
  
  public function testGetText(){
    $this->assertEquals('the-text', $this->object->getText());
  }
  
  public function testGetTitle(){
    $this->assertEquals('a-title', $this->object->getTitle());
  }
}