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
use UnpCommon\Model\Base;
use UnpCommon\Model\Feature\DataEntity;
use UnpCommon\Model\Feature\Linkable;

/**
 * The class represents a single tag used to categorize an article.
 * It defines also the structure of the database table for the ORM.
 * 
 * @ORM\Entity
 * @ORM\Table(name="tag")
 */
class Tag extends Base implements Linkable, DataEntity{

  /**
   * @var string The title.
   * @ORM\Column(type="string", length=255)
   */
  private $title;

  public function __construct(array $data = array()){ 
    parent::__construct();
    if(isset($data['title'])){
      $this->title = $data['title'];
    }
  }

  /**
   * @return string
   */
  public function getTitle(){
    return $this->title;
  }

  /**
   * @param string $title
   */
  public function setTitle($title){
    $this->title = $title;
  }

  public function getDirectName(){
    return 'tag';
  }

  public function getDirectLink(){
    return '/tag/show/id/' . $this->id;
  }

  public function getIconClass(){
    return 'icon-tag';
  }

}