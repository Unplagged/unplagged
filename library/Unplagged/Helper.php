<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Unplagged_Helper{

  /**
   * Generates a random hash.
   * 
   * @return The hashed string.
   */
  public static function generateRandomHash(){
    return substr(md5(uniqid(rand())), 0, 32);
    ;
  }

  /**
   * Hash a string.
   * 
   * @String $string The unhashed string.
   * 
   * @return The hashed string.
   */
  public static function hashString($string){
    return md5($string);
  }

  /**
   * Checks if a string matches a hash using the same function the hash was created.
   * 
   * @String $string The unhashed string.
   * @String $hash The hash.
   * 
   * @return Whether the string matches the hash or not.
   */
  public static function checkStringAndHash($string, $hash){
    return $hash == $this->hashString($string);
  }
  
  /**
   * Gets the appropriate icon for a specific file extension
   * 
   * @String $ext The file extension.
   * 
   * @return The filename of the appropriate icon.
   */
  public static function getFileIconByExtenstion($ext){
    if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif' || $ext == 'tiff'){
      return 'picture.png';
    }else if($ext == 'pdf'){
      return 'page_white_acrobat.png';
    }else if($ext == 'doc' || $ext == 'odt' || $ext == 'txt'){
      return 'page_white_text.png';
    }else if($ext == 'zip'){
      return 'page_white_zip.png';
    }else{
      return 'page_white_text.png';
    }
  }

}

?>
