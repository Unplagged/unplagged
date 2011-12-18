<?php




/**
 * This interface is just a stub and will include the common parts to parse from
 * some input file to our internal Document representation.
 * 
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
interface DocumentParser{

  /**
   * @return Document 
   */
  public function parseDocument();
}
?>
