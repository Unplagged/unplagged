<?php
/**
 * File for class {@link DocumentTest}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class DocumentTest extends PHPUnit_Framework_TestCase{
  
  public function testStandardIdIsNull(){
    $document = new Application_Model_Document();
    
    $this->assertNull($document->getId());
  }
  
  public function testOriginalDataCanBeAccessed(){
    $document = new Application_Model_Document(array('originalData'));
    
    $this->assertEquals('originalData', $document->getOriginalData());
  }
  
  //public function test
  
}
?>
