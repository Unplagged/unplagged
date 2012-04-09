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
 * @author Benjamin Oertel <benjamin.oertel@me.com>
 * @version 1.0
 * 
 * @Entity
 * @Table(name="tags")
 */
class Application_Model_Tag extends Application_Model_Base{

  /**
   * The title.
   * @var string The title.
   * @access private
   * 
   * @Column(type="string", length=45)
   */
  private $title;

  /**
   * Constructor.
   */
  public function __construct(){
    
  }

  /**
   * Gets the tag id.
   * @return int Returns the tag id.
   */
  public function getId(){
    return $this->id;
  }

  /**
   * Gets the title.
   * @return int Returns the title.
   */
  public function getTitle(){
    return $this->title;
  }

  /**
   * Sets the tag title.
   * @param string $title The tag title.
   */
  public function setTitle($title){
    $this->title = $title;
  }

  public function getDirectName(){
    return "tag";
  }

  public function getDirectLink(){
    return "/tag/show/id/" . $this->id;
  }

  public function getIconClass(){
    return "tag-icon";
  }

}
