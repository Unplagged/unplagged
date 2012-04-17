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
 * The class represents a user state.
 * It defines also the structure of the database table for the ORM.
 * 
 * @Entity 
 * @Table(name="user_states")
 */
class Application_Model_User_State{

  /**
   * The logActionId is an unique identifier for each user state.
   * @var string The user state id.
   * $access protected
   * 
   * @Id @GeneratedValue @Column(type="integer")
   */
  private $id;

  /**
   * A title for the user state.
   * @var string The user state.
   * 
   * @Column(type="string", unique=true, length=32)
   */
  private $title;

  /**
   * A description for the user state.
   * @var string The user state.
   * 
   * @Column(type="string", length=256)
   */
  protected $description;

  public function __construct($data = array()){
    if(isset($data["title"])){
      $this->title = $data["title"];
    }

    if(isset($data["description"])){
      $this->description = $data["description"];
    }
  }

  public function getId(){
    return $this->id;
  }

  public function getTitle(){
    return $this->title;
  }

  public function getDescription(){
    return $this->description;
  }

}