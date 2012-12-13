<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../application/models/Base.php';
require_once '../application/models/Rating.php';

/**
 * 
 */
class RatingTest extends PHPUnit_Framework_TestCase {
  
  private $object;
  
  public function setUp(){
    $user = new Application_Model_User();
    $document = new Application_Model_Document();
    
    $this->object = new Application_Model_Rating(array('user'=>$user, 'source'=>$document, 'reason'=>'the-reason', 'rating'=>'the-rating'));
  }
  
  public function testGetSourceIsDocument(){
    $this->assertInstanceOf('Application_Model_Document', $this->object->getSource());
  }
  
  public function testGetDirectName(){
    $this->assertEquals('', $this->object->getDirectName());
  }
  
  public function testGetDirectLink(){
    $this->assertEquals('/document_page/list/id/', $this->object->getDirectLink());
  }
  
  public function testRatingCanBeSet(){
    $this->object->setRating('the-new-rating');
    
    $this->assertEquals('the-new-rating', $this->object->getRating());
  }
  
  public function testReasonCanBeSet(){
    $this->object->setReason('the-new-reason');
    
    $this->assertEquals('the-new-reason', $this->object->getReason());
  }
  
  public function testSourceCanBeSet(){
    $document = new Application_Model_Document();
    $this->object->setSource($document);
    
    $this->assertEquals($document, $this->object->getSource());
  }
  
  public function testToArray(){
    $this->object->created();
    $this->assertInternalType('array', $this->object->toArray());
  }
  
  public function testUserCanBeSet(){
    $user = new Application_Model_User();
    $this->object->setUser($user);
    
    $this->assertEquals($user, $this->object->getUser());
  }
}