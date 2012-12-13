<?php
/**
 * File for class {@link DocumentTest}.
 */

require_once '../application/models/Base.php';
require_once '../application/models/Document.php';

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class DocumentTest extends PHPUnit_Framework_TestCase{
  
  public function testStandardIdIsNull(){
    $document = new Application_Model_Document();
    
    $this->assertNull($document->getId());
  }
  
}
?>
