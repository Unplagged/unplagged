<?php

/**
 * File for class {@link Application_Model_Log_Action}.
 */

/**
 * The class represents a log action.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 * 
 * @Entity 
 * @Table(name="log_actions")
 */
class Application_Model_Log_Action{

  /**
   * The logActionId is an unique identifier for each log action.
   * @var string The log action id.
   * 
   * @Id @GeneratedValue @Column(type="integer")
   */
  private $id;

  /**
   * The module for the log acction.
   * @var string The module.
   * 
   * @Column(type="string", length=32)
   */
  private $module;

  /**
   * A title for the log acction.
   * @var string The usernamee.
   * 
   * @Column(type="string", unique="true", length=32)
   */
  private $title;

  /**
   * A description for the log action.
   * @var string The description.
   * 
   * @Column(type="string", length=256)
   */
  private $description;

  public function __construct($data = array()){
    if(isset($data["module"])){
      $this->module = $data["module"];
    }

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

  public function getModule(){
    return $this->module;
  }

  public function getTitle(){
    return $this->title;
  }

  public function getDescription(){
    return $this->description;
  }

}