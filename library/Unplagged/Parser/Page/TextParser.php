<?php

/**
 * File for class {@link Unplagged_Parser_TextParser}.
 */

/**
 *
 * @author Benjamin Oertel
 */
class Unplagged_Parser_Page_TextParser implements Unplagged_Parser_Page_Parser{

  public function __construct(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
  }

  /**
   * This function returns true if it could confirm, that tesseract is working.
   * @param $file The previous uploaded file.
   * @param $language The language of the uploaded file.
   * 
   * @return Application_Model_Document
   */
  public function parseToPage(Application_Model_File $file, $language){
    try{
      $hash = Unplagged_Helper::generateRandomHash();

      $inputFileLocation = $file->getFullPath();

      $page = new Application_Model_Document_Page();
      $page->setContent(file_get_contents($inputFileLocation), "text");

      return $page;
    }catch(InvalidArgumentException $e){
      //parsing wasn't successful
      return null;
    }
  }

}
?>
