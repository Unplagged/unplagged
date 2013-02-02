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
namespace UnpCommon\Model\Document;

use \Doctrine\ORM\Mapping as ORM;
use \UnpCommon\Model\Feature\ArrayCreator;
use \UnpCommon\Model\SimpleEntity;

/**
 * The class represents a fragment type.
 * 
 * @ORM\Entity 
 * @ORM\Table(name="fragment_type")
 */
class FragmentType extends SimpleEntity implements ArrayCreator{

  /**
   * @var string The name of the fragment type.
   * @ORM\Column(type="string", unique=true, length=255)
   */
  private $name;

  /**
   * @var string A description for the fragment type.
   * @ORM\Column(type="string", length=255)
   */
  private $description;

  public function __construct($name = '', $description = ''){
    $this->name = $name;
    $this->description = $description;
  }

  public function toArray(){
    return array(
        'name'=>$this->name,
        'description'=>$this->description,
    );
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

}