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

  const ICON_CLASS = 'icon-fragment';
  const PERMISSION_TYPE = 'document_fragment';
  
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
   * @JoinColumn(name="fragment_type_id", referencedColumnName="id", onDelete="CASCADE")
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
  
  protected $conversationTypes = array('comment', 'rating');

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
    $alias = $this->getPlag()->getLineFrom()->getPage()->getDocument()->getCase()->getAlias();

    return 'Fragment ' . $alias . ' ' . $this->getPlag()->getLineFrom()->getPage()->getPageNumber();
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

  public function getContent($returnType = 'list', $highlight = true){

    $plagLines = !empty($this->plag) ? $this->plag->getContent() : array();
    $sourceLines = !empty($this->source) ? $this->source->getContent() : array();

    // convert tags to htmlentities, before simtext is called
    if($returnType != 'array' && $returnType != 'text'){
      foreach($plagLines as $lineNumber=>$lineContent){
        $plagLines[$lineNumber] = htmlentities($lineContent, ENT_COMPAT, 'UTF-8');
      }

      foreach($sourceLines as $lineNumber=>$lineContent){
        $sourceLines[$lineNumber] = htmlentities($lineContent, ENT_COMPAT, 'UTF-8');
      }
    }


    // before converting the content to the correct return type, do simtext highlighting if requested
    if($highlight){
      $simtextResult = Unplagged_CompareText::compare($plagLines, $sourceLines, 4);
      // print_r($simtextResult);
      $plagLines = $simtextResult['left'];
      $sourceLines = $simtextResult['right'];
    }

    if($returnType != 'array' && $returnType != 'text'){
      foreach($plagLines as $lineNumber=>$lineContent){
        if($returnType == 'list'){
          $plagLines[$lineNumber] = '<li value="' . $lineNumber . '">' . $lineContent . '</li>';
        }
      }

      foreach($sourceLines as $lineNumber=>$lineContent){
        if($returnType == 'list'){
          $sourceLines[$lineNumber] = '<li value="' . $lineNumber . '">' . $lineContent . '</li>';
        }
      }
    }

    $result = array();
    switch($returnType){
      case 'array':
        $result['plag'] = $plagLines;
        $result['source'] = $sourceLines;
        break;
      case 'htmltext':
        $result['plag'] = implode("<br />", $plagLines);
        $result['source'] = implode("<br />", $sourceLines);
        break;
      case 'text':
        $result['plag'] = implode("\n", $plagLines);
        $result['source'] = implode("\n", $sourceLines);
        break;
      default:
        $result['plag'] = '<ol>' . implode("\n", $plagLines) . '</ol>';
        $result['source'] = '<ol>' . implode("\n", $sourceLines) . '</ol>';
        break;
    }

    return $result;
  }
  
  public function setDocument($document){
    $this->document = $document;
  }



}