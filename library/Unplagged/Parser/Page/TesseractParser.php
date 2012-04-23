<?php

/**
 * File for class {@link Unplagged_Parser_TesseractParser}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class Unplagged_Parser_Page_TesseractParser implements Unplagged_Parser_Page_Parser{

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

      $inputFileLocation = $file->getAbsoluteLocation() . DIRECTORY_SEPARATOR . $file->getFilename();
      $outputFileLocation = TEMP_PATH . DIRECTORY_SEPARATOR . 'ocr' . DIRECTORY_SEPARATOR . $hash;

      $adapter = new Unplagged_Parser_Page_TesseractAdapter($inputFileLocation, $outputFileLocation, $language);
      $adapter->execute();

      // tesseract adds .txt extension automatically, so filename is differently than previously specified
      $outputFileLocation .= '.txt';

      unset($data);
      $data["content"] = nl2br(file_get_contents($outputFileLocation));
      $data["content"] = str_replace("\r\n", "", $data["content"]);
      $data["content"] = str_replace("\n", "", $data["content"]);

      $page = new Application_Model_Document_Page($data);
      // remove the ocr-scanned document, because it is stored in the database now
      unlink($outputFileLocation);

      return $page;
    }catch(InvalidArgumentException $e){
      //parsing wasn't successful
      return null;
    }
  }

}
?>