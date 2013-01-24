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
 * This class represents some kind of user action that already happened, so 
 * that it can be used as entry for the timeline.
 * 
 * @ORM\Entity 
 * @ORM\Table(name="action")
 */
class Action implements DataEntity{

  /**
   * @var int The notification action id.
   * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @var string The unique name of the notification action. 
   * @ORM\Column(type="string", unique=true, length=255)
   */
  private $name;

  /**
   * @var string The unique name of the notification action.
   * @ORM\Column(type="string", length=255)
   */
  private $title;

  /**
   * @var string The description for the notification action
   * @ORM\Column(type="string", length=255)
   */
  private $description;

  public function __construct(array $data = array()){
    $this->name = $data['name'];
    $this->title = $data['title'];
    $this->description = $data['description'];
  }

  /**
   * @return int
   */
  public function getId(){
    return $this->id;
  }

  /**
   * @return string
   */
  public function getName(){
    return $this->name;
  }

  /**
   * @return string
   */
  public function getDescription(){
    return $this->description;
  }

  /**
   * @return string
   */
  public function getTitle(){
    return $this->title;
  }

}