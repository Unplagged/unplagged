<?php
/**
 * File for class {@link CaseTest}.
 */

require_once ('../application/models/InvestigationCase.php');

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class InvestigationCaseTest extends PHPUnit_Framework_TestCase
{
  
  public function testCaseCanHaveName()
  {
    $case = new Application_Model_InvestigationCase('AName');
    
    $this->assertEquals('AName', $case->getName());
  }
  
  protected function testCaseNameCanHaveWhitespace()
  {
    //@todo
    $this->assertTrue(false);
  }
}

?>
