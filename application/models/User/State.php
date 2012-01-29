<?php

/**
 * File for class {@link Application_Model_User_State}.
 */

/**
 * The class represents a user state.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 * 
 * @Entity 
 * @Table(name="user_states")
 */
class Application_Model_User_State{

  /**
   * The logActionId is an unique identifier for each user state.
   * @var string The user state id.
   * $access protected
   * 
   * @Id @GeneratedValue @Column(type="integer")
   */
  private $id;

  /**
   * A title for the user state.
   * @var string The user state.
   * 
   * @Column(type="string", unique="true", length=32)
   */
  private $title;

  /**
   * A description for the user state.
   * @var string The user state.
   * 
   * @Column(type="string", length=256)
   */
  protected $description;

  public function __construct($data = array()){
    if(isset($data["title"])){
      $this->title = $data["title"];
    }

    if(isset($data["description"])){
      $this->description = $data["description"];
    }
  }

  public function getId(){
    return $this->id;
  }

  public function getTitle(){
    return $this->title;
  }

  public function getDescription(){
    return $this->description;
  }

}