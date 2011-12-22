<?php
/**
 * File for class {@link TesseractParser}.
 */

require_once 'DocumentParser.php';

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class Unplagged_Ocr_TesseractParser implements Unplagged_Ocr_DocumentParser{
  
  public function parseDocument(){
    return new Application_Model_Document();
  }
}
?>
