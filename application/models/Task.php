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
 * This class represents a task, which is most likely to be triggered by a user.
 * 
 * It defines also the structure of the database table for the ORM.
 * 
 * @Entity 
 * @Table(name="cron_tasks")
 */
class Application_Model_Task extends Application_Model_Base{

  /**
   * @ManyToOne(targetEntity="Application_Model_User")
   * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $initiator;

  /**
   * @var string The date when the task finished.
   * @Column(type="datetime", nullable=true)
   */
  private $endDate;

  /**
   * @var The action that has to be executed by this task.
   * @ManyToOne(targetEntity="Application_Model_Action")
   * @JoinColumn(name="action_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $action;

  /**
   * @ManyToOne(targetEntity="Application_Model_Base", cascade={"persist"})
   * @JoinColumn(name="resource_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $resource;

  /**
   * @var string The log message returned by the cron execution.
   * @Column(type="text", nullable=true)
   */
  private $log;
  
  /**
   * @var string The current progress in percent of the task.
   * @Column(type="integer")
   */
  private $progressPercentage = 0;

  public function __construct($data = array()){
    parent::__construct($data);
    
    if(isset($data['initiator'])){
      $this->initiator = $data['initiator'];
    }

    if(isset($data['action'])){
      $this->action = $data['action'];
    }

    if(isset($data['resource'])){
      $this->resource = $data['resource'];
    }
  }

  public function getDirectLink(){
    
  }

  public function getDirectName(){
    
  }

  public function getInitiator(){
    return $this->initiator;
  }

  public function getEndDate(){
    return $this->endDate;
  }

  public function getAction(){
    return $this->action;
  }

  public function getResource(){
    return $this->resource;
  }

  public function getLog(){
    return $this->log;
  }

  public function setEndDate($endDate){
    $this->endDate = $endDate;
  }

  public function setLog($log){
    $this->log = $log;
  }
  
  public function getProgressPercentage(){
    return $this->progressPercentage;
  }

  public function setProgressPercentage($progressPercentage){
    $this->progressPercentage = $progressPercentage;
  }
}