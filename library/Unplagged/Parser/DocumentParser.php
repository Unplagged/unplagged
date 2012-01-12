<?php

/**
 * This interface is just a stub and will include the common parts to parse from
 * some input file to our internal Document representation.
 * 
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
interface Unplagged_Parser_DocumentParser{

  /**
   * @param $file The previous uploaded file.
   * @param $language The language of the uploaded file.
   * 
   * @return Application_Model_Document
   */
  public function parseToDocument(Application_Model_File $file, $language);
}

?>
