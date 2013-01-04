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

use Doctrine\ORM\Mapping AS ORM;

/**
 * The class represents a notification action.
 * 
 * @ORM\Entity 
 * @ORM\Table(name="action")
 */
class Action{

  /**
   * @var int The notification action id.
   * 
   * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @var string The unique name of the notification action.
   * 
   * @ORM\Column(type="string", unique=true, length=255)
   */
  private $name;

  /**
   * @var string The unique name of the notification action.
   * 
   * @ORM\Column(type="string", length=255)
   */
  private $title;

  /**
   * @var string The description for the notification action
   * 
   * @ORM\Column(type="string", length=255)
   */
  private $description;

  public function __construct($data = array()){
    if(isset($data["name"])){
      $this->name = $data["name"];
    }

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

  public function getName(){
    return $this->name;
  }

  public function getDescription(){
    return $this->description;
  }

  public function getTitle(){
    return $this->title;
  }
  
  public function setName($name){
    $this->name = $name;
  }

  public function setTitle($title){
    $this->title = $title;
  }

  public function setDescription($description){
    $this->description = $description;
  }
}