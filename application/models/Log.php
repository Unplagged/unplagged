<?php

/**
 * File for class {@link Application_Model_Log}.
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