<?php
/**
 * File for class {@link TesseractParser}.
 */

require_once 'DocumentParser.php';

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class TesseractParser implements DocumentParser{
  
  public function parseDocument(){
    return new Document();
  }
}
?>
