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

use \PHPUnit_Framework_TestCase;
use \UnpCommon\Model\Document;
use \UnpCommon\Model\Document\Fragment;
use \UnpCommon\Model\Document\Page;
use \UnpCommon\Model\File;

/**
 * 
 */
class DocumentTest extends PHPUnit_Framework_TestCase{

  private $document;

  protected function setUp(){
    $file = new File();
    $this->document = new Document('the-title', $file);
  }

  public function testGetIconClass(){
    $this->assertEquals('fam-icon-book', $this->document->getIconClass());
  }

  public function testToArray(){
    $this->assertInternalType('array', $this->document->toArray());
  }

  public function testToArrayWithPage(){
    $page = new Page($this->document);
    $this->document->addPage($page);
    $documentArray = $this->document->toArray();
    $this->assertEquals(1, count($documentArray['pages']));
  }

  public function testGetDirectName(){
    $this->assertEquals('the-title', $this->document->getDirectName());
  }

  public function testGetDirectLink(){
    $this->assertEquals('/document_page/list/id/', $this->document->getDirectLink());
  }

  public function testGetSidebarActions(){
    $this->assertInternalType('array', $this->document->getSidebarActions());
  }

  public function testGetLanguage(){
    $this->document->setLanguage('de');
    $this->assertEquals('de', $this->document->getLanguage());
  }

  public function testTitleCanBeSet(){
    $this->document->setTitle('the-new-title');

    $this->assertEquals('the-new-title', $this->document->getTitle());
  }

  public function testBibliographicInformation(){
    $this->assertInstanceOf('\UnpCommon\Model\BibliographicInformation', $this->document->getBibliographicInformation());
  }

  public function testPageCanBeAdded(){
    $page = new Page($this->document);
    $this->document->addPage($page);
    $this->assertEquals(1, count($this->document->getPages()));
  }

  public function testGetPageNumber(){
    $page = new Page($this->document);
    $page2 = new Page($this->document);
    $this->document->addPage($page);
    $this->document->addPage($page2);
    $this->assertEquals(2, $this->document->getPageNumber($page2));
  }
  
  public function testFragmentCanBeAdded(){
    $fragment = new Fragment();
    $this->document->addFragment($fragment);
    $this->assertEquals(1, count($this->document->getFragments()));
  }
  
  public function testGetSourceFile(){
    $this->assertInstanceOf('\UnpCommon\Model\File', $this->document->getSourceFile());
  }
  
  public function testGetCase(){
    $case = new \UnpCommon\Model\PlagiarismCase();
    $this->assertNull($this->document->getCase());
    $this->document->setCase($case);
    $this->assertInstanceOf('\UnpCommon\Model\PlagiarismCase', $this->document->getCase());
  }

}
