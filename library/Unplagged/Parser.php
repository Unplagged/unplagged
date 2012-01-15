<?php

/**
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Parser
 *
 * @author benjamin
 */
class Unplagged_Parser{

  private static $mimeMappings = array(
    'image/tif' => 'Document_ImageParser'
    , 'image/tiff' => 'Document_ImageParser'
    //@todo sprobably not the best idea to push all octet-stream through tesseract, but will work for now
    , 'application/octet-stream' => 'Document_TesseractParser'
    , 'image/jpeg' => 'Document_ImageParser'
    , 'image/gif' => 'Document_ImageParser'
    , 'image/png' => 'Document_ImageParser'
    , 'image/jpg' => 'Document_ImageParser'
    , 'image/*' => 'Document_ImageParser'
    , 'application/pdf' => 'Document_ImageParser'
  );

  public static function factory($mimeType){

    if(array_key_exists($mimeType, self::$mimeMappings)){
      $parserName = 'Unplagged_Parser_' . self::$mimeMappings[$mimeType];
      return new $parserName();
    }
  }

}
?>
