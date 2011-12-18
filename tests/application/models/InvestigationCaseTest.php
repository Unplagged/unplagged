<?php

/**
 * File for class {@link CaseTest}.
 */
require_once '../application/models/InvestigationCase.php';

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class InvestigationCaseTest extends PHPUnit_Framework_TestCase{

  public function testCaseCanHaveName(){
    $case = new Application_Model_InvestigationCase('AName', 'SomeLabel');

    $this->assertEquals('AName', $case->getName());
  }

  public function testCaseNameCanHaveWhitespace(){
    $case = new Application_Model_InvestigationCase('Another Name', 'SomeLabel');
    $this->assertEquals('Another Name', $case->getName());
  }

  public function testCaseCanHaveAlias(){
    $case = new Application_Model_InvestigationCase('SomeName', 'alias');
    $this->assertEquals('alias', $case->getAlias());
  }

  public function testCaseNameCanBeDifferentThanAlias(){
    $case = new Application_Model_InvestigationCase('SomeName', 'alias');
    
    $this->assertNotEquals($case->getName(), $case->getAlias());
  }
  
  //not implemented yet
  /*public function testCaseCanHaveState()
  {
    $case = new Application_Model_InvestigationCase('Name', 'alias', 'investigation');
    
    $this->assertEquals('investigation', $case->getState());
  }*/
}

?>
