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
namespace UnpCommon\Model;

use \Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;
use \UnpCommon\Model\Base;
use \UnpCommon\Model\BibliographicInformation;
use \UnpCommon\Model\Document\Fragment;
use \UnpCommon\Model\Document\Page;
use \UnpCommon\Model\Feature\ArrayCreator;
use \UnpCommon\Model\Feature\Linkable;
use \UnpCommon\Model\File;
use \UnpCommon\Model\PlagiarismCase;
use \UnpCommon\Model\State;

/**
 * The class represents a single document.
 * 
 * @ORM\Entity 
 * @ORM\Table(name="document")
 * @ORM\HasLifeCycleCallbacks
 */
class Document extends Base implements Linkable, ArrayCreator{

  /**
   * @var string The title of the document.
   * @ORM\Column(type="string", length=255)
   */
  private $title = '';

  /**
   * @var ArrayCollection The pages in the document.
   * @ORM\OneToMany(targetEntity="\UnpCommon\Model\Document\Page", mappedBy="document", fetch="EXTRA_LAZY")
   * @ORM\OrderBy({"pageNumber" = "ASC"})
   */
  private $pages;

  /**
   * @var ArrayCollection The fragments in the document.
   * 
   * @ORM\OneToMany(targetEntity="\UnpCommon\Model\Document\Fragment", mappedBy="document")
   */
  private $fragments;

  /**
   * @var BibliographicInformation Contains the bibliographic meta information 
   * of the document
   * @ORM\OneToOne(targetEntity="\UnpCommon\Model\BibliographicInformation", cascade={"persist"})
   * @ORM\JoinColumn(name="bibliographicInformation_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $bibliographicInformation;

  /**
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\PlagiarismCase", inversedBy="documents")
   * @ORM\JoinColumn(name="case_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $case;

  /**
   * @var The file the document was initially created from.
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\File")
   * @ORM\JoinColumn(name="source_file_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $sourceFile;

  /**
   * @var An ISO code for the main language of this document.
   * @ORM\Column(type="string", length=5)
   */
  private $language = 'en';

  public function __construct($title = '', File $sourceFile = null, $language = 'en', State $state = null){
    parent::__construct();

    $this->title = $title;
    $this->state = $state;
    $this->sourceFile = $sourceFile;
    $this->language = $language;

    $this->bibliographicInformation = new BibliographicInformation();
    $this->pages = new ArrayCollection();
    $this->fragments = new ArrayCollection();
  }

  /**
   * @return string
   */
  public function getTitle(){
    return $this->title;
  }

  /**
   * @param string  $title
   */
  public function setTitle($title = ''){
    $this->title = $title;
  }

  /**
   * @return BibliographicInformation
   */
  public function getBibliographicInformation(){
    return $this->bibliographicInformation;
  }

  /**
   * @return array
   */
  public function getPages(){
    return $this->pages->toArray();
  }

  /**
   * @param Page $page
   */
  public function addPage(Page $page){
    if(!$this->pages->contains($page)){
      $this->pages->add($page);
    }
  }

  /**
   * Finds the page number of the given page in this document.
   * 
   * @param Page $page
   * @return boolean
   */
  public function getPageNumber(Page $page){
    return $this->pages->indexOf($page) + 1;
  }

  /**
   * @return array
   */
  public function getFragments(){
    return $this->fragments->toArray();
  }

  /**
   * @param Fragment $fragment
   */
  public function addFragment(Fragment $fragment){
    if(!$this->fragments->contains($fragment)){
      $fragment->setDocument($this);
      $this->fragments->add($fragment);
    }
  }

  /**
   * @return string ISO 639-1 language code
   */
  public function getLanguage(){
    return $this->language;
  }

  /**
   * @param string $language ISO 639-1 language code
   */
  public function setLanguage($language){
    $this->language = $language;
  }

  /**
   * @return int
   * @todo create a service layer that calculates this
   */
  /* public function getPlagiarismPercentage(){
    $pagesCount = $this->pages->count();
    $percentageSum = 0;

    foreach($this->pages as $page){
    $percentageSum += $page->getPlagiarismPercentage();
    }

    return ($pagesCount != 0) ? round($percentageSum * 1. / $pagesCount / 10) * 10 : 0;
    } */

  /**
   * @return File
   */
  public function getSourceFile(){
    return $this->sourceFile;
  }

  /**
   * @return PlagiarismCase
   */
  public function getCase(){
    return $this->case;
  }

  /**
   * @param PlagiarismCase $case
   */
  public function setCase(PlagiarismCase $case){
    $this->case = $case;
  }

  /**
   * @return array
   * 
   * @todo move to config file somehow?
   */
  public function getSidebarActions(){
    $actions = array();

    $action['label'] = 'Actions';
    $actions[] = $action;

    $action['link'] = '/document_page/list/id/' . $this->id;
    $action['label'] = 'Show document pages';
    $action['icon'] = 'icon-page';
    $actions[] = $action;

    $action['link'] = '/document/edit/id/' . $this->id;
    $action['label'] = 'Edit document';
    $action['icon'] = 'icon-pencil';
    $actions[] = $action;

    $action['link'] = '/document/delete/id/' . $this->id;
    $action['label'] = 'Remove document';
    $action['icon'] = 'icon-delete';
    $actions[] = $action;

    $action['link'] = '/document_page/create/document/' . $this->id;
    $action['label'] = 'Add new page to document';
    $action['icon'] = 'icon-page-add';
    $actions[] = $action;

    $action['link'] = '/document/detect-plagiarism/id/' . $this->id;
    $action['label'] = 'Detect plagiarism';
    $action['icon'] = 'icon-eye';
    $actions[] = $action;

    return $actions;
  }

  public function toArray(){
    $data = array(
        'id'=>$this->id,
        'bibliographic_information'=>$this->bibliographicInformation->toArray(),
        'pages'=>array(),
    );

    foreach($this->pages as $page){
      $data['pages'][] = $page->toArray();
    }

    return $data;
  }

  public function getDirectName(){
    return $this->title;
  }

  public function getDirectLink(){
    return '/document_page/list/id/' . $this->id;
  }

  public function getIconClass(){
    return 'fam-icon-book';
  }

}