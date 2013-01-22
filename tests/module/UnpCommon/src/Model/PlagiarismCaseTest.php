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
use UnpCommon\Model\PlagiarismCase;

/**
 *
 */
class PlagiarismCaseTest extends PHPUnit_Framework_TestCase {

  private $case;

  public function setUp() {
    parent::setUp();

    $this->case = new PlagiarismCase(array('name' => 'SomeName', 'alias' => 'alias', 'requiredFragmentRatings' => array()));
  }

  public function testCaseCanHaveName() {
    $case = new PlagiarismCase(array('name' => 'AName', 'label' => 'SomeLabel', 'alias' => 'alias'));

    $this->assertEquals('AName', $case->getName());
  }

  public function testCaseNameCanHaveWhitespace() {
    $case = new PlagiarismCase(array('name' => 'Another Name', 'label' => 'SomeLabel'));
    $this->assertEquals('Another Name', $case->getName());
  }

  public function testCaseCanHaveAlias() {
    $case = new PlagiarismCase(array('name' => 'SomeName', 'alias' => 'alias'));
    $this->assertEquals('alias', $case->getAlias());
  }

  public function testCaseNameCanBeDifferentThanAlias() {
    $this->assertNotEquals($this->case->getName(), $this->case->getAlias());
  }

  public function testUpdatedIsDateTime() {
    $this->case->updated();

    $this->assertInstanceOf('\DateTime', $this->case->getUpdated());
  }

  public function testCreatedIsDateTime() {
    $this->case->created();

    $this->assertInstanceOf('\DateTime', $this->case->getCreated());
  }

  public function testGetDirectLink() {
    $this->assertEquals('/case/list', $this->case->getDirectLink());
  }

  public function testTargetCanBeAdded() {
    $document = new \UnpCommon\Model\Document();
    $this->case->addTargetDocument($document);

    $this->assertTrue($this->case->containsTargetDocument($document));
  }
/*
  public function testDefaultRoleCanBeChanged() {
    $role = new Application_Model_User_InheritableRole();
    $this->case->addDefaultRole($role);

    $this->assertTrue($this->case->hasDefaultRole($role));
  }
 */

  /*public function testRequiredFragmentRatingsCanBeSet() {
    $this->case->setRequiredFragmentRatings('the-new-rating');

    $this->assertEquals('the-new-rating', $this->case->getRequiredFragmentRatings());
  }*/

  public function testGetPublishableName() {
    $this->assertEquals($this->case->getAlias(), $this->case->getPublishableName());
  }

  public function testGetDirectName() {
    $this->assertEquals('alias', $this->case->getDirectName());
  }

  public function testSingleFileCanBeRemoved() {
    $file = new \UnpCommon\Model\File();
    $this->case->addFile($file);

    $file2 = new \UnpCommon\Model\File();
    $this->case->addFile($file2);

    $this->case->removeFile($file2);

    $this->assertTrue($this->case->containsFile($file));
    $this->assertFalse($this->case->containsFile($file2));
  }

  public function testGetFiles() {
    $this->assertInternalType('array', $this->case->getFiles());
  }

  public function testToArray() {
    $this->assertInternalType('array', $this->case->toArray());
  }

  public function testDocumentCanBeAdded() {
    $document = new \UnpCommon\Model\Document();
    $this->case->addDocument($document);

    $this->assertEquals(1, count($this->case->getDocuments()));
  }

  /*
  public function testCollaboratorsCanBeAdded() {
    $user = new \UnpCommon\Model\User();
    $this->case->addCollaborator($user);

    $this->assertEquals(1, count($this->case->getCollaborators()));
  }

  public function testCollaboratorCanBeRemoved() {
    $user = new \UnpCommon\Model\User();
    $this->case->addCollaborator($user);

    $this->case->removeCollaborator($user);
    $this->assertEquals(0, $this->case->getCollaborators()->count());
  }

  public function testCollaboratorIdsCanBeRetrieved() {
    $user = new \UnpCommon\Model\User();
    $this->case->addCollaborator($user);

    $this->assertInternalType('array', $this->case->getCollaboratorIds());
  }
  */

  public function testReportCanBeAdded() {
    $user = new \UnpCommon\Model\User();
    $report = new \UnpCommon\Model\Report($user, $this->case);
    $this->case->addReport($report);

    $this->assertEquals(1, count($this->case->getReports()));
  }
  
  public function testGetIconClass(){
    $this->assertEquals('icon-case', $this->case->getIconClass());
  }
  
  public function testGetTargetDocuments(){
    $this->assertInternalType('array', $this->case->getTargetDocuments());
  }

  /*
  public function testBarcodeHasCorrectType() {
    $document = new \UnpCommon\Model\Document();
    $page = new Application_Model_Document_Page();
    $document->addPage($page);
    $this->case->setTarget($document);
    $this->case->updateBarcodeData();

    $this->assertInstanceOf('Unplagged_Barcode', $this->case->getBarcode(20, 20, 30, true, 10));
  }

  public function testGetRoles() {
    $this->assertEquals($this->case->getRoles(), $this->case->getDefaultRoles());
  }
  

  public function testEmptyPlagiarismPercentage() {
    $this->assertEquals(0, $this->case->getPlagiarismPercentage());
  }

  public function testRealPlagiarismPercentage() {
    $document = new \UnpCommon\Model\Document();
    $page = new \UnpCommon\Model\Document\Page();
    $document->addPage($page);
    $this->case->setTarget($document);
    $this->case->updateBarcodeData();

    $this->assertEquals(0.0, $this->case->getPlagiarismPercentage());
  }
  public function testSetCollaborators(){
    $case = new \UnpCommon\Model\PlagiarismCase(array('name' => 'SomeName', 'alias' => 'alias', 'requiredFragmentRatings' => array()));
    $user = new Application_Model_User();
    $case->addCollaborator($user);
    $case->setCollaborators(array(1));
  }
*/
}