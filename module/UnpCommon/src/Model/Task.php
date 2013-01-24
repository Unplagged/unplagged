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
namespace UnpCommon\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use UnpCommon\Model\Action;
use UnpCommon\Model\Base;
use UnpCommon\Model\User;

/**
 * This class represents a task that needs processeing later on. Most likely this will be retrieved from within a 
 * scheduled cronjob or a similar asynchronous service, that needs access to it's data.
 * 
 * @ORM\Entity 
 * @ORM\Table(name="cron_task")
 * 
 * @todo Check whether extending Base is really necessary
 */
class Task extends Base{

  /**
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\User")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $initiator;

  /**
   * @var string The date when the task finished.
   * @ORM\Column(type="datetime", nullable=true)
   */
  private $endDate;

  /**
   * @var The action that has to be executed by this task.
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\Action")
   * @ORM\JoinColumn(name="action_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $action;

  /**
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\Base", cascade={"persist"})
   * @ORM\JoinColumn(name="resource_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $resource;

  /**
   * @var string The log message returned by the cron execution.
   * @ORM\Column(type="text", nullable=true)
   */
  private $log;

  /**
   * @var string The current progress of the task in percent.
   * @ORM\Column(type="integer")
   */
  private $progressPercentage = 0;

  public function __construct(User $initiator = null, Action $action = null, Base $resource = null){
    parent::__construct();
    
    $this->initiator = $initiator;
    $this->action = $action;
    $this->resource = $resource;
  }

  /**
   * @return User
   */
  public function getInitiator(){
    return $this->initiator;
  }

  /**
   * @return Action
   */
  public function getAction(){
    return $this->action;
  }

  /**
   * @return Base
   */
  public function getResource(){
    return $this->resource;
  }

  /**
   * @return DateTime
   */
  public function getEndDate(){
    return $this->endDate;
  }

  /**
   * @param DateTime $endDate
   */
  public function setEndDate(DateTime $endDate){
    $this->endDate = $endDate;
  }

  /**
   * @return string
   */
  public function getLog(){
    return $this->log;
  }

  /**
   * @param string $log
   */
  public function setLog($log){
    $this->log = $log;
  }

  /**
   * @return int
   */
  public function getProgressPercentage(){
    return $this->progressPercentage;
  }

  /**
   * @param int $progressPercentage
   */
  public function setProgressPercentage($progressPercentage){
    $this->progressPercentage = $progressPercentage;
  }

}