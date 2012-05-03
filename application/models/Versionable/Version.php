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
 * Description of Versionable
 *
 * @Entity
 * @Table(name="versionable_versions") 
 */
class Application_Model_Versionable_Version extends Application_Model_Base{

  const ICON_CLASS = '';
  
  /**
   * @Column(type="integer")
   */
  protected $version;

  /**
   * @Column(type="array")
   */
  protected $data;

  /**
   * @ManyToOne(targetEntity="Application_Model_Versionable")
   */
  protected $versionable;

  public function __construct(Application_Model_Versionable $versionable){
    $this->versionable = $versionable;

    $this->version = $versionable->getCurrentVersion();
    $this->data = $versionable->toArray();
  }

  public function getDirectLink(){
    
  }

  public function getDirectName(){
    
  }

  public function toArray(){
    
  }

  public function getVersionable(){
    return $this->versionable;
  }

  public function getVersion(){
    return $this->version;
  }

  public function setVersionable($versionable){
    $this->versionable = $versionable;
  }

  /**
   * Return the elements data as an array, where the key is the name of the object property.
   * @return array 
   */
  public function getData(){
    return $this->data;
  }

}

?>
