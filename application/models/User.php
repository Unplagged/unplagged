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
 * This class represents a user of the Unplagged system.
 * 
 * @Entity 
 * @Table(name="users")
 * @HasLifeCycleCallbacks
 */
class Application_Model_User extends Application_Model_Base{

  const ICON_CLASS = 'icon-user';
  
  /**
   * @var string The date when this user got last modified.
   * 
   * @Column(type="datetime", nullable=true)
   */
  private $updated;

  /**
   * @var string The username, the user defined as an alias for the account.
   * 
   * @Column(type="string", length=255, unique=true)
   */
  private $username;

  /**
   * @var string The password the user set up to login to the private area in an encrypted version.
   * 
   * @Column(type="string", length=255)
   */
  private $encryptedPassword;

  /**
   * @var string The email the user set up to login to the private area.
   * 
   * @Column(type="string", length=255, unique=true)
   */
  private $email;

  /**
   * @var string The users firstname.
   * 
   * @Column(type="string", length=64, nullable=true)
   */
  private $firstname;

  /**
   * @var string The users lastname.
   * 
   * @Column(type="string", length=64, nullable=true)
   */
  private $lastname;

  /**
   * @var string The users registration hash, used to verify the account.
   * 
   * @Column(type="string", length=32, unique=true)
   */
  private $verificationHash;

  /**
   * @var Application_Model_State The current state of the user.
   * 
   * @ManyToOne(targetEntity="Application_Model_State")
   * @JoinColumn(name="state_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $state;

  /**
   * @var Application_Model_User_Role 
   * 
   * @OneToOne(targetEntity="Application_Model_User_Role", cascade={"persist", "remove"})
   */
  private $role;

  /**
   * @var Application_Model_File
   * 
   * @ManyToOne(targetEntity="Application_Model_File")
   * @JoinColumn(name="user_avatar_id", referencedColumnName="id")
   */
  private $avatar;

  /**
   * @var Application_Model_Case
   * 
   * @ManyToOne(targetEntity="Application_Model_Case")
   * @JoinColumn(name="current_case_id", referencedColumnName="id")
   */
  private $currentCase;

  /**
   * @ManyToMany(targetEntity="Application_Model_File") 
   * @JoinTable(name="user_has_file",
   *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="file_id", referencedColumnName="id")}
   *      )
   */
  private $files;
  
  /**
   * @var The personal settings of the user, e. g. the prefered language.
   * 
   * OneToMany(targetEntity="Application_Model_Setting") 
   */
  private $settings;

  public function __construct($data = array()){
    if(isset($data['username'])){
      $this->username = $data['username'];
    }

    if(isset($data['password'])){
      $this->encryptedPassword = $data['password'];
    }

    if(isset($data['email'])){
      $this->email = $data['email'];
    }

    if(isset($data['firstname'])){
      $this->firstname = $data['firstname'];
    }

    if(isset($data['lastname'])){
      $this->lastname = $data['lastname'];
    }

    if(isset($data['verificationHash'])){
      $this->verificationHash = $data['verificationHash'];
    }

    if(isset($data['state'])){
      $this->state = $data['state'];
    }

    $this->files = new \Doctrine\Common\Collections\ArrayCollection();

    if(isset($data['role']) && $data['role'] instanceof Application_Model_User_Role){
      $this->role = $data['role'];
    }else{
      $this->role = new Application_Model_User_Role();
    }
  }
  
  public function getUpdated(){
    return $this->updated;
  }
  
  /**
   * Sets the time of the last update to the current time.
   * 
   * @PreUpdate
   */
  public function updated(){
    $this->updated = new DateTime('now');
  }

  public function getUsername(){
    return $this->username;
  }
  
  public function getFirstname(){
    return $this->firstname;
  }

  public function setFirstname($firstname){
    $this->firstname = $firstname;
  }
  
  public function getLastname(){
    return $this->lastname;
  }

  public function setLastname($lastname){
    $this->lastname = $lastname;
  }

  public function getPassword(){
    return $this->encryptedPassword;
  }

  public function setPassword($password){
    $this->encryptedPassword = $password;
  }

  public function getEmail(){
    return $this->email;
  }

  public function getVerificationHash(){
    return $this->verificationHash;
  }
  
  public function setVerificationHash($verificationHash){
    $this->verificationHash = $verificationHash;
  }

  public function getState(){
    return $this->state;
  }

  public function setState($state){
    $this->state = $state;
  }

  public function getCurrentCase(){
    return $this->currentCase;
  }

  public function setCurrentCase($currentCase){
    $this->currentCase = $currentCase;
  }


  public function addFile(Application_Model_File $file){
    return $this->files->add($file);
  }

  public function removeFile(Application_Model_File $file){
    return $this->file->removeElement($file);
  }

  public function getFiles(){
    return $this->files;
  }

  public function clearFiles(){
    $this->files->clear();
  }

  public function hasFiles(){
    if($this->files->count() > 0){
      return true;
    }else{
      return false;
    }
  }

  /**
   *
   * @todo Would probably be better to handle the default somewhere else to keep this class clean
   */
  public function getAvatar(){
    if(empty($this->avatar)){
      return '/images/default-avatar.png';
    }

    return '/image/view/' . $this->avatar->getId();
  }

  public function getDirectName(){
    return $this->username;
  }

  public function getDirectLink(){
    
    return '/user/show/id/' . $this->id;
  }

  public function toArray(){
    $result = array();

    $result['id'] = $this->id;
    $result['username'] = $this->username;
    $result['avatar'] = $this->getAvatar();

    return $result;
  }

  public function getRole(){
    return $this->role;
  }

}