<?php

/**
 * This interface is just a stub and will include the common parts to parse from
 * some input file to our internal Document representation.
 * 
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
interface Unplagged_Parser_Page_Parser{

  /**
   * @param $file The previous uploaded file.
   * @param $language The language of the uploaded file.
   * 
   * @return Application_Model_Document_Page
   */
  public function parseToPage(Application_Model_File $file, $language);
}

?>
