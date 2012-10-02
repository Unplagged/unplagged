<?php

require_once '../application/models/Base.php';
require_once '../application/models/User.php';

class Application_Model_UserTest extends ControllerTestCase{

  private $user;

  public function setUp(){
    parent::setUp();
    
    $data = array();
    $data['role'] = new Application_Model_User_GuestRole();
    $data["username"] = "benjamin";
    $data["password"] = "password";
    $data['avatar'] = 'the-avatar';
    $data["email"] = "email@example.com";
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

  public function testPasswordCanBeChanged(){
    $this->user->setPassword('the-new-password');
    
    $this->assertEquals('the-new-password', $this->user->getPassword());
  }
  
  public function testUpdatedChangesDate(){
    $oldUpdated = $this->user->getUpdated();
    $this->user->updated();
    
    $this->assertNotSame($oldUpdated, $this->user->getUpdated());
  }
  
  public function testAvatarCanBeChanged(){
    $this->user->setAvatar('the-new-avatar');
    
    $this->assertEquals('the-new-avatar', $this->user->getAvatar());
  }
  
  public function testGetDirectLinkIsTheUsername(){
    $this->assertEquals($this->user->getUsername(), $this->user->getDirectName());
  }
  
  public function testGetDirectLink(){
    $this->assertEquals('/user/show/id/', $this->user->getDirectLink());
  }
  
  public function testVerificationHashCanBeChanged(){
    $this->user->setVerificationHash('the-new-hash');
    
    $this->assertEquals('the-new-hash', $this->user->getVerificationHash());
  }
  
  public function testGetEmail(){
    $this->assertEquals('email@example.com', $this->user->getEmail());
  }
  
  public function testGetSettings(){
    $this->assertNull($this->user->getSettings());
  }
  
  public function testFileCanBeAdded(){
    $file = new Application_Model_File();
    $this->user->addFile($file);
    
    $this->assertTrue($this->user->hasFile($file));
  }
  
  public function testDefaultFileCountIsZero(){
    $this->assertFalse($this->user->hasFiles());
  }
  
  public function testCurrentCaseCanBeChanged(){
    $newCase = new Application_Model_Case();
    $this->user->setCurrentCase($newCase);
    
    $this->assertEquals($newCase, $this->user->getCurrentCase());
  }
  
  public function testFilesCanBeCleared(){
    $file = new Application_Model_File();
    $this->user->addFile($file);
    
    $this->user->clearFiles();
    
    $this->assertFalse($this->user->hasFiles());
  }
  
  public function testSingleFileCanBeRemoved(){
    $file = new Application_Model_File();
    $this->user->addFile($file);
    
    $file2 = new Application_Model_File();
    $this->user->addFile($file2);
    
    $this->user->removeFile($file2);
    
    $this->assertTrue($this->user->hasFile($file));
    $this->assertFalse($this->user->hasFile($file2));
  }
  
  public function testGetFiles(){
    $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection', $this->user->getFiles());
  }
}