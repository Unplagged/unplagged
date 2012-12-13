<?php

/**
 * File for class {@link CaseTest}.
 */
require_once '../application/models/Base.php';
require_once '../application/models/Case.php';

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class CaseTest extends PHPUnit_Framework_TestCase {

  private $case;

  public function setUp() {
    parent::setUp();

    $this->case = new Application_Model_Case(array('name' => 'SomeName', 'alias' => 'alias', 'requiredFragmentRatings' => array()));
  }

  public function testCaseCanHaveName() {
    $case = new Application_Model_Case(array('name' => 'AName', 'label' => 'SomeLabel', 'alias' => 'alias'));

    $this->assertEquals('AName', $case->getName());
  }

  public function testCaseNameCanHaveWhitespace() {
    $case = new Application_Model_Case(array('name' => 'Another Name', 'label' => 'SomeLabel'));
    $this->assertEquals('Another Name', $case->getName());
  }

  public function testCaseCanHaveAlias() {
    $case = new Application_Model_Case(array('name' => 'SomeName', 'alias' => 'alias'));
    $this->assertEquals('alias', $case->getAlias());
  }

  public function testCaseNameCanBeDifferentThanAlias() {
    $this->assertNotEquals($this->case->getName(), $this->case->getAlias());
  }

  public function testUpdatedIsDateTime() {
    $this->case->updated();

    $this->assertInstanceOf('DateTime', $this->case->getUpdated());
  }

  public function testCreatedIsDateTime() {
    $this->case->created();

    $this->assertInstanceOf('DateTime', $this->case->getCreated());
  }

  public function testGetDirectLink() {
    $this->assertEquals('/case/list', $this->case->getDirectLink());
  }

  public function testNameCanBeChanged() {
    $this->case->setName('the-new-name');

    $this->assertEquals('the-new-name', $this->case->getName());
  }

  public function testTargetCanBeChanged() {
    $this->case->setTarget('the-new-target');

    $this->assertEquals('the-new-target', $this->case->getTarget());
  }

  public function testDefaultRoleCanBeChanged() {
    $role = new Application_Model_User_InheritableRole();
    $this->case->addDefaultRole($role);

    $this->assertTrue($this->case->hasDefaultRole($role));
  }

  public function testAliasCanBeChanged() {
    $this->case->setAlias('the-new-alias');

    $this->assertEquals('the-new-alias', $this->case->getAlias());
  }

  public function testRequiredFragmentRatingsCanBeSet() {
    $this->case->setRequiredFragmentRatings('the-new-rating');

    $this->assertEquals('the-new-rating', $this->case->getRequiredFragmentRatings());
  }

  public function testGetPublishableName() {
    $this->assertEquals($this->case->getAlias(), $this->case->getPublishableName());
  }

  public function testGetDirectName() {
    $this->assertEquals('SomeName', $this->case->getDirectName());
  }

  public function testFilesCanBeCleared() {
    $file = new Application_Model_File();
    $this->case->addFile($file);

    $this->case->clearFiles();

    $this->assertFalse($this->case->hasFile($file));
  }

  public function testSingleFileCanBeRemoved() {
    $file = new Application_Model_File();
    $this->case->addFile($file);

    $file2 = new Application_Model_File();
    $this->case->addFile($file2);

    $this->case->removeFile($file2);

    $this->assertTrue($this->case->hasFile($file));
    $this->assertFalse($this->case->hasFile($file2));
  }

  public function testGetFiles() {
    $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection', $this->case->getFiles());
  }

  public function testToArray() {
    $this->assertInternalType('array', $this->case->toArray());
  }

  public function testDocumentCanBeAdded() {
    $document = new Application_Model_Document();
    $this->case->addDocument($document);

    $this->assertEquals(1, $this->case->getDocuments()->count());
  }

  public function testCollaboratorsCanBeAdded() {
    $user = new Application_Model_User();
    $this->case->addCollaborator($user);

    $this->assertEquals(1, $this->case->getCollaborators()->count());
  }

  public function testCollaboratorCanBeRemoved() {
    $user = new Application_Model_User();
    $this->case->addCollaborator($user);

    $this->case->removeCollaborator($user);
    $this->assertEquals(0, $this->case->getCollaborators()->count());
  }

  public function testCollaboratorsCanBeCleared() {
    $user = new Application_Model_User();
    $this->case->addCollaborator($user);

    $this->case->clearCollaborators();
    $this->assertEquals(0, $this->case->getCollaborators()->count());
  }

  public function testCollaboratorIdsCanBeRetrieved() {
    $user = new Application_Model_User();
    $this->case->addCollaborator($user);

    $this->assertInternalType('array', $this->case->getCollaboratorIds());
  }

  public function testReportCanBeAdded() {
    $user = new Application_Model_User();
    $report = new Application_Model_Report(array(), $user, $this->case);
    $this->case->addReport($report);

    $this->assertEquals(1, $this->case->getReports()->count());
  }

  public function testBarcodeHasCorrectType() {
    $document = new Application_Model_Document();
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
    $document = new Application_Model_Document();
    $page = new Application_Model_Document_Page();
    $document->addPage($page);
    $this->case->setTarget($document);
    $this->case->updateBarcodeData();

    $this->assertEquals(0.0, $this->case->getPlagiarismPercentage());
  }

  public function testSetCollaborators(){
    $case = new Application_Model_Case(array('name' => 'SomeName', 'alias' => 'alias', 'requiredFragmentRatings' => array()));
    $user = new Application_Model_User();
    $case->addCollaborator($user);
    $case->setCollaborators(array(1));
  }
}