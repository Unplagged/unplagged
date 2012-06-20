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
 * The class represents a fragment type.
 * It defines also the structure of the database table for the ORM.
 * 
 * @Entity 
 * @Table(name="document_fragment_types")
 */
class Application_Model_Document_Fragment_Type{
  
  const PERMISSION_TYPE = 'document-fragment-type';

  /**
   * The id is an unique identifier for each fragment type.
   * @var string The fragment type id.
   * 
   * @Id @GeneratedValue @Column(type="integer")
   */
  private $id;

  /**
   * A unique name for the fragment type.
   * @var string The name of the fragment type.
   * 
   * @Column(type="string", unique=true, length=32)
   */
  private $name;

  /**
   * A description for the fragment type.
   * @var string The description.
   * 
   * @Column(type="string", length=256)
   */
  private $description;

  public function __construct($data = array()){
    if(isset($data["name"])){
      $this->name = $data["name"];
    }
    
    if(isset($data["description"])){
      $this->description = $data["description"];
    }
  }
  
  public function toArray() {
    $data["name"] = $this->name;
    $data["description"] = $this->description;

    return $data;
  }

  public function getId(){
    return $this->id;
  }
  
  public function getName(){
    return $this->name;
  }
  
  public function getDescription(){
    return $this->description;
  }

}