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
 * The class represents a file.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * 
 * @Entity 
 * @Table(name="files")
 * @HasLifeCycleCallbacks
 */
class Application_Model_File extends Application_Model_Base{

  const ICON_CLASS = 'file-icon';
  
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
   * @var string
   *  
   * @Column(type="text")
   */
  private $description = '';
  
  /**
   * Method auto-called when object is updated in database.
   * 
   * @PrePersist
   */
  public function updated(){
    $this->updated = new DateTime("now");
  }

  public function __construct($data = array()){
    if(isset($data["filename"])){
      $this->filename = $data["filename"];
    }

    if(isset($data["mimetype"])){
      $this->mimetype = $data["mimetype"];
    }

    if(isset($data["size"])){
      $this->size = $data["size"];
    }

    if(isset($data["location"])){
      $this->location = $data["location"];
    }

    if(isset($data["extension"])){
      $this->extension = $data["extension"];
    }
    
    if(isset($data['description'])){
      $this->description = $data['description'];
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

  /**
   * @todo relying on some constant in a model file isn't the best idea in my opinion, better would be to store the whole 
   * path I think. This would only stop users from moving the installation of Unplagged around easily, but that shouldn't
   * be that bad.
   */
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
      $result["filename"] = $this->filename;
    }
    if(!empty($this->extension)){
      $result["extension"] = $this->extension;
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