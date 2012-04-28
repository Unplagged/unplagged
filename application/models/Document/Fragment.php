<?php

use DoctrineExtensions\Versionable\Versionable;

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
 * The class represents a fragment within a document.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 * 
 * @Entity 
 * @Table(name="document_fragments")
 * @HasLifeCycleCallbacks
 */
class Application_Model_Document_Fragment extends Application_Model_Versionable{

  /**
   * The note.
   * @var string The note.
   * 
   * @Column(type="text")
   */
  private $note;

  /**
   * The lines in the document.
   * 
   * @ManyToOne(targetEntity="Application_Model_Document_Fragment_Type")
   * @JoinColumn(name="fragment_type_id", referencedColumnName="id")
   */
  private $type;

  /**
   * The plag partial of the fragment.
   *
   * @OneToOne(targetEntity="Application_Model_Document_Fragment_Partial", cascade={"persist", "remove"})
   * @JoinColumn(name="fragment_partial_plag_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $plag;

  /**
   * The source partial of the fragment.
   *
   * @OneToOne(targetEntity="Application_Model_Document_Fragment_Partial", cascade={"persist", "remove"})
   * @JoinColumn(name="fragment_partial_source_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $source;

  /**
   * @ManyToOne(targetEntity="Application_Model_Document", inversedBy="fragments")
   * @JoinColumn(name="document_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $document;

  public function __construct(array $data = null){
    parent::__construct();
    if(isset($data["plag"])){
      $this->plag = $data["plag"];
    }

    if(isset($data["source"])){
      $this->source = $data["source"];
    }

    if(isset($data["note"])){
      $this->note = $data["note"];
    }

    if(isset($data["type"])){
      $this->type = $data["type"];
    }
  }

  public function toArray(){
    $data["plag"] = $this->plag->toArray();
    $data["source"] = $this->source->toArray();
    $data["note"] = $this->note;
    $data["type"] = $this->type->toArray();

    return $data;
  }

  public function getDirectName(){
    return $this->getTitle();
  }

  public function getDirectLink(){
    return "/document_fragment/show/id/" . $this->id;
  }

  public function getIconClass(){
    return "fragment-icon";
  }

  public function getPlag(){
    return $this->plag;
  }

  public function getSource(){
    return $this->source;
  }

  public function getType(){
    return $this->type;
  }

  public function getTitle(){
    return "ABC" . $this->getPlag()->getPageFrom()->getPageNumber();
  }

  public function getNote(){
    return $this->note;
  }
  
  public function setNote($note){
    $this->note = $note;
  }

  public function setType($type){
    $this->type = $type;
  }
  
  public function setPlag($plag){
    $this->plag = $plag;
  }

  public function setSource($source){
    $this->source = $source;
  }





}