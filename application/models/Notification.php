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
 * @Table(name="notifications")
 */
class Application_Model_Notification extends Application_Model_Base{

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
   * @ManyToOne(targetEntity="Application_Model_Base")
   * @JoinColumn(name="source_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $source;

  public function __construct($data = array()){
    if(isset($data["user"])){
      $this->user = $data["user"];
    }

    if(isset($data["action"])){
      $this->action = $data["action"];
    }

    if(isset($data["source"])){
      $this->source = $data["source"];
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

  public function getIconClass(){
    return "notification-icon";
  }

  public function getTitle(){
    switch($this->action->getName()){
      case "user_registered":
        return "User %s registered";
      case "user_verified":
        return "User %s verified";
      case "user_requested_password":
        return "User %s requested password";
      case "case_created":
        return "Case %s was created";
      case "file_uploaded":
        return "File %s was uploaded";
      case "fragment_created":
        return "Fragment %s was created";
    }
  }

  public function getMessage(){
    switch($this->action->getName()){
      case "user_registered":
        return "The user created a new account on the website.";
      case "user_verified":
        return "The user verified his account.";
      case "user_requested_password":
        return "The user requested a new password for his account.";
      case "case_created":
        return "A new Case was created.";
      case "file_uploaded":
        return "A new file was uploaded to the files area.";
      case "fragment_created":
        return "A new fragment was created";
    }
  }
  
  /*public function getTitle(){
    switch($this->action->getName()){
      case "user_registered":
        return "User " . $this->getSource()->getUsername() . " registered";
      case "user_verified":
        return "User " . $this->getSource()->getUsername() . " verified";
      case "user_requested_password":
        return "User " . $this->getSource()->getUsername() . " requested password";
      case "case_created":
        return "Case " . $this->getSource()->getPublishableName() . " was created";
      case "file_uploaded":
        return "File " . $this->getSource()->getFilename() . " was uploaded";
      case "fragment_created":
        return "Fragment " . $this->getSource()->getTitle() . " was created";
    }
  }

  public function getMessage(){
    switch($this->action->getName()){
      case "user_registered":
        return "User " . $this->getSource()->getUsername() . " registered.";
      case "user_verified":
        return "User " . $this->getSource()->getUsername() . " verified.";
      case "user_requested_password":
        return "User " . $this->getSource()->getUsername() . " requested password.";
      case "case_created":
        return "Case " . $this->getSource()->getPublishableName() . " was created.";
      case "file_uploaded":
        return "File " . $this->getSource()->getFilename() . " was uploaded.";
      case "fragment_created":
        return "Fragment " . $this->getSource()->getTitle() . " was created";
    }
  }*/

  public function toArray(){
    $result = array();

    $result["id"] = $this->id;

    return $result;
  }

}