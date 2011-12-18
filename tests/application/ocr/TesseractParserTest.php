<?php
/**
 * File for class {@link TesseractParserTest}.
 */

include_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'ocr/TesseractParser.php';
include_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'models/Document.php';

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class TesseractParserTest extends PHPUnit_Framework_TestCase{
  
  public function testParseDocumentReturnsAlwaysDocument(){
    $tesseractParser = new TesseractParser();
    $document = $tesseractParser->parseDocument();
    
    $this->assertType('Document', $document);
  }  
}
?>
