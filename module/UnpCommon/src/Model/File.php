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
namespace UnpCommon\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use UnpCommon\Model\Base;
use UnpCommon\Model\Feature\ArrayCreator;
use UnpCommon\Model\Feature\DataEntity;
use UnpCommon\Model\Feature\Linkable;
use UnpCommon\Model\Feature\UpdateTracker;
use UnpCommon\Model\User;

/**
 * This class can be used to handle the data of an uploaded file.
 * 
 * It keeps track of the original filename for display purposes, but 
 * makes it also possible to store the file locally with a different 
 * name. This functionality can be useful to hide the storage 
 * specific aspects from the user that should only get to see the 
 * original filename.
 * 
 * @ORM\Entity 
 * @ORM\Table(name="file")
 * @ORM\HasLifeCycleCallbacks
 */
class File extends Base implements Linkable, DataEntity, UpdateTracker, ArrayCreator{

  /**
   * @var string The original name of the file.
   * 
   * @ORM\Column(type="string", length=255)
   */
  private $originalFilename = '';

  /**
   * @var string The full path of the file in the filesystem.
   * 
   * @ORM\Column(type="string", length=255)
   */
  private $path = '';

  /**
   * @var string The filename under which the file is actually stored in the file system.
   * 
   * @ORM\Column(type="string", length=255);
   */
  private $localFilename = '';

  /**
   * @var string The file extension.
   * 
   * @ORM\Column(type="string", length=255)
   */
  private $extension = '';

  /**
   * @var string The mimetype of the file.
   * 
   * @ORM\Column(type="string", length=255)
   */
  private $mimetype = '';

  /**
   * @var string The filesize in bytes.
   * 
   * @ORM\Column(type="integer")
   */
  private $size = -1;

  /**
   * @var string A text that describes the file, e. g. the origin, the reason for the upload or similar things.
   * 
   * @ORM\Column(type="text")
   */
  private $description = '';

  /**
   * @var User The user who uploaded this specific file.
   * 
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\User")
   * @ORM\JoinColumn(name="uploader_id", referencedColumnName="id")
   */
  private $uploader;

  /**
   * @var string The date of the latest modification.
   * @ORM\Column(type="datetime")
   */
  private $updated;

  public function __construct(array $data = array()){
    parent::__construct();

    foreach($data as $key=> $value){
      $this->setField($key, $value);
    }
  }

  private function setField($key, $value){
    if(!empty($key) && !empty($value) && property_exists($this, $key)){
      $this->$key = $value;
    }
  }

  /**
   * @return string
   */
  public function getOriginalFilename(){
    return $this->originalFilename;
  }

  /**
   * @return string
   */
  public function getPath(){
    return $this->path;
  }

  /**
   * @return string
   */
  public function getLocalFilename(){
    return $this->localFilename;
  }

  /**
   * @return string
   */
  public function getExtension(){
    return $this->extension;
  }

  /**
   * @return string
   */
  public function getMimetype(){
    return $this->mimetype;
  }

  /**
   * @return int
   */
  public function getSize(){
    return $this->size;
  }

  /**
   * @return string
   */
  public function getFullPath(){
    return $this->path . $this->localFilename;
  }

  /**
   * @return string
   */
  public function getDescription(){
    return $this->description;
  }

  /**
   * @param string $description
   */
  public function setDescription($description){
    $this->description = $description;
  }

  /**
   * @return User
   */
  public function getUploader(){
    return $this->uploader;
  }

  /**
   * @return bool
   */
  public function isImage(){
    return in_array($this->extension, array('jpg', 'jpeg', 'png', 'gif'));
  }

  public function toArray(){
    $result = array();

    if(!empty($this->originalFilename)){
      $result['originalFilename'] = $this->originalFilename;
    }
    if(!empty($this->extension)){
      $result['extension'] = $this->extension;
    }

    return $result;
  }

  /**
   * @ORM\PrePersist
   */
  public function updated(){
    $this->updated = new DateTime('now');
  }

  public function getUpdated(){
    return $this->updated;
  }

  public function getDirectName(){
    return $this->getOriginalFilename();
  }

  public function getDirectLink(){
    return "/file/show/id/" . $this->id;
  }

  public function getIconClass(){
    return 'icon-file';
  }

}