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
   * 
   * @todo we need to change this md5 isn't secure at all, normally this needs salting and at least sha256, better would
   * be to use bcrypt or phpass
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

  public function formatDiff($diff, $baseVersion, $changeVersion) {
    if(empty($diff)) {
      return "<br />No difference";
    }
    
    $diff = $diff[0];
    
    $baseResult = "";
    $changedResult = "";
    
    $baseResult .= "<div class=\"document-page diff clearfix\"><div class=\"src-wrapper\"><h3>Version " . $baseVersion . "</h3><ol>";
    $changedResult .= "<div class=\"src-wrapper\"><h3>Version " . $changeVersion . "</h3><ol>";
    foreach($diff as $lines) {
      $base = $lines["base"];
      $change = $lines["changed"];
      
      $iterator = count($base["lines"]) > count($change["lines"]) ? $base["lines"] : $change["lines"];
      foreach($iterator as $key => $line) {
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
