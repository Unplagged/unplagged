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
use \UnpCommon\Model\Feature\ArrayCreator;

/**
 * This class represents a continuous block of a document, i. e. a single 
 * line or a collection of lines with an indicator of the first and the 
 * last character of the block.
 * 
 * It can for example be used to store data of marked plagiarized fragments 
 * or highlighted text.
 * 
 * @ORM\Entity 
 * @ORM\Table(name="document_mark")
 * @ORM\HasLifeCycleCallbacks
 */
class Mark extends Base implements ArrayCreator{

  /**
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\Document\Line")
   * @ORM\JoinColumn(name="start_line_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $startLine;

  /**
   * @var the index of the character on the start line where this Mark starts
   * @ORM\Column(type="integer", nullable=true)
   */
  private $startCharacterIndex = 0;

  /**
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\Document\Line")
   * @ORM\JoinColumn(name="end_line_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $endLine;

  /**
   * @var the index of the character on the end line where this Mark ends
   * @ORM\Column(type="integer", nullable=true)
   */
  private $endCharacterIndex = 0;

  public function __construct(Line $startLine, Line $endLine, $startCharacterIndex = 0, $endCharacterIndex = 0){
    parent::__construct();

    $this->startLine = $startLine;
    $this->endLine = $endLine;
    $this->startCharacterIndex = $startCharacterIndex;
    $this->endCharacterIndex = $endCharacterIndex;
  }

  /**
   * @return \UnpCommon\Model\Document\Line
   */
  public function getStartLine(){
    return $this->startLine;
  }

  /**
   * @param \UnpCommon\Model\Document\Line $lineFrom
   */
  public function setStartLine(Line $lineFrom){
    $this->lineFrom = $lineFrom;
  }

  /**
   * @return \UnpCommon\Model\Document\Line
   */
  public function getEndLine(){
    return $this->endLine;
  }

  /**
   * @param \UnpCommon\Model\Document\Line $endLine
   */
  public function setEndLine(Line $endLine){
    $this->endLine = $endLine;
  }

  /**
   * @return int
   */
  public function getStartCharacterIndex(){
    return $this->startCharacterIndex;
  }

  /**
   * @param int $startCharacterIndex
   */
  public function setStartCharacterIndex($startCharacterIndex){
    $this->startCharacterIndex = $startCharacterIndex;
  }

  /**
   * @return int
   */
  public function getEndCharacterIndex(){
    return $this->endCharacterIndex;
  }

  /**
   * @param int $endCharacterIndex
   */
  public function setEndCharacterIndex($endCharacterIndex){
    $this->endCharacterIndex = $endCharacterIndex;
  }

  /**
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

  public function toArray(){
    $data['lineFrom'] = $this->lineFrom->toArray();
    $data['characterFrom'] = $this->characterFrom;
    $data['lineTo'] = $this->lineTo->toArray();
    $data['characterTo'] = $this->characterTo;

    return $data;
  }

}
