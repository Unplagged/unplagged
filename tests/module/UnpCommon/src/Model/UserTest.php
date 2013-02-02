<?php

/*
 * Unplagged - The plagiarism detection cockpit.
 * Copyright (C) 2012 Unplagged
 *  
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *  
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace UnpCommonTest;

use PHPUnit_Framework_TestCase;
use UnpCommon\Model\User;

class Application_Model_UserTest extends PHPUnit_Framework_TestCase{

  private $user;

  public function setUp(){
    parent::setUp();
    
    $data = array();
    //$data['role'] = new Application_Model_User_GuestRole();
    $data["username"] = "benjamin";
    $data["password"] = "password";
    $data["email"] = "email@example.com";
    //$data["state"] = new Application_Model_State();
    
    $this->user = new \UnpCommon\Model\User($data);
  }

  /*public function testUserCanHaveState(){
    $testState = new Application_Model_State();
    $this->user->setState($testState);

    $this->assertEquals($this->user->getState(), $testState);
  }*/

  public function testPasswordCanBeChanged(){
    $this->user->setPassword('the-new-password');
    
    $this->assertEquals('the-new-password', $this->user->getPassword());
  }
  
  public function testUpdatedChangesDate(){
    $oldUpdated = $this->user->getUpdated();
    $this->user->updated();
    
    $this->assertNotSame($oldUpdated, $this->user->getUpdated());
  }
  
  public function testGetDirectLinkIsTheUsername(){
    $this->assertEquals($this->user->getUsername(), $this->user->getDirectName());
  }
  
  public function testGetDirectLink(){
    $this->assertEquals('/user/show/id/', $this->user->getDirectLink());
  }
  
  public function testGetEmail(){
    $this->assertEquals('email@example.com', $this->user->getEmail());
  }
  
  public function testFileCanBeAdded(){
    $file = new \UnpCommon\Model\File();
    $this->user->addFile($file);
    
    $this->assertTrue($this->user->containsFile($file));
  }
  
  public function testDefaultFileCountIsZero(){
    $this->assertFalse($this->user->hasFiles());
  }
  
  public function testCurrentCaseCanBeChanged(){
    $newCase = new \UnpCommon\Model\PlagiarismCase();
    $this->user->setCurrentCase($newCase);
    
    $this->assertEquals($newCase, $this->user->getCurrentCase());
  }
  
  public function testSingleFileCanBeRemoved(){
    $file = new \UnpCommon\Model\File();
    $this->user->addFile($file);
    
    $file2 = new \UnpCommon\Model\File();
    $this->user->addFile($file2);
    
    $this->user->removeFile($file2);
    
    $this->assertTrue($this->user->containsFile($file));
    $this->assertFalse($this->user->containsFile($file2));
  }
  
  public function testGetFiles(){
    $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection', $this->user->getFiles());
  }
  
  /**
   * Makes sure that the display name doesn't work as it 
   * is just to fit the interface.
   */
  public function testDisplaynameCantBeSet(){
    $this->user->setDisplayName('name');
    
    $this->assertNull($this->user->getDisplayName());
  }
  
  /**
   * Makes sure that the display name doesn't work as it 
   * is just to fit the interface.
   */
  public function testIdCantBeSet(){
    $this->user->setId('12345678');
    
    $this->assertNull($this->user->getId());
  }
  
  public function testCorrectIconClass(){
    $this->assertEquals('fam-icon-user', $this->user->getIconClass());
  }
  
  public function testUsernameCanBeChanged(){
    $this->user->setUsername('the-new-username');
    
    $this->assertEquals('the-new-username', $this->user->getUsername());
  }
  
  public function testEmailCanBeChanged(){
    $this->user->setEmail('the-new-email');
    
    $this->assertEquals('the-new-email', $this->user->getEmail());
  }
  
  public function testToArray(){
    $this->assertInternalType('array', $this->user->toArray());
  }
  
  public function testSetState(){
    $this->user->setState('the-new-state');
    
    $this->assertEquals('the-new-state', $this->user->getState());
  }
  
  public function testGetRoleIsNotImplementedYet(){
    $this->assertNull($this->user->getRole());
  }
}