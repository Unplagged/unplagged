<?php

/**
 * File for class {@link CaseControllerTest}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class CaseControllerTest extends ControllerTestCase{
  
  public function testCaseCreateDispatchesCorrectly(){
    $this->dispatch('/case/create');
    $this->assertController('case');
    $this->assertAction('create'); 
  }
}

?>
