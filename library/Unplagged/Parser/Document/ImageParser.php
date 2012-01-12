<?php

/**
 * File for class {@link Unplagged_Parser_ImageParser}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class Unplagged_Parser_Document_ImageParser implements Unplagged_Parser_Document_Parser{
  
  public function parseToDocument(Application_Model_File $file, $language){
    try{
      $inputFileLocation = BASE_PATH . DIRECTORY_SEPARATOR . $file->getLocation() . DIRECTORY_SEPARATOR . $file->getId() . "." . $file->getExtension();
      $imagemagickTempPath = TEMP_PATH . DIRECTORY_SEPARATOR . 'imagemagick';
      $outputFileLocation = $imagemagickTempPath . DIRECTORY_SEPARATOR . $file->getId() . '.tif';
      
      $adapter = new Unplagged_Parser_Document_ImagemagickAdapter($inputFileLocation, $outputFileLocation);
      $adapter->execute();

      // init document
      $data["file"] = $file;
      $data["title"] = $file->getFilename();

      $document = new Application_Model_Document($data);
      
      $parser = Unplagged_Parser::factory('image/tif');
      // for loop over pages
        $page = $parser->parseToPage($file, $language);
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