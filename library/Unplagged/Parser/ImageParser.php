<?php

/**
 * File for class {@link Unplagged_Parser_ImageParser}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class Unplagged_Parser_ImageParser implements Unplagged_Parser_DocumentParser{
  
  public function parseToDocument(Application_Model_File $file, $language){
    try{
      $inputFileLocation = APPLICATION_PATH . DIRECTORY_SEPARATOR . $file->getLocation();
      $imagemagickTempPath = TEMP_PATH . DIRECTORY_SEPARATOR . 'imagemagick';
      $outputFileLocation = $imagemagickTempPath . DIRECTORY_SEPARATOR . $file->getId() . '.tif';

      if(!is_dir($imagemagickTempPath))
      {
        mkdir($imagemagickTempPath);
        chmod($imagemagickTempPath, 0755);
      }
      
      $adapter = new Unplagged_Parser_ImagemagickAdapter($inputFileLocation, $outputFileLocation);
      $adapter->execute();

      $parser = Unplagged_Parser::factory('image/tif');
      $document = $parser->parseToDocument($file, $language);

      // remove the converted imaged, because it should be in the database now
      unset($outputFileLocation);

      return $document;
    }catch(InvalidArgumentException $e){
      //parsing wasn't successful
      return null;
    }
  }
  
}
?>