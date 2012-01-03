<?php

/**
 * File for class {@link Application_Model_Document}.
 */

/**
 * The class represents a line of text in a document.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 * 
 * @Entity 
 * @Table(name="documents")
 * @HasLifeCycleCallbacks
 */
class Application_Model_Document{

  /**
   * The documentId is an unique identifier for each document.
   * @var string The documentId.
   * 
   * @Id @GeneratedValue @Column(type="integer")
   */
  private $id;
  
  
  private $mimeType;
  
  /**
   * @ManyToOne(targetEntity="Application_Model_File")
   * @JoinColumn(name="original_file_id", referencedColumnName="id")
   */
  private $origionalFile;

  /**
   * The lines in the document.
   * 
   * @OneToMany(targetEntity="Application_Model_Document_Page", mappedBy="document")
   * @OrderBy({"pageNumber" = "ASC"})
   */
  private $pages;

  public function __construct($data){
    if(isset($data["file"])){
      $this->origionalFile = $data["file"];
    }

    if(isset($data["title"])){
      $this->title = $data["title"];
    }    $this->pages = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * Method auto-called when object is persisted to database for the first time.
   * 
   * @PrePersist
   */
  public function created(){
    $this->created = new DateTime("now");
  }

  public function getId(){
    return $this->id;
  }

  public function getOriginalData(){
    return 'originalData';
  }
  
  public function getTitle(){
    return $this->title;
  }
  
  public function addPage(Application_model_Document_Page $page){
    $page->setDocument($this);
    $this->pages->add($page);
  }
}

?> 