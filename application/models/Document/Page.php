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
 * The class represents a single page in a document.
 * It defines also the structure of the database table for the ORM.
 * 
 * @Entity 
 * @Table(name="document_pages")
 */
class Application_Model_Document_Page extends Application_Model_Base{

  /**
   * The page number in the origional document.
   * @var integer The page number.
   * 
   * @Column(type="integer")
   */
  private $pageNumber;

  /**
   * @ManyToOne(targetEntity="Application_Model_Document", inversedBy="pages")
   * @JoinColumn(name="document_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $document;
  
  /**
   * The lines in the document.
   * 
   * @OneToMany(targetEntity="Application_Model_Document_Page_DetectionReport", mappedBy="page")
   * @OrderBy({"created" = "ASC"})
   */
  protected $detectionReports;
  
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
    public function getDirectName(){
    return "page";
  }
    public function getDirectLink(){
    return "/document_page/show/id/" . $this->id;
  }

  public function getIconClass(){
    return "page-icon";
  }
}

?> 