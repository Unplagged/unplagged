<?php
/**
 * File for class {@link TesseractAdapterTest}.
 */

include APPLICATION_PATH . DIRECTORY_SEPARATOR . 'ocr/TesseractAdapter.php';

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class TesseractAdapterTest extends PHPUnit_Framework_TestCase{
  
  public function testArgumentsMustBeValid(){
    $this->setExpectedException('InvalidArgumentException');
    $tesseractAdapter = new TesseractAdapter('', '', '');
  }
}
?>
