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
 * The class represents a report of a file.
 * It defines also the structure of the database table for the ORM.
 * 
 * @Entity 
 * @Table(name="reports")
 * @HasLifeCycleCallbacks
 */
class Application_Model_Report extends Application_Model_Base{

  const ICON_CLASS = 'icon-report';
  
  /**
   * The current state of the report.
   * 
   * @ManyToOne(targetEntity="Application_Model_State")
   * @JoinColumn(name="state_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $state;

  /**
   * @ManyToOne(targetEntity="Application_Model_User", cascade={"remove"})
   * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $user;
  
  
  /**
   * @OneToOne(targetEntity="Application_Model_File", cascade={"remove"})
   * @JoinColumn(name="file_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $file;

  /**
   * The title of the report.
   * 
   * @Column(type="string")
   */
  private $title;
  
  /**
   * The report is saved as file
   * 
   * @Column(type="string")
   */
  private $filePath;
  
 
  public function __construct(&$data){
    
    if(isset($data["title"])){
      $this->title = $data["title"];
    }
    
    if(isset($data["state"])){
      $this->state = $data["state"];
    }
    if(isset($data["user"])){
      $this->user = $data["user"];
    }
	 if(isset($data["file"])){
      $this->file = $data["file"];
    }
	
	if(isset($data["filePath"])){
      $this->file = $data["filePath"];
    }
  }

  public function getId(){
    return $this->id;
  }

  public function getPercentage(){
    return $this->percentage;
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

  public function getFile(){
    return $this->file;
  }
  
  public function getSource(){
    return $this->source;
  }

    
  public function getDirectName(){
    return $this->getTitle();
  }

  public function getDirectLink(){
    return "/report/list/" . $this->id;
  }

}