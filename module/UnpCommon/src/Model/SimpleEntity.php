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

use \Doctrine\ORM\Mapping as ORM;
use \UnpCommon\Model\Feature\CreatedTracker;

/**
 * This class provides the most basic functionality for entities.
 * 
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class SimpleEntity implements CreatedTracker{

  /**
   * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") 
   */
  protected $id;

  /**
   * @var string The date and time when the object was created initially.
   * @ORM\Column(type="datetime")
   */
  protected $created;

  /**
   * @return int
   */
  public function getId(){
    return $this->id;
  }
  
  /**
   * @ORM\PrePersist
   */
  public function created(){
    if($this->created == null){
      $this->created = new \DateTime('now');
    }
  }

  public function getCreated(){
    return $this->created;
  }
}