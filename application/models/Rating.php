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
 * The class represents a single tag used to categorize an article.
 * It defines also the structure of the database table for the ORM.
 * 
 * @Entity
 * @Table(name="ratings")
 */
class Application_Model_Rating extends Application_Model_Base{

  const ICON_CLASS = 'icon-star';

  /**
   * @ManyToOne(targetEntity="Application_Model_User")
   * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $user;

  /**
   * The element the rating is related to.
   *
   * @ManyToOne(targetEntity="Application_Model_Base", inversedBy="ratings")
   * @JoinColumn(name="source_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $source;

  /**
   * The text related to the rating.
   * 
   * @Column(type="text", nullable=true)
   */
  private $reason;

  /**
   * The rating either approved = true or declined = false.
   * 
   * @Column(type="boolean")
   */
  private $rating;

  /**
   * Constructor.
   */
  public function __construct($data = array()){
    parent::__construct($data);
    
    if(isset($data['user'])){
      $this->user = $data['user'];
    }

    if(isset($data["source"])){
      $this->source = $data["source"];
    }

    if(isset($data["reason"])){
      $this->reason = $data["reason"];
    }

    if(isset($data["rating"])){
      $this->rating = $data["rating"];
    }
  }

  public function getDirectName(){
    return $this->source->getDirectName();
  }

  public function getDirectLink(){
    return $this->source->getDirectLink();
  }

  public function getUser(){
    return $this->user;
  }

  public function getSource(){
    return $this->source;
  }

  public function getReason(){
    return $this->reason;
  }

  public function getRating(){
    return $this->rating;
  }

  public function setUser(Application_Model_User $user){
    $this->user = $user;
  }

  public function setSource(Application_Model_Document $source){
    $this->source = $source;
  }

  public function setReason($reason){
    $this->reason = $reason;
  }

  public function setRating($rating){
    $this->rating = $rating;
  }

  public function toArray($hide = array()){
    $result = array();

    $result["id"] = $this->id;
    $result["reason"] = $this->reason;
    $result["rating"] = $this->rating;
    $result["user"] = $this->user->toArray();
    if(!in_array('source', $hide)){
      $result["source"] = $this->source->toArray();
    }
    $result["created"] = Unplagged_Helper::jsTime($this->created);
    $result['type'] = 'rating';

    return $result;
  }

}