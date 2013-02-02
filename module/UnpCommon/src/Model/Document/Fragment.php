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

use \Doctrine\ORM\Mapping as ORM;
use \UnpCommon\Model\Base;
use \UnpCommon\Model\Document;
use \UnpCommon\Model\Feature\ArrayCreator;
use \UnpCommon\Model\Feature\Linkable;
use \Unplagged_CompareText;

/**
 * The class represents a fragment within a document.
 * 
 * @ORM\Entity 
 * @ORM\Table(name="document_fragment")
 */
class Fragment extends Base implements Linkable, ArrayCreator{//Application_Model_Versionable{

  /**
   * @var string The description.
   * @ORM\Column(type="text")
   */
  private $description;

  /**
   * ManyToOne(targetEntity="Application_Model_Document_Fragment_Type")
   * JoinColumn(name="fragment_type_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $type;

  /**
   * The plag partial of the fragment.
   *
   * ORM\OneToOne(targetEntity="Application_Model_Document_Fragment_Partial", cascade={"persist", "remove"})
   * ORM\JoinColumn(name="fragment_partial_plag_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $plag;

  /**
   * The source partial of the fragment.
   *
   * ORM\OneToOne(targetEntity="Application_Model_Document_Fragment_Partial", cascade={"persist", "remove"})
   * ORM\JoinColumn(name="fragment_partial_source_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $source;

  /**
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\Document", inversedBy="fragments"), cascade={"persist"})
   * @ORM\JoinColumn(name="document_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $document;
  protected $conversationTypes = array('comment', 'rating');

  public function __construct($data = array()){
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

  public function toVersionArray(){
    $data["note"] = $this->note;
    $data["type"] = $this->type->toArray();
    $data["content"] = $this->getContent();

    return $data;
  }

  public function getPlag(){
    return $this->plag;
  }

  public function getSource(){
    return $this->source;
  }

  /**
   * @return Application_Model_Fragment_Type
   */
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
      foreach($plagLines as $lineNumber=> $lineContent){
        $plagLines[$lineNumber] = htmlentities($lineContent, ENT_COMPAT, 'UTF-8');
      }

      foreach($sourceLines as $lineNumber=> $lineContent){
        $sourceLines[$lineNumber] = htmlentities($lineContent, ENT_COMPAT, 'UTF-8');
      }
    }


    // before converting the content to the correct return type, do simtext highlighting if requested
    if($highlight){
      $comparer = new Unplagged_CompareText();
      $simtextResult = $comparer->compare($plagLines, $sourceLines, 4);
      // print_r($simtextResult);
      $plagLines = $simtextResult['left'];
      $sourceLines = $simtextResult['right'];
    }

    if($returnType == 'listpdf'){
      foreach($plagLines as $lineNumber=> $lineContent){
        $plagLines[$lineNumber] = '<li>' .
                '<span class="number">' . $lineNumber . '</span>' .
                $lineContent . '</li>';
      }

      foreach($sourceLines as $lineNumber=> $lineContent){
        $sourceLines[$lineNumber] = '<li>' .
                '<span class="number">' . $lineNumber . '</span>' .
                $lineContent . '</li>';
      }
      /* foreach ($plagLines as $lineNumber => $lineContent) {
        $plagLines[$lineNumber] = '<li value="' . $lineNumber . '">' .
        '<div class="number">' . $lineNumber . '</div>' .
        $lineContent . '</li>';
        }

        foreach ($sourceLines as $lineNumber => $lineContent) {
        $sourceLines[$lineNumber] = '<li value="' . $lineNumber . '">' .
        '<div class="number">' . $lineNumber . '</div>' .
        $lineContent . '</li>';
        } */
    }else if($returnType != 'array' && $returnType != 'text'){
      foreach($plagLines as $lineNumber=> $lineContent){
        if($returnType == 'list'){
          $plagLines[$lineNumber] = '<li value="' . $lineNumber . '">' . $lineContent . '</li>';
        }
      }

      foreach($sourceLines as $lineNumber=> $lineContent){
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

  public function getSidebarActions(){
    $actions = array();

    $action['label'] = 'Actions';
    $actions[] = $action;

    $action['link'] = '/document_fragment/show/id/' . $this->id;
    $action['label'] = 'Show fragment';
    $action['icon'] = 'icon-page';
    $actions[] = $action;

    if($this->getState()->getName() != 'approved'){
      $action['link'] = '/document_fragment/edit/id/' . $this->id;
      $action['label'] = 'Edit fragment';
      $action['icon'] = 'icon-pencil';
      $actions[] = $action;
    }

    $action['link'] = '/document_fragment/delete/id/' . $this->id;
    $action['label'] = 'Remove fragment';
    $action['icon'] = 'icon-delete';
    $actions[] = $action;

    $action['link'] = '/document_fragment/changelog/id/' . $this->id;
    $action['label'] = 'Changelog';
    $action['icon'] = 'icon-table-relationship';
    $actions[] = $action;

    return $actions;
  }

  public function toArray(){
    $data = array(
        'description'=>$this->description,
        
    );
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
    return 'icon-fragment';
  }

}