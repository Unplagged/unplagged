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
    $tesseractParser = new Unplagged_Parser_Page_TesseractParser();
    $document = $tesseractParser->parseToPage(new Application_Model_File(), 'eng');
    
    $this->assertNull($document);
  }  
}
?>
