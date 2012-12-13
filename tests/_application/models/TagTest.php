<?php

require_once('../application/models/Tag.php');

class Application_Model_TagTest extends ControllerTestCase {

  protected $object;

  public function setUp() {
    parent::setUp();
    $this->object = new Application_Model_Tag(array('title'=>'a-title'));
  }

  public function testStandardIdIsNullByDefault() {
    $this->assertNull($this->object->getId());
  }

  public function testGetDirectName(){
    $this->assertEquals('tag', $this->object->getDirectName());
  }
  
  public function testGetDirectLink(){
    $this->assertEquals('/tag/show/id/', $this->object->getDirectLink());
  }
  
  public function testTitleIsChangeable(){
    $this->object->setTitle('a-different-title');
    $this->assertEquals('a-different-title', $this->object->getTitle());
  }
}
