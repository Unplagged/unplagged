<?php

require_once('../application/models/State.php');

class Application_Model_StateTest extends ControllerTestCase {

  protected $state;

  public function setUp() {
    parent::setUp();
    $this->state = new Application_Model_State(array(
                'title' => 'Titel',
                'name' => 'a-name'
                , 'description' => 'Beschreibung'
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

  public function testNameIsChangeable(){
    $this->state->setName('a-different-name');
    $this->assertEquals('a-different-name', $this->state->getName());
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