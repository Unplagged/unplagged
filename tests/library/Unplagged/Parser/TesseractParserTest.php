<?php
/**
 * File for class {@link TesseractParserTest}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class TesseractParserTest extends PHPUnit_Framework_TestCase{
  
  public function testParseDocumentReturnsNullOnEmptyFile(){
    $tesseractParser = new Unplagged_Parser_TesseractParser();
    $document = $tesseractParser->parseToDocument(new Application_Model_File(), 'eng');
    
    $this->assertNull($document);
  }  
}
?>
