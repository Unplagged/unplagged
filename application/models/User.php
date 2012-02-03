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
 * The class represents a user.
 * 
 * @Entity 
 * @Table(name="users")
 * @HasLifeCycleCallbacks
 */
class Application_Model_User{

  /**
   * The userId is an unique identifier for each user.
   * @var string The userId.
   * 
   * @Id @GeneratedValue @Column(type="integer")
   */
  protected $id;

  /**
   * The date when the user registered.
   * @var string The registration date.
   * 
   * @Column(type="datetime")
   */
  protected $created;

  /**
   * The username, the user account was modified.
   * @var string The latest modification date.
   * 
   * @Column(type="datetime", nullable=true)
   */
  protected $updated;

  /**
   * The username, the user defined as an alias for the account.
   * @var string The usernamee.
   * 
   * @todo do we need to specify the length? I believe 32 could be a bit short and I heard there are no 
   * performance issues on mysql when we would use 255
   * 
   * @Column(type="string", length=32, unique=true)
   */
  protected $username;

  /**
   * The password the user set up to login to the private area.
   * @var string The password.
   * 
   * @Column(type="string", length=32)
   */
  protected $password;

  /**
   * The email the user set up to login to the private area.
   * @var string The email address.
   * 
   * @Column(type="string", length=32, unique=true)
   */
  protected $email;

  /**
   * The users firstname.
   * @var string The firstname.
   * 
   * @Column(type="string", length=64, nullable=true)
   */
  protected $firstname;

  /**
   * The users lastname.
   * @var string The lastname.
   * 
   * @Column(type="string", length=64, nullable=true)
   */
  protected $lastname;

  /**
   * The users registration hash, used to verify the account.
   * @var string The registration hash.
   * 
   * @Column(type="string", length=32, unique=true)
   */
  protected $verificationHash;

  /**
   * @ManyToOne(targetEntity="Application_Model_User_State")
   * @JoinColumn(name="user_state_id", referencedColumnName="id")
   */
  protected $state;  
  
  /**
   * @ManyToOne(targetEntity="Application_Model_Case")
   * @JoinColumn(name="current_case_id", referencedColumnName="id")
   */
  protected $currentCase;

  public function __construct($data = array()){
    if(isset($data["username"])){
      $this->username = $data["username"];
    }

    if(isset($data["password"])){
      $this->password = $data["password"];
    }

    if(isset($data["email"])){
      $this->email = $data["email"];
    }

    if(isset($data["firstname"])){
      $this->firstname = $data["firstname"];
    }

    if(isset($data["lastname"])){
      $this->lastname = $data["lastname"];
    }

    if(isset($data["verificationHash"])){
      $this->verificationHash = $data["verificationHash"];
    }

    if(isset($data["state"])){
      $this->state = $data["state"];
    }
  }
  
  /**
   * Sets the creation time to the current time, if it hasn't been already set.
   * 
   * @PrePersist
   */
  public function created(){
    if($this->created == null){
      $this->created = new DateTime("now");
    }
  }

  /**
   * Sets the time of the last update to the current time.
   * 
   * @PreUpdate
   */
  public function updated(){
    $this->updated = new DateTime("now");
  }

  public function getId(){
    return $this->id;
  }

  public function getCreated(){
    return $this->created;
  }

  public function getUpdated(){
    return $this->updated;
  }

  public function getUsername(){
    return $this->username;
  }

  public function getPassword(){
    return $this->password;
  }

  public function getEmail(){
    return $this->email;
  }

  public function getFirstname(){
    return $this->firstname;
  }

  public function getLastname(){
    return $this->lastname;
  }

  public function getVerificationHash(){
    return $this->verificationHash;
  }

  public function getState(){
    return $this->state;
  }

  public function setState($state){
    $this->state = $state;
  }

  public function setFirstname($firstname){
    $this->firstname = $firstname;
  }

  public function setLastname($lastname){
    $this->lastname = $lastname;
  }

  public function getCurrentCase(){
    return $this->currentCase;
  }

  public function setCurrentCase($currentCase){
    $this->currentCase = $currentCase;
  }

  public function unsetCurrentCase(){
    $this->currentCase = null;
  }
}