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
 * Represents a single line in a document.
 * 
 * @todo Is having the line number here really necessary, couldn't we ask the page to calculate this?
 * 
 * @Entity 
 * @Table(name="document_page_lines")
 */
final class Application_Model_Document_Page_Line extends Application_Model_Base{
    
  /**
   * @var int The line number relative to the page.
   * @Column(type="integer")
   */
  private $lineNumber;

  /**
   * @var Application_Model_Document_Page The page this line comes from.
   * @ManyToOne(targetEntity="Application_Model_Document_Page", inversedBy="lines", cascade={"persist"})
   * @JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $page;

  /**
   * @var string The content of the line.
   * @Column(type="text", nullable=true)
   */
  private $content;

  public function __construct($data = array()){
    parent::__construct($data);

    if(isset($data['lineNumber'])){
      $this->lineNumber = $data['lineNumber'];
    }
    if(isset($data['page'])){
      $this->page = $data['page'];
    }
    if(isset($data['content'])){
      $this->content = $data['content'];
    }
  }

  /**
   * @return array
   */
  public function toArray(){
    $data = array();
    $data['id'] = $this->id;
    $data['lineNumber'] = $this->lineNumber;
    $data['content'] = $this->content;
    
    return $data;
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

  /**
   * @return int
   * @todo Check if necessary. See above.
   */
  public function getLineNumber(){
    return $this->lineNumber;
  }

  /**
   * @param int $lineNumber
   * @todo Check if necessary. See above.
   */
  public function setLineNumber($lineNumber){
    $this->lineNumber = $lineNumber;
  }

  /**
   * @return string
   */
  public function getContent(){
    return $this->content;
  }

  /**
   * @param string $content
   */
  public function setContent($content){
    $this->content = $content;
  }

  /**
   * @param Application_Model_Document_Page $page The page this line is on.
   */
  public function setPage(Application_Model_Document_Page $page){
    $this->page = $page;
  }
  
  /**
   * @return Application_Model_Document_Page The page this line is on.
   */
  public function getPage(){
    return $this->page;
  }
}