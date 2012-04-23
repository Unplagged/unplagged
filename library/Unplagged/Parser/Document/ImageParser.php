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

  public function parseToDocument(Application_Model_File $file, $language, Application_Model_Document $document = null){
    try{
      $inputFileLocation = $file->getAbsoluteLocation() . DIRECTORY_SEPARATOR . $file->getId() . "." . $file->getExtension();
      $imagemagickTempPath = TEMP_PATH . DIRECTORY_SEPARATOR . 'imagemagick';

      $outputFileLocation = $imagemagickTempPath . DIRECTORY_SEPARATOR . $file->getId() . '-%d.tif';

      $adapter = new Unplagged_Parser_Document_ImagemagickAdapter($inputFileLocation, $outputFileLocation);
      $adapter->execute();

      // create the document
      if(!$document){
        $data["file"] = $file;
        $data["title"] = $file->getFilename();
        $document = new Application_Model_Document($data);
      }
      $parser = new Unplagged_Parser_Page_TesseractParser();

      $i = 1;
      // iterate over converted files and ocr them
      $handler = opendir($imagemagickTempPath);
      while($tifFile = readdir($handler)){
        if($tifFile != "." && $tifFile != ".."){
          if(preg_match('/' . $file->getId() . '(-(\d)*){0,1}.tif/', $tifFile)){
            // for loop over pages
            $fileData = array('filename'=>$tifFile, 'extension'=>'tif', 'mimeType'=>'image/tiff', 'location'=>'temp/imagemagick');
            $tempFile = new Application_Model_File($fileData);
            $tempFile->setId($file->getId());

            $page = $parser->parseToPage($tempFile, $language);
            $page->setPageNumber($i);
            $document->addPage($page);

            $this->_em->persist($page);

            // remove the converted imaged, because it should be in the database now
            $tifPath = $imagemagickTempPath . DIRECTORY_SEPARATOR . $tifFile;
            unlink($tifPath);
            $i++;
          }
        }
      }

      $this->_em->persist($document);
      $this->_em->flush();

      return $document;
    }catch(Exception $e){
      //parsing wasn't successful
      return null;
    }
  }

}

?>