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
 * @Table(name="document_page_detection_reports")
 * @HasLifeCycleCallbacks
 */
class Application_Model_Document_Page_DetectionReport extends Application_Model_Base{

  const ICON_CLASS = 'icon-report';
  
  /**
   * The percentage of plagiarism in this page.
   * @var integer The percentage of plagiarism.
   * 
   * @Column(type="decimal", scale=2, nullable=true)
   */
  private $percentage;

  /**
   * The used service that did the detection.
   * @var string The servicename.
   * 
   * @Column(type="string", length=64)
   */
  private $servicename;

  /**
   * The current state of the report.
   * 
   * @ManyToOne(targetEntity="Application_Model_State")
   * @JoinColumn(name="state_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $state;

  /**
   * @ManyToOne(targetEntity="Application_Model_Document_Page", inversedBy="detection_reports")
   * @JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $page;

  /**
   * @ManyToOne(targetEntity="Application_Model_User")
   * @JoinColumn(name="user_id", referencedColumnName="id")
   */
  protected $user;

  /**
   * The content of the page.
   * 
   * @Column(type="text", nullable=true)
   */
  private $content;

  public function __construct(&$data){
    if(isset($data["content"])){
      $this->content = $data["content"];
    }
    if(isset($data["percentage"])){
      $this->percentage = $data["percentage"];
    }
    if(isset($data["servicename"])){
      $this->servicename = $data["servicename"];
    }
    if(isset($data["page"])){
      $this->page = $data["page"];
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

  public function getDirectName(){
    return $this->getContent();
  }

  public function getDirectLink(){
    return "/document-page-detection-report/show/id/" . $this->id;
  }

}