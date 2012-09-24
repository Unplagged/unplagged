<?php

require_once '../application/models/Base.php';
require_once '../application/models/User.php';

class Application_Model_UserTest extends ControllerTestCase{

  private $user;

  public function setUp(){
    parent::setUp();
    
    $data = array();
    $data["username"] = "benjamin";
    $data["password"] = "password";
    $data["email"] = "email@email.de";
    $data["firstname"] = "Hannah";
    $data["lastname"] = "Mustermann";
    $data["verificationHash"] = "123jkh123jk124dsfnbm23";
    $data["state"] = new Application_Model_State();
    
    $this->user = new Application_Model_User($data);
  }

  public function testUserCanHaveFirstname(){
    $testFirstname = "Benjamin";
    $this->user->setFirstname($testFirstname);

    $this->assertEquals($this->user->getFirstname(), $testFirstname);
  }

  public function testUserCanHaveLastname(){
    $testLastname = "Mustermann";
    $this->user->setLastname($testLastname);

    $this->assertEquals($this->user->getLastname(), $testLastname);
  }

  public function testUserCanHaveState(){
    $testState = new Application_Model_State();
    $this->user->setState($testState);

    $this->assertEquals($this->user->getState(), $testState);
  }

}