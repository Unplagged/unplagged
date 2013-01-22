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
use UnpCommon\Model\Document;
use UnpCommon\Model\PlagiarismCase;
use UnpCommon\Model\Report;
use UnpCommon\Model\User;

/**
 * Description of ReportTest
 *
 */
class ReportTest extends PHPUnit_Framework_TestCase{

  private $object;

  public function setUp(){
    $user = new User();
    $document = new Document();
    $case = new PlagiarismCase();
    $this->object = new Report($user, $case, $document, 'the-title');
    array(
        'title'=>'the-title', 
        'filePath'=>'file-path', 
        'reportTitle'=>'the-report-title', 
        'reportGroupName'=>'the-report-group-name', 
        'reportIntroduction'=>'the-introduction', 
        'reportEvaluation'=>'the-evaluation');
  }

  public function testGetName(){
    $this->assertEquals('the-title', $this->object->getName());
  }
  
  public function testCreatorIsAlwaysTypeUser(){
    $this->assertInstanceOf('\UnpCommon\Model\User', $this->object->getCreator());
  }

  public function testTargetIsAlwaysTypeDocument(){
    $this->assertInstanceOf('\UnpCommon\Model\Document', $this->object->getTargetDocument());
  }

  public function testGetDirectLink(){
    $this->assertEquals('/report/list/id/', $this->object->getDirectLink());
  }

  public function testCaseIsAlwaysTypeCase(){
    $this->assertInstanceOf('\UnpCommon\Model\PlagiarismCase', $this->object->getCase());
  }

  public function testGetDirectName(){
    $this->assertEquals('the-title', $this->object->getDirectName());
  }
  
  public function testGetIconClass(){
    $this->assertEquals('icon-report', $this->object->getIconClass());
  }
  
  public function testFileCanBeChanged(){
    $file = new \UnpCommon\Model\File();
    $this->object->setFile($file);
    $this->assertEquals($file, $this->object->getFile());
  }

  public function testParameterCanBeSetAndUnset(){
    $this->object->setParameter('testParameter', 'the-value');
    
    $this->assertEquals('the-value', $this->object->getParameter('testParameter'));
    
    $this->object->setParameter('testParameter');
    
    $this->assertNull($this->object->getParameter('testParameter'));
  }
  
}