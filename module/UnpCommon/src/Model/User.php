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

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use UnpCommon\Model\Feature\ArrayCreator;
use UnpCommon\Model\Base;
use UnpCommon\Model\Feature\DataEntity;
use UnpCommon\Model\Feature\Linkable;
use UnpCommon\Model\Feature\UpdateTracker;
use UnpCommon\Model\File;
use UnpCommon\Model\PlagiarismCase;
use UnpCommon\Model\State;
use UnpCommon\Model\User;
use Zend\Paginator\ScrollingStyle\All;
use ZfcUser\Entity\UserInterface as ZfcUser;

/**
 * This class represents a user of the Unplagged system.
 * 
 * @ORM\Entity(repositoryClass="\UnpCommon\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User extends Base implements Linkable, DataEntity, ZfcUser, UpdateTracker, ArrayCreator{

  /**
   * @ORM\Column(type="string", length=255, unique=true)
   */
  private $username;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $encryptedPassword;

  /**
   * @ORM\Column(type="string", length=255, unique=true)
   */
  private $email;

  /**
   * @var Role 
   * 
   * ORM\OneToOne(targetEntity="\UnpCommon\Model\User\Role", inversedBy="user", cascade={"persist", "remove"})
   */
  private $role;

  /**
   * @var PlagiarismCase The case this user selected
   * to work on currently.
   * 
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\PlagiarismCase")
   * @ORM\JoinColumn(name="current_case_id", referencedColumnName="id")
   */
  private $currentCase;

  /**
   * ORM\ManyToMany(targetEntity="\UnpCommon\Model\File") 
   * ORM\JoinTable(name="user_has_file",
   *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="file_id", referencedColumnName="id")}
   *      )
   * ORM\OrderBy({"created" = "DESC"})
   */
  private $files;

  /**
   * @var All the cases, the user is taking part in.
   * 
   * @ORM\ManyToMany(targetEntity="\UnpCommon\Model\PlagiarismCase", mappedBy="collaborators")
   */
  private $cases;

  /**
   * @var string The date when this user got last modified.
   * 
   * @ORM\Column(type="datetime", nullable=true)
   */
  private $updated;

  public function __construct(array $data = array()){
    parent::__construct();

    if(isset($data['username'])){
      $this->username = $data['username'];
    }
    if(isset($data['password'])){
      $this->encryptedPassword = $data['password'];
    }
    if(isset($data['email'])){
      $this->email = $data['email'];
    }
    $this->files = new ArrayCollection();
    $this->cases = new ArrayCollection();

    //$this->role = new Role();
  }

  /**
   * Implemented because of the interface, but should actually do
   * nothing as Doctrine should handle this.
   * 
   * @param int $id
   * @return User
   */
  public function setId($id){
    return $this;
  }

  /**
   * @return DateTime
   */
  public function getUpdated(){
    return $this->updated;
  }

  /**
   * Sets the time of the last update to the current time.
   * 
   * @ORM\PreUpdate
   */
  public function updated(){
    $this->updated = new DateTime('now');
  }

  /**
   * @return string
   */
  public function getUsername(){
    return $this->username;
  }

  /**
   * @param string $username
   * @return User
   */
  public function setUsername($username){
    $this->username = $username;
    return $this;
  }

  /**
   * @return string
   */
  public function getPassword(){
    return $this->encryptedPassword;
  }

  /**
   * @param string $password
   */
  public function setPassword($password){
    $this->encryptedPassword = $password;
  }

  /**
   * @return string
   */
  public function getEmail(){
    return $this->email;
  }

  /**
   * @param string $email
   * @return User
   */
  public function setEmail($email){
    $this->email = $email;
    return $this;
  }

  /**
   * @return PlagiarismCase
   */
  public function getCurrentCase(){
    return $this->currentCase;
  }

  /**
   * @param PlagiarismCase $currentCase
   */
  public function setCurrentCase(PlagiarismCase $currentCase = null){
    $this->currentCase = $currentCase;
  }

  /**
   * @param File $file
   */
  public function addFile(File $file){
    if(!$this->files->contains($file)){
      $this->files->add($file);
    }
  }

  /**
   * @param File $file
   */
  public function removeFile(File $file){
    if($this->files->contains($file)){
      $this->files->removeElement($file);
    }
  }

  /**
   * @return ArrayCollection
   */
  public function getFiles(){
    return $this->files;
  }

  /**
   * @return bool
   */
  public function hasFiles(){
    return $this->files->count() > 0;
  }

  /**
   * @param File $file
   * @return bool
   */
  public function containsFile(File $file){
    return $this->files->contains($file);
  }

  /**
   * @return \UnpCommon\Model\Role
   */
  public function getRole(){
    return $this->role;
  }

  /**
   * Overwritten here to fit the ZfcUser interface, that doesn't
   * include the actual type in the parameter.
   * 
   * @param State $state
   */
  public function setState($state){
    $this->state = $state;
  }

  /**
   * Implemented because of the interface, but should actually never
   * be as we don't use a displayname.
   * 
   * @param string $displayName
   * @return User
   */
  public function getDisplayName(){
    return null;
  }

  /**
   * Implemented because of the interface, but should actually never
   * be called as we don't use a displayname.
   * 
   * @param string $displayName
   * @return User
   */
  public function setDisplayName($displayName){
    return $this;
  }
  
  /**
   * @return array
   */
  public function toArray(){
    $result = array(
        'id'=> $this->id,
        'username'=> $this->username,
    );

    return $result;
  }

  public function getDirectName(){
    return $this->username;
  }

  public function getDirectLink(){
    return '/user/show/id/' . $this->id;
  }

  public function getIconClass(){
    return 'icon-user';
  }

}