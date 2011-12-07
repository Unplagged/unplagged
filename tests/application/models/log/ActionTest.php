<?php

require_once('../application/models/log/Action.php');

class Application_Model_Log_ActionTest extends ControllerTestCase {
    
    protected $action;
        
    public function setUp()
    {
        parent::setUp();                
        $this->action = new Application_Model_Log_Action();
    }

    public function testSetId() {
        $testId = "1";
        $this->action->setId($testId);

        $this->assertEquals($this->action->getId(), $testId);
    }
    
    public function testSetTitle() {
        $testTitle = "Titel";
        $this->action->setTitle($testTitle);

        $this->assertEquals($this->action->getTitle(), $testTitle);
    }
        
    public function testSetDescription() {
        $testDescription = "Beschreibung";
        $this->action->setDescription($testDescription);

        $this->assertEquals($this->action->getDescription(), $testDescription);
    }
    
    public function testSetModule() {
        $testModule = "registration";
        $this->action->setModule($testModule);

        $this->assertEquals($this->action->getModule(), $testModule);
    }
}