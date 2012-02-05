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
 * The class represents a log entry.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 *
 * @Entity
 * @Table(name="logs")
 * @HasLifeCycleCallbacks
 */
class Application_Model_Log{

  /**
   * The logId is an unique identifier for each log entry.
   * @var string The userId.
   *
   * @Id @GeneratedValue @Column(type="integer")
   */
  protected $id;

  /**
   * The date when the log entry was created.
   * @var string The creation date.
   *
   * @Column(type="datetime")
   */
  protected $created;

  /**
   * @ManyToOne(targetEntity="Application_Model_Log_Action", cascade={"remove"})
   * @JoinColumn(name="log_action_id", referencedColumnName="id")
   */
  protected $action;

  /**
   * @ManyToOne(targetEntity="Application_Model_User", cascade={"remove"})
   * @JoinColumn(name="user_id", referencedColumnName="id")
   */
  protected $user;

  /**
   * A note for the specific log entry.
   * @var string The comment.
   * @access protected
   *
   * @Column(type="string", length=256, nullable=true)
   */
  protected $comment;

  /**
   * Method auto-called when object is persisted to database for the first time.
   *
   * @PrePersist
   */
  public function created(){
    $this->created = new DateTime("now");
  }

  public function __construct($data = array()){
    if(isset($data["action"])){
      $this->action = $data["action"];
    }

    if(isset($data["user"])){
      $this->user = $data["user"];
    }

    if(isset($data["comment"])){
      $this->comment = $data["comment"];
    }
  }
  
  public function getId(){
    return $this->id;
  }

  public function getCreated(){
    return $this->created;
  }

  public function getAction(){
    return $this->action;
  }

  public function getUser(){
    return $this->user;
  }

  public function getComment(){
    return $this->comment;
  }

}