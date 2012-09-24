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
   * @ManyToOne(targetEntity="Application_Model_User", cascade={"remove"})
   * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $user;

  /**
     * @ManyToOne(targetEntity="Application_Model_Document")
     * @JoinColumn(name="target_document_id", referencedColumnName="id")
     */
  private $target;

  /**
   * The title of the report.
   * 
   * @Column(type="string")
   */
  private $title;

  /**
   * The report is saved as file
   * 
   * @Column(type="string", nullable=true)
   */
  private $filePath;

  /**
   * @ManyToOne(targetEntity="Application_Model_Case", inversedBy="reports")
   * @JoinColumn(name="case_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $case;
  
  /**
   * Title of the report. 
   * It will be shown on the first page
   * 
   * @Column(type="string", nullable=true)
   */
  private $reportTitle;

  /**
   * Group name of the report. 
   * It will be shown on the first page
   * 
   * @Column(type="string", nullable=true)
   */
  private $reportGroupName;
  
  /**
   * Introduction of the report. 
   * 
   * @Column(type="string", nullable=true)
   */
  private $reportIntroduction;
  
  /**
   * Evaluation text of the report. 
   * 
   * @Column(type="string", nullable=true)
   */
  private $reportEvaluation;
  
  public function __construct($data = array()){
    parent::__construct($data);

    if(isset($data["title"])){
      $this->title = $data["title"];
    }
    if(isset($data["user"])){
      $this->user = $data["user"];
    }
    if(isset($data["target"])){
      $this->target = $data["target"];
    }
    if(isset($data['case'])){
      $this->case = $data['case'];
    }
    if(isset($data["filePath"])){
      $this->filePath = $data["filePath"];
    }
    if(isset($data["reportTitle"])) {
        $this->reportTitle = $data["reportTitle"];
    }
    if(isset($data["reportGroupName"])) {
        $this->reportGroupName = $data["reportGroupName"];
    }
    if(isset($data["reportIntroduction"])) {
        $this->reportIntroduction = $data["reportIntroduction"];
    }   
    if(isset($data["reportEvaluation"])) {
        $this->reportEvaluation = $data["reportEvaluation"];
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

  public function getTitle(){
    return $this->title;
  }

  public function getTarget(){
    return $this->target;
  }

  public function getSource(){
    return $this->source;
  }

  public function getDirectName(){
    return $this->getTitle();
  }

  public function getFilePath(){
    return $this->filePath;
  }

  public function getDirectLink(){
    return "/report/list/id/" . $this->id;
  }

  public function setCase($case){
    $this->case = $case;
  }
 
  public function getCase(){
    return $this->case;  
  }
  
  public function setFilePath($filePath){
    $this->filePath = $filePath;
  }
  
  public function getReportTitle() {
    return $this->reportTitle;
  }
  
  public function getReportGroupName() {
      return $this->reportGroupName;
  }
  
  public function getReportEvaluation() {
      return $this->reportEvaluation;
  }
  
  public function getReportIntroduction() {
      return $this->reportIntroduction;
  }
}