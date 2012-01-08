<?php

require_once('../application/models/user/State.php');

class Application_Model_User_StateTest extends ControllerTestCase {
    
    protected $state;
        
    public function setUp()
    {
        parent::setUp();                
        $this->state = new Application_Model_User_State(array(
          'title'=>'Titel'
          , 'description'=>'Beschreibung'
        ));
    }
    
    public function testStandardIdIsNull()
    {
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
}