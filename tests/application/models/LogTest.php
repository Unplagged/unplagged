<?php

require_once('../application/models/Log.php');
require_once('../application/models/log/Action.php');
require_once('../application/models/User.php');

class Application_Model_LogTest extends ControllerTestCase {
    
    protected $log;
        
    public function setUp()
    {
        parent::setUp();                
        $this->log = new Application_Model_Log();
    }

    public function testSetId() {
        $testId = "1";
        $this->log->setId($testId);

        $this->assertEquals($this->log->getId(), $testId);
    }
    
    public function testSetAction() {
        $testAction = new Application_Model_Log_Action();
        $this->log->setAction($testAction);

        $this->assertEquals($this->log->getAction(), $testAction);
    }
    
    public function testSetUser() {
        $testUser = new Application_Model_User();
        $this->log->setUser($testUser);

        $this->assertEquals($this->log->getUser(), $testUser);
    }
    
    public function testSetComment() {
        $testComment = "Passwort";
        $this->log->setComment($testComment);

        $this->assertEquals($this->log->getComment(), $testComment);
    }
        
    public function testCreated() {
        $this->log->created();
    }
    
    public function testgetCreated() {
        $this->log->getCreated();
    }
}