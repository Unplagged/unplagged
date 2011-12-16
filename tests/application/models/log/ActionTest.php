<?php

require_once('../application/models/log/Action.php');

class Application_Model_Log_ActionTest extends ControllerTestCase{

  protected $action;

  public function setUp(){
    parent::setUp();
    $this->action = new Application_Model_Log_Action(array(
          'module'=>'registration'
          , 'title'=>'Titel'
          , 'description'=>'Beschreibung'
        ));
  }

  public function testStandardIdIsNull(){
    $this->assertNull($this->action->getId());
  }

  public function testTitleWasSet(){
    $testTitle = "Titel";

    $this->assertEquals($this->action->getTitle(), $testTitle);
  }

  public function testSetDescription(){
    $testDescription = "Beschreibung";

    $this->assertEquals($this->action->getDescription(), $testDescription);
  }

  public function testModuleWasSet(){
    $testModule = "registration";

    $this->assertEquals($this->action->getModule(), $testModule);
  }

}