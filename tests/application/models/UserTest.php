<?php

require_once ('../application/models/User.php');

class Application_Model_UserTest extends ControllerTestCase {
    
    protected $user;
        
    public function setUp()
    {
        parent::setUp();                
        $this->user = new Application_Model_User();
    }

    public function testSetId() {
        $testId = "1";
        $this->user->setId($testId);

        $this->assertEquals($this->user->getId(), $testId);
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
    
    public function testSetEmail() {
        $testEmail = "mail@domain.tld";
        $this->user->setEmail($testEmail);

        $this->assertEquals($this->user->getEmail(), $testEmail);
    }
    
    public function testSetVerificationHash() {
        $testVerificationHash = "123jhkjh1234h42nmbrmen24";
        $this->user->setVerificationHash($testVerificationHash);

        $this->assertEquals($this->user->getVerificationHash(), $testVerificationHash);
    }
    
    public function testSetState() {
        $testState = "123jhkjh1234h42nmbrmen24";
        $this->user->setState($testState);

        $this->assertEquals($this->user->getState(), $testState);
    }
    
    public function testCreated() {
        $this->user->created();
    }
    
    public function testUpdated() {
        $this->user->updated();
    }
}