<?php

/**
 * Unplagged - The plagiarism detection cockpit.
 * Copyright (C) 2012 Unplagged
 *  
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *  
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once BASE_PATH . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'phpass-0.3' . DIRECTORY_SEPARATOR . 'PasswordHash.php';

class Unplagged_Helper{

  /**
   * Generates a random hash.
   * 
   * @return The hashed string.
   */
  public static function generateRandomHash(){
    return substr(md5(uniqid(rand())), 0, 32);
  }

  /**
   * Hash a string.
   * 
   * @String $string The unhashed string.
   * 
   * @return The hashed string.
   * 
   * @todo we need to change this md5 isn't secure at all, normally this needs salting and at least sha256, better would
   * be to use bcrypt or phpass
   */
  public static function hashString($pass){
    $hashCostLog2 = 10;
    $hashPortable = false;
    $hasher = new PasswordHash($hashCostLog2, $hashPortable);
    $hash = $hasher->HashPassword($pass);

    return $hash;
  }

  /**
   * Checks if a string matches a hash using the same function the hash was created.
   * 
   * @String $string The unhashed string.
   * @String $hash The hash.
   * 
   * @return Whether the string matches the hash or not.
   */
  public static function checkStringAndHash($pass, $hash){
    $hashCostLog2 = 10;
    $hashPortable = false;
    $hasher = new PasswordHash($hashCostLog2, $hashPortable);

    if($hasher->CheckPassword($pass, $hash)){
      return true;
    }else{
      return false;
    }
  }

  /**
   * Gets the appropriate icon for a specific file extension
   * 
   * @String $ext The file extension.
   * 
   * @return The filename of the appropriate icon.
   */
  public static function getFileIconByExtension($ext){
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

  /**
   * Create a new entry in the notification table.
   * 
   * @string $type The notification type.
   * @string $title The element id the notification is related to.
   * @integer $user The user the notification belongs to.
   * 
   */
  public static function notify($action, $source, $user){
    $em = Zend_Registry::getInstance()->entitymanager;

    $data = array();
    $data["action"] = $em->getRepository('Application_Model_Action')->findOneBy(array('name'=>$action));
    $data["user"] = $user;
    $data["source"] = $source;

    $notification = new Application_Model_Notification($data);

    $em->persist($notification);
    $em->flush();
  }

  public static function humanTiming(DateTime $dateTime){

    $totaldelay = time() - $dateTime->getTimestamp();

    if($totaldelay < 0){
      return '';
    }else{
      if($days = floor($totaldelay / 86400)){
        return $dateTime->format("Y-m-d H:i:s");
      }
      if($hours = floor($totaldelay / 3600)){
        $totaldelay = $totaldelay % 3600;
        return $hours . ' hours ago';
      }
      if($minutes = floor($totaldelay / 60)){
        $totaldelay = $totaldelay % 60;
        return $minutes . ' minutes ago';
      }
      return 'less than a minute ago';
    }
  }

  public function formatDocumentPageNumber($pageNumber){
    return str_pad($pageNumber, 3, '0', STR_PAD_LEFT);
  }

  public function formatDiff($diff, $baseVersion, $changeVersion){
    if(empty($diff)){
      return "<br />No difference";
    }

    $diff = $diff[0];

    $baseResult = "";
    $changedResult = "";

    $baseResult .= "<div class=\"document-page diff clearfix\"><div class=\"src-wrapper\"><h3>Version " . $baseVersion . "</h3><ol>";
    $changedResult .= "<div class=\"src-wrapper\"><h3>Version " . $changeVersion . "</h3><ol>";
    foreach($diff as $lines){
      $base = $lines["base"];
      $change = $lines["changed"];

      $iterator = count($base["lines"]) > count($change["lines"]) ? $base["lines"] : $change["lines"];
      foreach($iterator as $key=>$line){
        $baseText = !empty($base["lines"][$key]) ? $base["lines"][$key] : "";
        $changeText = !empty($change["lines"][$key]) ? $change["lines"][$key] : "";

        if(empty($baseText))
          $changeText = "<ins>" . $changeText . "</ins>";

        $baseResult .= "<li>" . $baseText . "</li>";

        if(empty($changeText))
          $changeText = "<del>" . $changeText . "</del>";

        $changedResult .= "<li>" . $changeText . "</li>";
      }
    }
    $baseResult .= "</ol></div>";
    $changedResult .= "</ol></div></div>";

    return $baseResult . $changedResult;
  }

}
?>
