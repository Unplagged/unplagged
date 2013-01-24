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

use Doctrine\ORM\Mapping as ORM;
use UnpCommon\Model\Feature\DataEntity;

/**
 * This class models the state of an operation. It also provides additional data
 * for display purposes.
 * 
 * It defines also the structure of the database table for the ORM.
 * 
 * @ORM\Entity 
 * @ORM\Table(name="state")
 */
class State implements DataEntity{

  /**
   * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @var string The name of the State.
   * @ORM\Column(type="string", unique=true, length=255)
   */
  private $name = '';
  
  /**
   * @var string The title of this State.
   * @ORM\Column(type="string", length=255)
   * 
   * @todo whats the difference between name and title?
   */
  private $title = '';

  /**
   * @var string The description for this State.
   * @ORM\Column(type="string", length=255)
   */
  private $description = '';

  public function __construct(array $data = array()){
    if(isset($data['name'])){
      $this->name = $data['name'];
    }

    if(isset($data['title'])){
      $this->title = $data['title'];
    }
    
    if(isset($data['description'])){
      $this->description = $data['description'];
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

  public function setTitle($title){
    $this->title = $title;
  }

  public function setDescription($description){
    $this->description = $description;
  }
}