<?php

require_once ('../application/models/User.php');

class Application_Model_UserTest extends ControllerTestCase {
    
    private $user;
        
    public function setUp()
    {
        parent::setUp();                
        $this->user = new Application_Model_User();
    }

    public function testUserCanHaveFirstname() {
        $testFirstname = "Benjamin";
        $this->user->setFirstname($testFirstname);

        $this->assertEquals($this->user->getFirstname(), $testFirstname);
    }
    
    public function testUserCanHaveLasttname() {
        $testLastname = "Mustermann";
        $this->user->setLastname($testLastname);

        $this->assertEquals($this->user->getLastname(), $testLastname);
    }
    
    public function testUserCanHaveState() {
        $testState = new Application_Model_User_State();
        $this->user->setState($testState);

        $this->assertEquals($this->user->getState(), $testState);
    }
    
    public function testConstructor() {
        $data["username"] = "benjamin";
        $data["password"] = "password";
        $data["email"] = "email@email.de";
        $data["firstname"] = "Hannah";
        $data["lastname"] = "Mustermann";
        $data["verificationHash"] = "123jkh123jk124dsfnbm23";
        $data["state"] = new Application_Model_User_State();
        
        $this->user = new Application_Model_User($data);
        
        $this->assertEquals($this->user->getUsername(), $data["username"]);
        $this->assertEquals($this->user->getPassword(), $data["password"]);
        $this->assertEquals($this->user->getEmail(), $data["email"]);
        $this->assertEquals($this->user->getFirstname(), $data["firstname"]);
        $this->assertEquals($this->user->getLastname(), $data["lastname"]);
        $this->assertEquals($this->user->getVerificationHash(), $data["verificationHash"]);
        $this->assertEquals($this->user->getState(), $data["state"]);
    }
}

?>