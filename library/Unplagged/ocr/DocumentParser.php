<?php
/**
 * This interface is just a stub and will include the common parts to parse from
 * some input file to our internal Document representation.
 * 
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
interface Unplagged_Ocr_DocumentParser{

  /**
   * 
   */
  public function __construct(array $data);
  
  /**
   * This function
   * 
   * @return Application_Model_Document 
   */
  public function parseDocument(); 
}
?>
