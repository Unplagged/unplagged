<?php

/**
 * File for class {@link Unplagged_Parser_ImageParser}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class Unplagged_Parser_Document_ImageParser implements Unplagged_Parser_Document_Parser{

  public function __construct(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
  }
  
  public function parseToDocument(Application_Model_File $file, $language){
    try{
      $inputFileLocation = $file->getAbsoluteLocation() . DIRECTORY_SEPARATOR . $file->getId() . "." . $file->getExtension();
      $imagemagickTempPath = TEMP_PATH . DIRECTORY_SEPARATOR . 'imagemagick';
      $outputFileLocation = $imagemagickTempPath . DIRECTORY_SEPARATOR . $file->getId() . '.tif';
      
      $adapter = new Unplagged_Parser_Document_ImagemagickAdapter($inputFileLocation, $outputFileLocation);
      $adapter->execute();

      // init document
      $data["file"] = $file;
      $data["title"] = $file->getFilename();
      
      
      $fileData = array('filename'=>$file->getId() . '.tif', 'extension' => 'tif', 'mimeType' =>'image/tiff' , 'location'=>'temp/imagemagick');
      $tempFile = new Application_Model_File($fileData);
      $tempFile->setId($file->getId());
      
      $document = new Application_Model_Document($data);
      
      $parser = new Unplagged_Parser_Page_TesseractParser();
      // for loop over pages

        $page = $parser->parseToPage($tempFile, $language);
        $page->setPageNumber(1);
        $document->addPage($page);
        $this->_em->persist($page);

        // remove the converted imaged, because it should be in the database now
        unset($outputFileLocation);
      // end for loop
      
      $this->_em->persist($document);
      $this->_em->flush();

      return $document;
    }catch(InvalidArgumentException $e){
      //parsing wasn't successful
      return null;
    }
  }
  
}
?>