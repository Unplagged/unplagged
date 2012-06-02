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
 * It is able to keep track of the original filename, but also a
 * new one that was used to store the file locally. This 
 * functionality can be useful to hide the storage specific aspects
 * from the user that should only get to see the original filename.
 *
 * @author Unplagged Development Team
 * 
 * @Entity 
 * @Table(name="files")
 * @HasLifeCycleCallbacks
 */
class Application_Model_File extends Application_Model_Base{

  const ICON_CLASS = 'icon-file';

  /**
   * @var string The latest modification date.
   * 
   * @Column(type="datetime")
   */
  private $updated;

  /**
   * @var string The original name of the file.
   * 
   * @Column(type="string", length=255)
   */
  private $filename;

  /**
   * @var string The filename under which the file is actually stored in the file system.
   * 
   * @Column(type="string", length=255);
   */
  private $localFilename;

  /**
   * @var string The mimetype of the file.
   * 
   * @Column(type="string", length=32)
   */
  private $mimetype;

  /**
   * @var string The filesize in bytes.
   * 
   * @Column(type="integer", length=255)
   */
  private $size;

  /**
   * The location of the file in the filesystem.
   * @var string The file location.
   * 
   * @Column(type="string", length=255)
   */
  private $location;

  /**
   * The extension of the file.
   * @var string The file extension.
   * 
   * @Column(type="string", length=255)
   */
  private $extension;

  /**
   * @var string A text that the describes the file, e. g. the origin, the reason for the upload or the like.
   *  
   * @Column(type="text")
   */
  private $description = '';

  public function __construct(array $data = array()){
    foreach($data as $key=>$value){
      $this->setOption($key, $value);
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

  private function setOption($key, $value){
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

  public function getSize(){
    return round($this->size / 1024, 2) . " KB";
  }

  public function getExtension(){
    return $this->extension;
  }

  public function getLocation(){
    return $this->location;
  }

  /**
   * @todo relying on some constant in a model file isn't the best idea in my opinion, better would be to store the whole 
   * path I think. This would only stop users from moving the installation of Unplagged around easily, but that shouldn't
   * be that bad.
   */
  public function getAbsoluteLocation(){
    if(is_dir($this->location)){
      return $this->location;
    }else{
      //@todo deprecated remove later on, if every file was stored with the full path
      return BASE_PATH . DIRECTORY_SEPARATOR . $this->location;
    }
  }

  public function getFullPath(){
    return $this->getAbsoluteLocation() . $this->localFilename;  
  }
  
  public function setLocation($location){
    $this->location = $location;
  }

  public function setExtension($extension){
    $this->extension = $extension;
  }

  public function isImage(){
    return in_array($this->extension, array('jpg', 'jpeg', 'png', 'gif', 'tiff'));
  }

  public function getDirectName(){
    return $this->filename;
  }

  public function getDirectLink(){
    return "/file/show/id/" . $this->id;
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

  public function getDescription(){
    return $this->description;
  }

  public function setDescription($description){
    $this->description = $description;
  }

}