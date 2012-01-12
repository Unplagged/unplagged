<?php

/**
 * File for class {@link CaseTest}.
 */
require_once '../application/models/Case.php';

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class CaseTest extends PHPUnit_Framework_TestCase{

  private $case;
  
  public function setUp(){
    parent::setUp();
    
    $this->case = new Application_Model_Case('SomeName', 'alias');
  }
  
  public function testCaseCanHaveName(){
    $case = new Application_Model_Case('AName', 'SomeLabel');

    $this->assertEquals('AName', $case->getName());
  }

  public function testCaseNameCanHaveWhitespace(){
    $case = new Application_Model_Case('Another Name', 'SomeLabel');
    $this->assertEquals('Another Name', $case->getName());
  }

  public function testCaseCanHaveAlias(){
    $case = new Application_Model_Case('SomeName', 'alias');
    $this->assertEquals('alias', $case->getAlias());
  }

  public function testCaseNameCanBeDifferentThanAlias(){
    $this->assertNotEquals($this->case->getName(), $this->case->getAlias());
  }
  
  public function testUpdatedIsDateTime(){
    $this->case->updated();
    
    $this->assertInstanceOf('DateTime', $this->case->getUpdated());
  }
  
  public function testCreatedIsDateTime(){
    $this->case->created();
    
    $this->assertInstanceOf('DateTime', $this->case->getCreated());
  }
  
  //not implemented yet
  /*public function testCaseCanHaveState()
  {
    $case = new Application_Model_InvestigationCase('Name', 'alias', 'investigation');
    
    $this->assertEquals('investigation', $case->getState());
  }*/
}

?>
