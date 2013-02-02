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
use \UnpCommon\Model\Base;
use \UnpCommon\Model\Document;
use \UnpCommon\Model\Feature\ArrayCreator;
use \UnpCommon\Model\Feature\Linkable;

/**
 * This class can be used to store metadata of a document.
 * 
 * @ORM\Entity 
 * @ORM\Table(name="bibliographic_information")
 */
class BibliographicInformation extends Base implements Linkable, ArrayCreator{

  /**
   * ORM\OneToOne(targetEntity="\UnpCommon\Model\Document", mappedBy="reference")
   */
  private $document;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $sourceType;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $address = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $author = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $booktitle = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $chapter = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $edition = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $editor = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $howpublished = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $isbn = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $issn = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $journal = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $key = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $note = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $number = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $pages = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $publisher = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $series = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $title = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $url = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $volume = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $year = '';

  /**
   * @var array This array defines in which order the fields are returned.
   */
  public static $accessibleFields = array(
      'author'=>array('label'=>'Author', 'required'=>true),
      'title'=>array('label'=>'Title', 'required'=>true),
      'year'=>array('label'=>'Year', 'required'=>false),
      'month'=>array('label'=>'Month', 'required'=>false),
      'day'=>array('label'=>'Day', 'required'=>false),
      'journal'=>array('label'=>'Journal', 'required'=>false),
      'volume'=>array('label'=>'Volume', 'required'=>false),
      'number'=>array('label'=>'Number', 'required'=>false),
      'publisher'=>array('label'=>'Publisher', 'required'=>false),
      'address'=>array('label'=>'Address', 'required'=>false),
      'series'=>array('label'=>'Series', 'required'=>false),
      'edition'=>array('label'=>'Edition', 'required'=>false),
      'booktitle'=>array('label'=>'Booktitle', 'required'=>false),
      'editor'=>array('label'=>'Editor', 'required'=>false),
      'pages'=>array('label'=>'Pages', 'required'=>false),
      'isbn'=>array('label'=>'ISBN', 'required'=>false),
      'issn'=>array('label'=>'ISSN', 'required'=>false),
      'url'=>array('label'=>'URL', 'required'=>false),
      'key'=>array('label'=>'Key', 'required'=>false),
      'note'=>array('label'=>'Note', 'required'=>false),
  );
  public static $sourceTypes = array(
      'full'=>array('author', 'title', 'year', 'month', 'day', 'journal', 'volume', 'number', 'publisher', 'address', 'series', 'edition', 'booktitle', 'editor', 'pages', 'isbn', 'issn', 'url', 'key', 'note'),
      'book'=>array('author', 'year', 'title', 'isbn', 'publisher', 'address', 'url', 'note'),
      'periodical'=>array('author', 'title', 'journal', 'volume', 'number', 'year', 'month', 'day', 'pages', 'publisher', 'issn', 'url', 'note'),
      'essay'=>array('author', 'title', 'year', 'volume', 'hrsg', 'pages', 'publisher', 'address', 'isbn', 'url', 'note')
  );

  public function getDocument(){
    return $this->document;
  }

  public function setDocument(Document $document){
    $this->document = $document;
  }

  public function getSourceType(){
    return $this->sourceType;
  }

  public function setSourceType($sourceType){
    $this->sourceType = $sourceType;
  }

  /**
   * @param string $fieldName
   * @return string
   * 
   * @todo I'm still not totally convinced, that having some kind of magic accessor here is really a good idea,
   * but I'm also too lazy right now, to write all the getters and setters..
   */
  public function getContent($fieldName){
    if(!empty($this->$fieldName)){
      return $this->$fieldName;
    }
  }

  public function setContent($content, $fieldName){
    if(is_string($content) && !method_exists($this, 'get' . ucfirst($fieldName))){
      $this->$fieldName = $content;
    }
  }

  public function getDirectLink(){
    return '';
  }

  public function getDirectName(){
    return '';
  }

  public function getIconClass(){
    return 'icon-bibtex';
  }

  public function toArray(){
    return get_class_vars('\UnpCommon\Model\BibliographicInformation');
  }

}