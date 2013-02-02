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
namespace UnpCommon\Model\Document;

use \UnpCommon\Model\Base;
use \Doctrine\ORM\Mapping as ORM;

/**
 * 
 * Entity 
 * @ORM\Table(name="document_detection_report")
 * @ORM\HasLifeCycleCallbacks
 */
class DetectionReport extends Base{
  
  /**
   * The percentage of plagiarism in this page.
   * @var integer The percentage of plagiarism.
   * 
   * @ORM\Column(type="decimal", scale=2, nullable=true)
   */
  private $percentage;

  /**
   * The used service that did the detection.
   * @var string The servicename.
   * 
   * @ORM\Column(type="string", length=64)
   */
  private $servicename;

  /**
   * @ORM\ManyToOne(targetEntity="Application_Model_Document_Page", inversedBy="detection_reports")
   * @JORM\oinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $page;

  /**
   * @ORM\ManyToOne(targetEntity="Application_Model_User")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
   */
  protected $user;

  /**
   * The content of the page.
   * 
   * @ORM\Column(type="text", nullable=true)
   */
  private $content;

  public function __construct($data = array()){
    parent::__construct($data);
    
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
    if(isset($data["user"])){
      $this->user = $data["user"];
    }
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

  public function getDirectName(){
    return $this->getContent();
  }

  public function getDirectLink(){
    return '/document-page-detection-report/show/id/' . $this->id;
  }
  
  public function getIconClass(){
    return 'icon-report';
  }

}