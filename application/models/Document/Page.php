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
 * The class represents a single page in a document.
 * It defines also the structure of the database table for the ORM.
 * 
 * @Entity 
 * @Table(name="document_pages")
 */
class Application_Model_Document_Page extends Application_Model_Versionable{

  const ICON_CLASS = 'icon-page';
  const PERMISSION_TYPE = 'document_page';

  /**
   * The page number in the origional document.
   * @var integer The page number.
   * 
   * @Column(type="integer")
   */
  private $pageNumber;

  /**
   * @ManyToOne(targetEntity="Application_Model_Document", inversedBy="pages")
   * @JoinColumn(name="document_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $document;

  /**
   * The lines in the document.
   * 
   * @OneToMany(targetEntity="Application_Model_Document_Page_DetectionReport", mappedBy="page")
   * @OrderBy({"created" = "ASC"})
   */
  private $detectionReports;

  /**
   * The file the document was initially created from.
   * 
   * @ManyToOne(targetEntity="Application_Model_File")
   * @JoinColumn(name="original_file_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $originalFile;

  /**
   * The lines in the document.
   * 
   * @OneToMany(targetEntity="Application_Model_Document_Page_Line", mappedBy="page", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
   * @OrderBy({"lineNumber" = "ASC"})
   */
  private $lines;

  /**
   * Defines whether the page is disabled for being used in reports and barcodes.
   * @var bool Whether the page is disabled or not.
   * 
   * @Column(type="boolean")
   */
  private $disabled = false;

  public function __construct($data = null){
    parent::__construct();

    if(isset($data["disabled"])){
      $this->disabled = $data["disabled"];
    }

    if(isset($data["file"])){
      $this->originalFile = $data["file"];
    }

    if(isset($data["pageNumber"])){
      $this->pageNumber = $data["pageNumber"];
    }
    $this->lines = new \Doctrine\Common\Collections\ArrayCollection();
  }

  public function toArray($appendLines = false){
    $data["id"] = $this->id;
    $data["pageNumber"] = $this->pageNumber;

    if($appendLines == true){
      $data["lines"] = array();

      foreach($this->lines as $line){
        $data["lines"][] = $line->toArray();
      }
    }
    //@todo $data["file"] = ...
    return $data;
  }

  public function getId(){
    return $this->id;
  }

  public function getOriginalFile(){
    return $this->originalFile;
  }

  public function setDocument(Application_Model_Document $document){
    $this->document = $document;
  }

  public function getPageNumber(){
    return $this->pageNumber;
  }

  public function getDocument(){
    return $this->document;
  }

  public function setPageNumber($pageNumber){
    $this->pageNumber = $pageNumber;
  }

  public function getDisabled(){
    return $this->disabled;
  }

  public function setDisabled($disabled){
    $this->disabled = $disabled;
  }

  public function getDirectName(){
    return "page";
  }

  public function getDirectLink(){
    return "/document_page/show/id/" . $this->id;
  }

  public function getLines(){
    return $this->lines;
  }

  public function addLine(Application_Model_Document_Page_Line $line){
    $line->setPage($this);
    $this->lines->add($line);
  }

  public function removeLine(Application_Model_Document_Page_Line $line){
    $this->lines->removeElement($line);
  }

  public function removeLineByIndex($lineId){
    $this->lines->remove($lineId);
  }

  /**
   * Return the content of all lines in the page.
   * 
   * @param array|string $returnType Can be list (<li>), array or text (<br /> as linebreaks)
   * 
   * @return string or array, as specified in the input param 
   */
  public function getContent($returnType = 'list'){
    $result = array();

    if($returnType == 'array' || $returnType == 'text'){
      foreach($this->lines as $line){
        $result[$line->getLineNumber()] = $line->getContent();
      }
    }else if($returnType == 'htmltext'){
      foreach($this->lines as $line){
        $result[$line->getLineNumber()] = htmlentities($line->getContent(), ENT_COMPAT, 'UTF-8');
      }
    }else{
      foreach($this->lines as $line){
        $result[$line->getLineNumber()] = '<li value="' . $line->getLineNumber() . '">' . htmlentities($line->getContent(), ENT_COMPAT, 'UTF-8') . '</li>';
      }
    }

    switch($returnType){
      case 'array':
        return $result;
      case 'htmltext':
        return implode("<br />", $result);
      case 'text':
        return implode("\n", $result);
      default:
        return '<ol>' . implode("\n", $result) . '</ol>';
    }
  }

  /**
   * Sets the content of all lines in the page, new lines are automatically created and
   * non-existing ones are removed.
   * 
   * @param string|array $content The content of the page
   * @param string $inputType The format of the content (array, text, htmltext)
   * @return type 
   */
  public function setContent($content, $inputType = 'text'){
    $lines = array();
    if($inputType == 'text'){
      $lines = explode("\n", $content);
    }else if($inputType == 'htmltext'){
      $lines = explode("<br />", $content);
    }else if($inputType == 'array'){
      $lines = $content;
    }

    $lineNumbers = array();
    foreach($lines as $key=>$value){
      $lineNumbers[$key + 1] = $key + 1;
    }

    $removedLines = array();

    // 1) search all lines that already exist by their line numbers and update them
    $this->lines->filter(function($line) use (&$lineNumbers, &$lines, &$removedLines){
          if(in_array($line->getLineNumber(), $lineNumbers)){
            $line->setContent($lines[$line->getLineNumber() - 1]);
            unset($lineNumbers[$line->getLineNumber()]);
            return true;
          }
          $removedLines[] = $line;
          return false;
        });

    // 2) create new lines for those that don't exist yet
    foreach($lineNumbers as $lineNumber){
      $data['lineNumber'] = $lineNumber;
      $data['content'] = $lines[$lineNumber - 1];

      $line = new Application_Model_Document_Page_Line($data);
      $this->addLine($line);
    }

    // 3) remove lines that were in the page before, but not anymore
    foreach($removedLines as $line){
      $this->removeLine($line);
      Zend_Registry::getInstance()->entitymanager->remove($line);
    }
  }

  /**
   * Return the percentage of plagiarism on this page.
   * 
   * @return percentage value of plagiarism in 10-percent steps
   */
  public function getPlagiarismPercentage(){
    // amount of all lines
    $linesCount = $this->lines->count();
    $plagCount = 0;

    // amount of plagiarised lines
    // @todo: select only approved fragments here
    $plagLines = array();
    $em = Zend_Registry::getInstance()->entitymanager;
    $query = $em->createQuery('
      SELECT f FROM Application_Model_Document_Fragment f 
      JOIN f.plag pl
      JOIN pl.lineFrom lfrom JOIN lfrom.page pfrom
      JOIN pl.lineTo lto JOIN lto.page pto
      WHERE :pn >= pfrom.pageNumber AND :pn <= pto.pageNumber AND pfrom.document = :documentId');
    $query->setParameter("pn", $this->pageNumber);
    $query->setParameter("documentId", $this->document->getId());
    $fragments = $query->getResult();

    foreach($fragments as $fragment){
      $startPageNumber = $fragment->getPlag()->getLineFrom()->getPage()->getPageNumber();
      $startLineNumber = $fragment->getPlag()->getLineFrom()->getLineNumber();
      $endPageNumber = $fragment->getPlag()->getLineTo()->getPage()->getPageNumber();
      $endLineNumber = $fragment->getPlag()->getLineTo()->getLineNumber();

      $firstLineNumber = $this->lines->first()->getLineNumber();
      $lastLineNumber = $this->lines->last()->getLineNumber();

      // 1) page number is in between two pages, the whole page is plagiarised
      if($startPageNumber < $this->pageNumber && $endPageNumber > $this->pageNumber){
        $plagLines = $this->lines;

        // 2) start page number and end page number are on this page
      }elseif($startPageNumber == $this->pageNumber && $endPageNumber == $this->pageNumber){
        $this->updatePlagLines($startLineNumber, $endLineNumber, $plagLines);

        // 3) page number is page number of start line and end page number is larger
      }elseif($startPageNumber == $this->pageNumber && $endPageNumber > $this->pageNumber){
        $this->updatePlagLines($startLineNumber, $lastLineNumber, $plagLines);

        // 4) page number is page number somewhere between and end page number is on the same page
      }elseif($startPageNumber == $this->pageNumber && $endPageNumber > $this->pageNumber){
        $this->updatePlagLines($firstLineNumber, $endLineNumber, $plagLines);
      }
    }
    $plagCount = sizeof($plagLines);

    return ($plagCount != 0) ? round($plagCount * 1. / $linesCount * 10) * 10 : 0;
  }

  private function updatePlagLines($startLineNumber, $endLineNumber, &$plagLines){
    $this->lines->filter(function($line) use (&$plagLines, $startLineNumber, $endLineNumber){
          if($line->getLineNumber() >= $startLineNumber && $line->getLineNumber() <= $endLineNumber){
            // add line number to result array
            $plagLines[$line->getLineNumber()] = $line->getLineNumber();
            return true;
          }
          return false;
        });
  }

}