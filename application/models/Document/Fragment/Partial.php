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
 * This class represents a continuous block of a document, i. e. a single line or a collection
 * of lines with an indicator of the first and the last character of the block.
 * 
 * It can for example be used to store data of marked plagiarized fragments or highlighted text.
 * 
 * @Entity 
 * @Table(name="document_fragment_partials")
 * @HasLifeCycleCallbacks
 */
class Application_Model_Document_Fragment_Partial extends Application_Model_Base{

  /**
   * @ManyToOne(targetEntity="Application_Model_Document_Page_Line")
   * @JoinColumn(name="line_from_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $lineFrom;

  /**
   * @Column(type="integer", length=64, nullable=true)
   */
  private $characterFrom = 1;

  /**
   * @ManyToOne(targetEntity="Application_Model_Document_Page_Line")
   * @JoinColumn(name="line_to_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $lineTo;

  /**
   * @Column(type="integer", length=64, nullable=true)
   */
  private $characterTo = 1;

  public function __construct($data = array()){
    parent::__construct($data);

    if(isset($data["lineFrom"])){
      $this->lineFrom = $data["lineFrom"];
    }

    if(isset($data["characterFrom"])){
      $this->characterFrom = $data["characterFrom"];
    }

    if(isset($data["lineTo"])){
      $this->lineTo = $data["lineTo"];
    }

    if(isset($data["characterTo"])){
      $this->characterTo = $data["characterTo"];
    }
  }

  public function toArray(){
    $data['lineFrom'] = $this->lineFrom->toArray();
    $data['characterFrom'] = $this->characterFrom;
    $data['lineTo'] = $this->lineTo->toArray();
    $data['characterTo'] = $this->characterTo;

    return $data;
  }

  public function getDirectName(){
    //return "document_page_position";
  }

  public function getDirectLink(){
    //return "/document-page-position/show/id/" . $this->id;
  }

  public function getIconClass(){
    //return "document-icon";
  }

  public function getLineFrom(){
    return $this->lineFrom;
  }

  public function getCharacterFrom(){
    return $this->characterFrom;
  }

  public function getLineTo(){
    return $this->lineTo;
  }

  public function getCharacterTo(){
    return $this->characterTo;
  }

  public function setLineFrom($lineFrom){
    $this->lineFrom = $lineFrom;
  }

  public function setLineTo($lineTo){
    $this->lineTo = $lineTo;
  }

  public function setCharacterFrom($characterFrom){
    $this->characterFrom = $characterFrom;
  }

  public function setCharacterTo($characterTo){
    $this->characterTo = $characterTo;
  }

  /**
   * 
   * 
   * @return string 
   */
  public function getContent(){
    $startPageNumber = $this->lineFrom->getPage()->getPageNumber();
    $endPageNumber = $this->lineTo->getPage()->getPageNumber();

    $result = array();
    foreach($this->lineFrom->getPage()->getDocument()->getPages() as $page){
      if($page->getPageNumber() > $endPageNumber){
        break;
      }

      // iterate over all the pages in between start and end page
      if($page->getPageNumber() >= $startPageNumber){
        foreach($page->getLines() as $line){
          // iterate over all the pages in between start and end page
          if($page->getPageNumber() != $startPageNumber || $line->getLineNumber() >= $this->lineFrom->getLineNumber()){
            $result[$line->getLineNumber()] = $line->getContent();
          }

          // if linenumber on last page is bigger than the last line number
          if($page->getPageNumber() == $endPageNumber && $line->getLineNumber() == $this->lineTo->getLineNumber()){
            break;
          }
        }
      }
    }

    return $result;
  }

}
