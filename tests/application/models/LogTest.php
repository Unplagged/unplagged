<?php

require_once('../application/models/Log.php');
require_once('../application/models/log/Action.php');
require_once('../application/models/User.php');

class Application_Model_LogTest extends ControllerTestCase {
    
    private $log;
        
    public function setUp()
    {
        parent::setUp();                
        $this->log = new Application_Model_Log();
    }

    public function testConstructor() {
      
        $data["action"] = new Application_Model_Log_Action();
        $data["user"] = new Application_Model_User();
        $data["comment"] = "Hello World";
        
        $log = new Application_Model_Log($data);

        $this->assertEquals($log->getAction(), $data["action"]);
        $this->assertEquals($log->getUser(), $data["user"]);
        $this->assertEquals($log->getComment(), $data["comment"]);
    }
}