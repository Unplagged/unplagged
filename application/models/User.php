<?php

/**
 * File for class {@link Application_Model_User}.
 */

/**
 * The class represents a user.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
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
  private $id;

  /**
   * The date when the user registered.
   * @var string The registration date.
   * 
   * @Column(type="datetime")
   */
  private $created;

  /**
   * The username, the user account was modified.
   * @var string The latest modification date.
   * 
   * @Column(type="datetime")
   */
  private $updated;

  /**
   * The username, the user defined as an alias for the account.
   * @var string The usernamee.
   * 
   * @Column(type="string", length=32, unique=true)
   */
  private $username;

  /**
   * The password the user set up to login to the private area.
   * @var string The password.
   * 
   * @Column(type="string", length=32)
   */
  private $password;

  /**
   * The email the user set up to login to the private area.
   * @var string The email address.
   * 
   * @Column(type="string", length=32, unique=true)
   */
  private $email;

  /**
   * The users firstname.
   * @var string The firstname.
   * 
   * @Column(type="string", length=64, nullable=true)
   */
  private $firstname;

  /**
   * The users lastname.
   * @var string The lastname.
   * 
   * @Column(type="string", length=64, nullable=true)
   */
  private $lastname;

  /**
   * The users registration hash, used to verify the account.
   * @var string The registration hash.
   * 
   * @Column(type="string", length=32, unique=true)
   */
  private $verificationHash;

  /**
   * @ManyToOne(targetEntity="Application_Model_User_State")
   * @JoinColumn(name="user_state_id", referencedColumnName="id")
   */
  private $state;

  /**
   * Method auto-called when object is persisted to database for the first time.
   * 
   * @PrePersist
   */
  public function created(){
    $this->created = new DateTime("now");
  }

  /**
   * Method auto-called when object is updated in database.
   * 
   * @PrePersist
   */
  public function updated(){
    $this->updated = new DateTime("now");
  }

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
}