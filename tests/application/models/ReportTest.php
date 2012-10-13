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

/**
 * Description of ReportTest
 *
 */
class ReportTest extends PHPUnit_Framework_TestCase {
  
  private $object;

  public function setUp(){
    $user = new Application_Model_User();
    $document = new Application_Model_Document();
    $case = new Application_Model_Case();
    $this->object = new Application_Model_Report(array('title'=>'the-title', 'filePath'=>'file-path', 'reportTitle'=>'the-report-title', 'reportGroupName'=>'the-report-group-name', 'reportIntroduction'=>'the-introduction', 'reportEvaluation'=>'the-evaluation'), $user, $case, $document);
  }
  
  public function testPercentageCanBeSet(){
    $this->object->setPercentage(100);
    
    $this->assertEquals(100, $this->object->getPercentage());
  }
  
  public function testServicenameCanBeSet(){
    $this->object->setServicename('the-servicename');
    
    $this->assertEquals('the-servicename', $this->object->getServicename());
  }
  
  public function testCreatorIsAlwaysTypeUser(){
    $this->assertInstanceOf('Application_Model_User', $this->object->getUser());
  }
  
  public function testTargetIsAlwaysTypeDocument(){
    $this->assertInstanceOf('Application_Model_Document', $this->object->getTarget());
  }
  
  public function testGetDirectLink(){
    $this->assertEquals('/report/list/id/', $this->object->getDirectLink());
  }
  
  public function testCaseIsAlwaysTypeCase(){
    $this->assertInstanceOf('Application_Model_Case', $this->object->getCase());
  }
  
  public function testCaseCanBeSet(){
    $case = new Application_Model_Case();
    $this->object->setCase($case);
    
    $this->assertEquals($case, $this->object->getCase());
  }
  
  public function testGetTitle(){
    $this->assertEquals('the-title', $this->object->getTitle());
  }
  
  public function testGetReportTitle(){
    $this->assertEquals('the-report-title', $this->object->getReportTitle());
  }
  
  public function testGetReportGroupName(){
    $this->assertEquals('the-report-group-name', $this->object->getReportGroupName());
  }
  
  public function testGetReportEvaluation(){
    $this->assertEquals('the-evaluation', $this->object->getReportEvaluation());
  }
  
  public function testGetReportIntroduction(){
    $this->assertEquals('the-introduction', $this->object->getReportIntroduction());
  }
  
  public function testGetDirectName(){
    $this->assertEquals('the-title', $this->object->getDirectName());
  }
  
  public function testFilepathCanBeSet(){
    $this->object->setFilePath('the-filepath');
    
    $this->assertEquals('the-filepath', $this->object->getFilePath());
  }
}