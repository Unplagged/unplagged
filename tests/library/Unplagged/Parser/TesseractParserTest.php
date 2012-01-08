<?php
/**
 * File for class {@link TesseractParserTest}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class TesseractParserTest extends PHPUnit_Framework_TestCase{
  
  public function testParseDocumentReturnsAlwaysDocument(){
    $tesseractParser = new Unplagged_Parser_TesseractParser();
    $document = $tesseractParser->parseToDocument();
    
    $this->assertType('Application_Model_Document', $document);
  }  
}
?>
