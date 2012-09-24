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
 * @Entity
 * @Table(name="notifications")
 */
class Application_Model_Notification extends Application_Model_Base{

  const ICON_CLASS = 'icon-notification';
  
  /**
   * @ManyToOne(targetEntity="Application_Model_User")
   * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $user;

  /**
   * @ManyToOne(targetEntity="Application_Model_Action")
   * @JoinColumn(name="action_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $action;

  /**
   * The element this notification is related to.
   *
   * @ManyToOne(targetEntity="Application_Model_Base", inversedBy="notifications")
   * @JoinColumn(name="source_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $source;

    /**
   * The element the permission is checked on.
   *
   * @ManyToOne(targetEntity="Application_Model_Base", inversedBy="notifications")
   * @JoinColumn(name="permission_source_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $permissionSource;
  
  public function __construct($data = array()){
    parent::__construct($data);
    
    if(isset($data["user"])){
      $this->user = $data["user"];
    }

    if(isset($data["action"])){
      $this->action = $data["action"];
    }

    if(isset($data["source"])){
      $this->source = $data["source"];
    }
    
    if(isset($data["permissionSource"])){
      $this->permissionSource = $data["permissionSource"];
    }
  }

  public function getId(){
    return $this->id;
  }

  public function getUser(){
    return $this->user;
  }

  public function getSource(){
    return $this->source;
  }

  public function getDirectName(){
    return "notification";
  }

  public function getDirectLink(){
    return "/notification/show/id/" . $this->id;
  }

  public function getTitle(){
    return $this->action->getTitle();
  }

  public function getMessage(){
    return $this->action->getDescription();
  }

  public function toArray(){
    $result = array();

    $result["id"] = $this->id;

    return $result;
  }

}