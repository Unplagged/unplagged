<?php

/**
 * The class represents a file.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 * 
 * @Entity 
 * @Table(name="files")
 * @HasLifeCycleCallbacks
 */
class Application_Model_File {

  /**
   * The fileId is an unique identifier for each file.
   * @var string The fileId.
   * 
   * @Id @GeneratedValue @Column(type="integer")
   */
  private $id;

  /**
   * The date when the file was uploaded.
   * @var string The upload date.
   * 
   * @Column(type="datetime")
   */
  private $created;

  /**
   * The date when the file was modified.
   * @var string The latest modification date.
   * 
   * @Column(type="datetime")
   */
  private $updated;

  /**
   * The name of the file.
   * @var string The filename.
   * 
   * @Column(type="string", length=64)
   */
  private $filename;

  /**
   * The mimetype of the file.
   * @var string The mimetype.
   * 
   * @Column(type="string", length=32)
   */
  private $mimetype;

  /**
   * The filesize in bytes..
   * @var string The filesize.
   * 
   * @Column(type="integer", length=32)
   */
  private $size;

  /**
   * The location of the file in the filesystem.
   * @var string The file location.
   * 
   * @Column(type="string", length=64)
   */
  private $location;
  
  /**
   * The extension of the file.
   * @var string The file extension.
   * 
   * @Column(type="string", length=16)
   */
  private $extension;
  
  /**
   * If the file is target or not
   * @var string The file is a target.
   * 
   * @Column(type="boolean")
   * 
   * @todo maybe we should move this to the case? So that we would have an array of target files
   * and then we could probably add here some associations to representations of this file, that were 
   * already created
   */ 
  private $isTarget = false;

  /**
   * Method auto-called when object is persisted to database for the first time.
   * 
   * @PrePersist
   */
  public function created(){
    $this->created = new DateTime("now");
  }

  /**
   * Method auto-called when object is updated in database.
   * 
   * @PrePersist
   */
  public function updated(){
    $this->updated = new DateTime("now");
  }
  
  public function __construct($data = array()){
    if(isset($data["filename"])) {
      $this->filename = $data["filename"];
    }
    
    if(isset($data["mimetype"])) {
      $this->mimetype = $data["mimetype"];
    }
    
    if(isset($data["size"])) {
      $this->size = $data["size"];
    }
    
    if(isset($data["location"])) {
      $this->location = $data["location"];
    }
    
    if(isset($data["extension"])) {
      $this->extension = $data["extension"];
    }
  }
  
  public function getId(){
    return $this->id;
  }

  /**
   *
   *
   * @param type $id 
   * @todo remove when TesseractParser doesn't rely anymore on the id for the file path.
   */
  public function setId($id){
    $this->id = $id;
  }
  
  public function getCreated(){
    return $this->created;
  }

  public function getUpdated(){
    return $this->updated;
  }

  public function getFilename(){
    return $this->filename;
  }

  public function getMimetype(){
    return $this->mimetype;
  }

  public function getSize(){
    return round($this->size / 1024, 2) . " KB";
  }
  
  public function getExtension(){
    return $this->extension;
  }

  public function getLocation(){
    return $this->location;
  }
  
  public function getAbsoluteLocation(){
    return BASE_PATH . DIRECTORY_SEPARATOR . $this->location;
  }
  
  public function getIsTarget(){
    return $this->isTarget;
  }

  public function setIsTarget($isTarget){
    $this->isTarget = $isTarget;
  }    
  
  public function setLocation($location){
    $this->location = $location;
  }

  public function setExtension($extension){
    $this->extension = $extension;
  }


}