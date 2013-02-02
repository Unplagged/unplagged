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
use \UnpCommon\Model\Comment;
use \UnpCommon\Model\Feature\ArrayCreator;
use \UnpCommon\Model\Feature\Linkable;


/**
 * This class enhances comments with the ability to approve or decline something
 * (most likely the source object of the comment).
 * 
 * @ORM\Entity This will be stored in the comment table
 */
class Rating extends Comment implements Linkable, ArrayCreator{

  /**
   * @var The rating either approved = true or declined = false.
   * @ORM\Column(type="integer")
   */
  private $rating = 0;

  /**
   * @return bool
   */
  public function getRating(){
    return $this->rating;
  }

  /**
   * @param bool $rating
   */
  public function setRating($rating){
    $this->rating = $rating;
  }

  public function toArray(){
    $result = parent::toArray();
    $result['rating'] = $this->rating;
    $result['type'] = 'rating';

    return $result;
  }

  public function getIconClass(){
    return 'fam-icon-star';
  }

}