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
 * @Entity 
 * @Table(name="documents")
 * @HasLifeCycleCallbacks
 */
class Application_Model_Document extends Application_Model_Base{

  const ICON_CLASS = 'icon-document';

  /**
   * @var string The title of the document.
   * 
   * @Column(type="string", length=64)
   */
  private $title;

  /**
   * @var ArrayCollection The pages in the document.
   * 
   * @OneToMany(targetEntity="Application_Model_Document_Page", mappedBy="document", fetch="EXTRA_LAZY")
   * @OrderBy({"pageNumber" = "ASC"})
   */
  private $pages;

  /**
   * @var ArrayCollection The fragments in the document.
   * 
   * @OneToMany(targetEntity="Application_Model_Document_Fragment", mappedBy="document")
   */
  private $fragments;

  /**
   * @var string The bibtex information of the document.
   * 
   * @OneToOne(targetEntity="Application_Model_BibTex", cascade={"persist"})
   * @JoinColumn(name="bibtex_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $bibTex;

  /**
   * The current state of the report.
   * 
   * @ManyToOne(targetEntity="Application_Model_State")
   * @JoinColumn(name="state_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $state;

  /**
   * @ManyToOne(targetEntity="Application_Model_Case", inversedBy="documents")
   * @JoinColumn(name="case_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $case;

  /**
   * The file the document was initially created from.
   * 
   * @ManyToOne(targetEntity="Application_Model_File")
   * @JoinColumn(name="initial_file_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $initialFile;
  
  /**
   *
   * @Column(type="string", length=2)
   */
  private $language = 'en';

  public function __construct(array $data = null){

    if(isset($data["title"])){
      $this->title = $data["title"];
    }

    // if(isset($data["bibtex"])){
    // $this->bibtex = $data["bibtex"];
    // }
    if(isset($data["state"])){
      $this->state = $data["state"];
    }
    if(isset($data["initialFile"])){
      $this->initialFile = $data["initialFile"];
    }

    $this->pages = new \Doctrine\Common\Collections\ArrayCollection();
    $this->fragments = new \Doctrine\Common\Collections\ArrayCollection();
  }

  public function getTitle(){
    return $this->title;
  }

  public function getBibTex(){
    return $this->bibTex;
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

  public function setTitle($title){
    $this->title = $title;
  }

  public function getState(){
    return $this->state;
  }

  public function setState($state){
    $this->state = $state;
  }

<<<<<<< HEAD
  public function setBibTex($bibTex){
    $this->bibTex = $bibTex;
  }
=======
  public function getLanguage(){
    return $this->language;  
  }
  
  public function setLanguage($language){
    $this->language = $language;
  }
  
  // public function setBibTex($bibTex){
    // $this->bibTex = $bibTex;
  // }
>>>>>>> 5d8376472d32b3c9eb0b0ddd34e47071408cb9bf

  public function toArray(){
    $data["id"] = $this->id;
    //$data["bibTex"] = $this->bibTex;
    $data["pages"] = array();

    foreach($this->pages as $page){
      $data["pages"][] = $page->toArray();
    }

    return $data;
  }

  public function getPlagiarismPercentage(){
    $pagesCount = $this->pages->count();
    $percentageSum = 0;

    foreach($this->pages as $page){
      $percentageSum += $page->getPlagiarismPercentage();
    }

    return ($pagesCount != 0) ? round($percentageSum * 1. / $pagesCount / 10) * 10 : 0;
  }

  public function setCase($case){
    $this->case = $case;
  }

  public function getInitialFile(){
    return $this->initialFile;
  }

  public function getCase(){
    return $this->case;
  }

  public function getSidebarActions(){
    $actions = array();

    $action['label'] = 'Actions';
    $actions[] = $action;

    $action['link'] = '/document_page/list/id/' . $this->id;
    $action['label'] = 'Show document pages';
    $action['icon'] = 'icon-page';
    $actions[] = $action;

    $action['link'] = '/document/edit/id/' . $this->id;
    $action['label'] = 'Edit document';
    $action['icon'] = 'icon-pencil';
    $actions[] = $action;

    $action['link'] = '/document/delete/id/' . $this->id;
    $action['label'] = 'Remove document';
    $action['icon'] = 'icon-delete';
    $actions[] = $action;

    $action['link'] = '/document_page/create/document/' . $this->id;
    $action['label'] = 'Add new page to document';
    $action['icon'] = 'icon-page-add';
    $actions[] = $action;

    $action['link'] = '/document_page/create/documen/' . $this->id;
    $action['label'] = 'Detect Plagiarism';
    $action['icon'] = 'icon-eye';
    $actions[] = $action;

    return $actions;
  }

}