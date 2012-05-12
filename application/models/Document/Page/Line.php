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
 * The class represents a single line in a document.
 * It defines also the structure of the database table for the ORM.
 * 
 * @Entity 
 * @Table(name="document_page_lines")
 */
class Application_Model_Document_Page_Line extends Application_Model_Base{

  /**
   * The line number in the page.
   * @var integer The line number.
   * 
   * @Column(type="integer")
   */
  private $lineNumber;

  /**
   * @ManyToOne(targetEntity="Application_Model_Document_Page", inversedBy="lines", cascade={"persist"})
   * @JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $page;

  /**
   * The content of the line.
   * 
   * @Column(type="text", nullable=true)
   */
  private $content;

  public function __construct($data){
    parent::__construct();

    if(isset($data["lineNumber"])){
      $this->lineNumber = $data["lineNumber"];
    }
    if(isset($data["page"])){
      $this->page = $data["page"];
    }
    if(isset($data["content"])){
      $this->content = $data["content"];
    }
  }

  public function toArray(){
    $data["id"] = $this->id;
    $data["lineNumber"] = $this->lineNumber;
    $data["content"] = $this->content;
    
    return $data;
  }

  public function getId(){
    return $this->id;
  }

  public function getDirectName(){
    return null;
  }

  public function getDirectLink(){
    return null;
  }

  public function getIconClass(){
    return null;
  }

  public function getLineNumber(){
    return $this->lineNumber;
  }

  public function setLineNumber($lineNumber){
    $this->lineNumber = $lineNumber;
  }

  public function getContent(){
    return $this->content;
  }

  public function setContent($content){
    $this->content = $content;
  }

  public function setPage($page){
    $this->page = $page;
  }
  public function getPage(){
    return $this->page;
  }



}