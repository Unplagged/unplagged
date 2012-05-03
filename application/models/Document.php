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
 * The class represents a single document.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 * 
 * @Entity 
 * @Table(name="documents")
 * @HasLifeCycleCallbacks
 */
class Application_Model_Document extends Application_Model_Base{

  /**
   * The title of the document.
   * @var string The title.
   * 
   * @Column(type="string", length=64)
   */
  private $title;

  /**
   * The lines in the document.
   * 
   * @OneToMany(targetEntity="Application_Model_Document_Page", mappedBy="document")
   * @OrderBy({"pageNumber" = "ASC"})
   */
  private $pages;

  /**
   * The fragments in the document.
   * 
   * @OneToMany(targetEntity="Application_Model_Document_Fragment", mappedBy="fragment")
   */
  private $fragments;

  /**
   * The bibtex information of the document.
   * @var string The bibtex information.
   * 
   * @Column(type="text", nullable=true)
   */
  private $bibtex;

  /**
   * The current state of the report.
   * 
   * @ManyToOne(targetEntity="Application_Model_State")
   * @JoinColumn(name="state_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $state;
  
  /**
   * The file the document was initially created from.
   * 
   * @ManyToOne(targetEntity="Application_Model_File")
   * @JoinColumn(name="original_file_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $originalFile;

  public function __construct(array $data = null){

    if(isset($data["title"])){
      $this->title = $data["title"];
    }

    if(isset($data["bibtex"])){
      $this->bibtex = $data["bibtex"];
    }
    if(isset($data["state"])){
      $this->state = $data["state"];
    }
    if(isset($data["originalFile"])){
      $this->originalFile = $data["originalFile"];
    }

    $this->pages = new \Doctrine\Common\Collections\ArrayCollection();
    $this->fragments = new \Doctrine\Common\Collections\ArrayCollection();
  }

  public function getId(){
    return $this->id;
  }

  public function getTitle(){
    return $this->title;
  }

  public function getBibtex(){
    return $this->bibtex;
  }

  public function getPages(){
    return $this->pages;
  }

  public function addPage(Application_Model_Document_Page $page){
    $page->setDocument($this);
    $this->pages->add($page);
  }

  public function getFragments(){
    return $this->fragments;
  }

  public function addFragment(Application_Model_Document_Fragment $fragment){
    $fragment->setDocument($this);
    $this->fragments->add($fragment);
  }

  public function getDirectName(){
    return $this->title;
  }

  public function getDirectLink(){
    return "/document_page/list/id/" . $this->id;
  }

  public function getIconClass(){
    return "document-icon";
  }

  public function setTitle($title){
    $this->title = $title;
  }

  public function getState(){
    return $this->state;
  }

  public function setState($state){
    $this->state = $state;
  }
  
  public function getOriginalFile(){
    return $this->originalFile;
  }
}