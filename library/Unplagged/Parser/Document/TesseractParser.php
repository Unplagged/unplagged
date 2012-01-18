<?php

/**
 * File for class {@link Unplagged_Parser_ImageParser}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class Unplagged_Parser_Document_TesseractParser implements Unplagged_Parser_Document_Parser{

  public function __construct(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
  }
  
  public function parseToDocument(Application_Model_File $file, $language){
    // init document
    $data["file"] = $file;
    $data["title"] = $file->getFilename();

    $document = new Application_Model_Document($data);

    $parser = new Unplagged_Parser_Page_TesseractParser();
    $page = $parser->parseToPage($file, $language);
    $document->addPage($page);
    $page->setPageNumber(1);
    $this->_em->persist($page);
    
    $this->_em->persist($document);
    $this->_em->flush();

    return $document;
  }

}

?>