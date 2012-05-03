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
 * @Table(name="document_page_simtext_reports")
 * @HasLifeCycleCallbacks
 */
class Application_Model_Document_Page_SimtextReport extends Application_Model_Base{

  const ICON_CLASS = 'report-icon';
  
  /**
   * The current state of the report.
   * 
   * @ManyToOne(targetEntity="Application_Model_State")
   * @JoinColumn(name="state_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $state;

  /**
   * @ManyToOne(targetEntity="Application_Model_Document_Page", inversedBy="simtext_reports", cascade={"persist"})
   * @JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $page;

  /**
   * @ManyToOne(targetEntity="Application_Model_User", cascade={"remove"})
   * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $user;

  /**
   * The documents, to compare the page against.
   * 
   * @Column(type="array", nullable=true)
   */
  private $documents;

  /**
   * The title of the report.
   * 
   * @Column(type="string")
   */
  private $title;
  
  /**
   * The content of the report.
   * 
   * @Column(type="text", nullable=true)
   */
  private $content;

  public function __construct(&$data){
    if(isset($data["content"])){
      $this->content = $data["content"];
    }
    if(isset($data["title"])){
      $this->title = $data["title"];
    }
    if(isset($data["page"])){
      $this->page = $data["page"];
    }
    if(isset($data["documents"])){
      $this->documents = $data["documents"];
    }
    if(isset($data["state"])){
      $this->state = $data["state"];
    }
    if(isset($data["user"])){
      $this->user = $data["user"];
    }
  }

  public function getId(){
    return $this->id;
  }

  public function getContent(){
    return $this->content;
  }

  public function getPage(){
    return $this->page;
  }

  public function getPercentage(){
    return $this->percentage;
  }

  public function setContent($content){
    $this->content = $content;
  }

  public function setPage(Application_Model_Document_Page $page){
    $this->page = $page;
  }

  public function setPercentage($percentage){
    $this->percentage = $percentage;
  }

  public function getServicename(){
    return $this->servicename;
  }

  public function setServicename($servicename){
    $this->servicename = $servicename;
  }

  public function getUser(){
    return $this->user;
  }

  public function getState(){
    return $this->state;
  }

  public function setState($state){
    $this->state = $state;
  }
  
  public function getTitle(){
    return $this->title;
  }

  public function getDocuments(){
    return $this->documents;
  }

    
  public function getDirectName(){
    return $this->getTitle();
  }

  public function getDirectLink(){
    return "/document_page/simtext-reports/id/" . $this->page->getId() . "/show/" . $this->id;
  }

}