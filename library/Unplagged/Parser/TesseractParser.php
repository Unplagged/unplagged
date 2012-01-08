<?php

/**
 * File for class {@link Unplagged_Parser_TesseractParser}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class Unplagged_Parser_TesseractParser implements Unplagged_Parser_DocumentParser{

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
  public function parseToDocument(Application_Model_File $file, $language){

    // init document
    $data["file"] = $file;
    $data["title"] = $file->getFilename();
    
    $document = new Application_Model_Document($data);
    $this->_em->persist($document);
    $this->_em->flush();

    $inputFileLocation = APPLICATION_PATH . DIRECTORY_SEPARATOR . $file->getLocation();
    $outputFileLocation = TEMP_PATH . DIRECTORY_SEPARATOR . 'ocr' . DIRECTORY_SEPARATOR . $document->getId();
    
    $adapter = new Unplagged_Parser_TesseractAdapter($inputFileLocation, $outputFileLocation, $language);
    $adapter->execute();

    // tesseract adds .txt extension automatically, so filename is differently than previously specified
    $outputFileLocation .= '.txt';

    unset($data);
    $data["pageNumber"] = 1;
    $data["content"] = nl2br(file_get_contents($outputFileLocation));    
    $data["content"] = str_replace("\r\n","", $data["content"]);
    $data["content"] = str_replace("\n","", $data["content"]);
    
    $page = new Application_Model_Document_Page($data);

    $this->_em->persist($page);
    $document->addPage($page);

    $this->_em->persist($document);
    $this->_em->flush();
    
    // remove the ocr-scanned document, because it is stored in the database now
    unset($outputFileLocation);

    return $document;
  }

}

?>