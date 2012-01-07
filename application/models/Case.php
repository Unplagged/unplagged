<?php

/**
 * File for class {@link InvestigationCase}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 * 
 * @Entity
 * @Table(name="cases")
 * @HasLifeCycleCallbacks
 */
class Application_Model_Case{

  /** @Id @GeneratedValue @Column(type="integer")  */
  private $id;

  /**
   * The "real" name of the case, under which it will get published later on.
   * 
   * @var string
   * 
   * @Column(type="string") 
   */
  private $name = '';

  /**
   * The alias is used to show everyone who doesn't have the permission to see the real case name.
   * 
   * @var string
   * 
   * @Column(type="string") 
   */
  private $alias = '';

  /**
   * @var string  
   */
  private $state = '';

  /**
   * The date when the document was created.
   * @var string The creation date.
   * 
   * @Column(type="datetime")
   */
  private $created;

  /**
   * The date when the document was updated the last time.
   * @var string The update date.
   * 
   * @Column(type="datetime", nullable=true)
   */
  private $updated;

  /**
   * @ManyToMany(targetEntity="Application_Model_Document")
   * @JoinTable(name="case_has_document",
   *      joinColumns={@JoinColumn(name="case_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="document_id", referencedColumnName="id")}
   *      )
   */
  private $documents;

  /**
   * @ManyToMany(targetEntity="Application_Model_File")
   * @JoinTable(name="case_has_file",
   *      joinColumns={@JoinColumn(name="case_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="file_id", referencedColumnName="id")}
   *      )
   */
  private $files;

  public function __construct($name, $alias){
    $this->documents = new \Doctrine\Common\Collections\ArrayCollection();
    $this->files = new \Doctrine\Common\Collections\ArrayCollection();
    $this->name = $name;
    $this->alias = $alias;
  }

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
   * @PreUpdate
   */
  public function updated(){
    $this->updated = new DateTime("now");
  }

  /**
   * @return string 
   */
  public function getName(){
    return $this->name;
  }

  /**
   * @return string
   */
  public function getAlias(){
    return $this->alias;
  }

  /**
   * @return string
   */
  public function getState(){
    return $this->state;
  }

}

?>
