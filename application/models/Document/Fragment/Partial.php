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
 * The class represents a partial (page, line, text) of a fragment within a document.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 * 
 * @Entity 
 * @Table(name="document_fragment_partials")
 * @HasLifeCycleCallbacks
 */
class Application_Model_Document_Fragment_Partial extends Application_Model_Base{

  /**
   *
   * @ManyToOne(targetEntity="Application_Model_Document_Page")
   * @JoinColumn(name="page_from_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $pageFrom;

  /**
   * The line position.
   * 
   * @Column(type="integer", length=64)
   */
  private $lineFrom;

  /**
   * The character position.
   * 
   * @Column(type="integer", length=64, nullable=true)
   */
  private $characterFrom = 1;

  /**
   *
   * @ManyToOne(targetEntity="Application_Model_Document_Page")
   * @JoinColumn(name="page_to_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $pageTo;

  /**
   * The line position.
   * 
   * @Column(type="integer", length=64)
   */
  private $lineTo;

  /**
   * The character position.
   * 
   * @Column(type="integer", length=64, nullable=true)
   */
  private $characterTo = 1;

  /**
   * The text.
   * 
   * @Column(type="text")
   */
  private $text;

  public function __construct(array $data = null){
    if(isset($data["pageFrom"])){
      $this->pageFrom = $data["pageFrom"];
    }

    if(isset($data["lineFrom"])){
      $this->lineFrom = $data["lineFrom"];
    }

    if(isset($data["characterFrom"])){
      $this->characterFrom = $data["characterFrom"];
    }

    if(isset($data["pageTo"])){
      $this->pageTo = $data["pageTo"];
    }

    if(isset($data["lineTo"])){
      $this->lineTo = $data["lineTo"];
    }

    if(isset($data["characterTo"])){
      $this->characterTo = $data["characterTo"];
    }

    if(isset($data["text"])){
      $this->text = $data["text"];
    }
  }

  public function toArray(){
    $data["pageFrom"] = $this->pageFrom->toArray();
    $data["lineFrom"] = $this->lineFrom;
    $data["characterFrom"] = $this->characterFrom;
    $data["pageTo"] = $this->pageTo->toArray();
    $data["lineTo"] = $this->lineTo;
    $data["characterTo"] = $this->characterTo;
    $data["text"] = $this->text;

    return $data;
  }

  public function getDirectName(){
    //return "document_page_position";
  }

  public function getDirectLink(){
    //return "/document-page-position/show/id/" . $this->id;
  }

  public function getPageFrom(){
    return $this->pageFrom;
  }

  public function getLineFrom(){
    return $this->lineFrom;
  }

  public function getCharacterFrom(){
    return $this->characterFrom;
  }

  public function getPageTo(){
    return $this->pageTo;
  }

  public function getLineTo(){
    return $this->lineTo;
  }

  public function getCharacterTo(){
    return $this->characterTo;
  }

  public function getText(){
    return $this->text;
  }

}