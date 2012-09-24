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
 * @Table(name="comments")
 */
class Application_Model_Comment extends Application_Model_Base {

  const ICON_CLASS = 'icon-comment';

  /**
   * @ManyToOne(targetEntity="Application_Model_User")
   * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $author;

  /**
   * @var string The title of the comment.
   * @access private
   * 
   * @Column(type="string", length=45, nullable=true)
   */
  private $title;

  /**
   * @var string The text of the comment.
   * @access private
   * 
   * @Column(type="string", length=255)
   */
  private $text;

  /**
   * @ManyToOne(targetEntity="Application_Model_Base")
   * @JoinColumn(name="source_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $source;

  /**
   * Constructor.
   */
  public function __construct($data = array(), $author = null, Application_Model_Base $source = null, $title = null, $text = null) {
    parent::__construct($data);

    if (isset($data["author"])) {
      $this->author = $data["author"];
    }

    if (isset($data["source"])) {
      $this->source = $data["source"];
    }

    if (isset($data["title"])) {
      $this->title = $data["title"];
    }

    if (isset($data["text"])) {
      $this->text = $data["text"];
    }
  }

  /**
   * Gets the tag id.
   * 
   * @return int Returns the tag id.
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Gets comment title.
   * 
   * @return int Returns the title.
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * Gets comment text.
   * 
   * @return int Returns the text.
   */
  public function getText() {
    return $this->text;
  }

  public function getAuthor() {
    return $this->author;
  }

  public function getSource() {
    return $this->source;
  }

  public function getDirectName() {
    if ($this->source instanceof Application_Model_Base) {
      return $this->source->getDirectName();
    }
  }

  public function getDirectLink() {
    if ($this->object instanceof Application_Model_Base) {
      return $this->source->getDirectLink();
    }
  }

  public function toArray($hide = array()) {
    $result = array();

    $result["id"] = $this->id;
    $result["text"] = $this->text;
    $result["author"] = $this->author->toArray();
    if (!in_array('source', $hide)) {
      $result["source"] = $this->source->toArray();
    }
    $result["created"] = Unplagged_Helper::jsTime($this->created);
    if (!empty($this->title)) {
      $result["title"] = $this->title;
    }
    $result['type'] = 'comment';
    return $result;
  }

}