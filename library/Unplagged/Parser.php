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

  public static function factory($mimeType){
    switch($mimeType){
      case "image/tif":
      case "image/tiff":
        return new Unplagged_Parser_TesseractParser();
    }
  }
}

?>
