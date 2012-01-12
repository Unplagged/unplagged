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
  
  /**
   * The date when the document was created.
   * @var string The creation date.
   * 
   * @Column(type="datetime")
   */
  private $created;
    /**
   * The title of the document.
   * @var string The title.
   * 
   * @Column(type="string", length=64)
   */
  private $title;
  
  /**
   * @ManyToOne(targetEntity="Application_Model_File")
   * @JoinColumn(name="original_file_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $originalFile;

  /**
   * The lines in the document.
   * 
   * @OneToMany(targetEntity="Application_Model_Document_Page", mappedBy="document")
   * @OrderBy({"pageNumber" = "ASC"})
   */
  private $pages;

  public function __construct(array $data=null){
    if(isset($data["file"])){
      $this->originalFile = $data["file"];
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
  
  public function addPage(Application_Model_Document_Page $page){
    $page->setDocument($this);
    $this->pages->add($page);
  }
  public function getPages(){
    return $this->pages;
  }
}

?> 