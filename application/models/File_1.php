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

/**
 * This class can be used to handle the data of an uploaded file.
 * 
 * It keeps track of the original filename for display purposes, but 
 * makes it also possible to store the file locally with a different 
 * name. This functionality can be useful to hide the storage 
 * specific aspects from the user that should only get to see the 
 * original filename.
 * 
 * @Entity 
 * @Table(name="files")
 * @HasLifeCycleCallbacks
 */
class Application_Model_File extends Application_Model_Base{

  const ICON_CLASS = 'icon-file';

  /**
   * @var string The date of the latest modification.
   * @Column(type="datetime")
   */
  private $updated;

  /**
   * @var string The original name of the file.
   * @Column(type="string", length=255)
   */
  private $filename;

  /**
   * @var string The filename under which the file is actually stored in the file system.
   * @Column(type="string", length=255);
   */
  private $localFilename;

  /**
   * @var string The mimetype of the file.
   * @Column(type="string", length=255)
   */
  private $mimetype;

  /**
   * @var string The filesize in bytes.
   * @Column(type="integer", length=255)
   */
  private $size;

  /**
   * @var string The location of the file in the filesystem.
   * @Column(type="string", length=255)
   */
  private $location;

  /**
   * @var string The file extension.
   * @Column(type="string", length=255)
   */
  private $extension;

  /**
   * @var string A text that describes the file, e. g. the origin, the reason for the upload or similar things.
   * @Column(type="text")
   */
  private $description = '';

  /**
   * @var Application_Model_User The user who uploaded this specific file.
   * @ManyToOne(targetEntity="Application_Model_User")
   * @JoinColumn(name="uploader_id", referencedColumnName="id")
   */
  private $uploader;

  /**
   * @var string The folder location of the file.
   * @Column(type="string", length=255, nullable=true)
   */
  private $folder;

  public function __construct($data = array()){
    parent::__construct($data);
    
    foreach($data as $key=>$value){
      $this->setField($key, $value);
    }
  }

  /**
   * This method is auto-called when the object is updated in the database.
   * 
   * @PrePersist
   */
  public function updated(){
    $this->updated = new DateTime('now');
  }

  private function setField($key, $value){
    if(!empty($key) && !empty($value) && property_exists($this, $key)){
      $this->$key = $value;
    }
  }

  public function getUpdated(){
    return $this->updated;
  }

  public function getFilename(){
    return $this->filename;
  }

  public function getLocalFilename(){
    return $this->localFilename;
  }

  public function getMimetype(){
    return $this->mimetype;
  }

  /**
   * @return string
   */
  public function getSize(){
    return round($this->size / 1024, 2) . ' KB';
  }

  /**
   * @return string
   */
  public function getExtension(){
    return $this->extension;
  }

  public function getLocation(){
    return $this->location;
  }

  public function getFullPath(){
    return $this->location . $this->localFilename;
  }

  public function setLocation($location){
    $this->location = $location;
  }

  public function isImage(){
    return in_array($this->extension, array('jpg', 'jpeg', 'png', 'gif'));
  }

  public function getDirectName(){
    return $this->filename;
  }

  public function getDirectLink(){
    //return "/file/show/id/" . $this->id;
    return "/file/list";
  }
  
  public function toArray(){
    $result = array();

    if(!empty($this->filename)){
      $result['filename'] = $this->filename;
    }
    if(!empty($this->extension)){
      $result['extension'] = $this->extension;
    }

    return $result;
  }

  /**
   * @return string
   */
  public function getDescription(){
    return $this->description;
  }

  
  public function setDescription($description){
    $this->description = $description;
  }

  /**
   * @return Application_Model_User
   */
  public function getUploader(){
    return $this->uploader;
  }
  
  /**
   * @return string
   */
  public function getFolder(){
    return $this->folder;
  }
}