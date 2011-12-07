<?php

require_once ('../application/models/User.php');

class Application_Model_UserTest extends ControllerTestCase {
    
    protected $user;
        
    public function setUp()
    {
        parent::setUp();                
        $this->user = new Application_Model_User();
    }

    public function testSetFirstname() {
        $testFirstname = "Benjamin";
        $this->user->setFirstname($testFirstname);

        $this->assertEquals($this->user->getFirstname(), $testFirstname);
    }
    
    public function testSetLasttname() {
        $testLastname = "Mustermann";
        $this->user->setLastname($testLastname);

        $this->assertEquals($this->user->getLastname(), $testLastname);
    }
    
    public function testSetUsername() {
        $testUsername = "benjamino";
        $this->user->setUsername($testUsername);

        $this->assertEquals($this->user->getUsername(), $testUsername);
    }
    
    public function testSetPassword() {
        $testPassword = "Passwort";
        $this->user->setPassword($testPassword);

        $this->assertEquals($this->user->getPassword(), $testPassword);
    }
}

?>
