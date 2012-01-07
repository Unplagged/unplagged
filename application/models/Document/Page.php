<?php

/**
 * File for class {@link Application_Model_Document_Page}.
 */

/**
 * The class represents a single page in a document.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 * 
 * @Entity 
 * @Table(name="document_pages")
 */
class Application_Model_Document_Page{

  /**
   * The documentId is an unique identifier for each document.
   * @var string The documentId.
   * 
   * @Id @GeneratedValue @Column(type="integer")
   */
  private $id;

  /**
   * The page number in the origional document.
   * @var integer The page number.
   * 
   * @Column(type="integer")
   */
  private $pageNumber;

  /**
   * @ManyToOne(targetEntity="Application_Model_Document", inversedBy="pages")
   * @JoinColumn(name="document_id", referencedColumnName="id")
   */
  private $document;

  /**
   * The content of the page.
   * 
   * @Column(type="text", nullable="true")
   */
  private $content;

  public function __construct($data){
    if(isset($data["content"])){
      $this->content = $data["content"];
    }

    if(isset($data["pageNumber"])){
      $this->pageNumber = $data["pageNumber"];
    }
    $this->lines = new \Doctrine\Common\Collections\ArrayCollection();
  }

  public function getId(){
    return $this->id;
  }

  public function setDocument(Application_Model_Document $document){
    $this->document = $document;
  }
  
  public function getPageNumber(){
    return $this->pageNumber;
  }

  public function getContent(){
    return $this->content;
  }
  public function getDocument(){
    return $this->document;
  }
  public function setPageNumber($pageNumber){
    $this->pageNumber = $pageNumber;
  }

  public function setContent($content){
    $this->content = $content;
  }
}

?> 