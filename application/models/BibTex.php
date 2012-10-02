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
 * The class represents a single document.
 * It defines also the structure of the database table for the ORM.
 * 
 * @Entity 
 * @Table(name="bibtex")
 * @HasLifeCycleCallbacks
 */
class Application_Model_BibTex extends Application_Model_Base{

  const ICON_CLASS = 'icon-bibtex';

  /**
   * @OneToOne(targetEntity="Application_Model_Document", mappedBy="bibTex")
   */
  private $document;

  /**
   * @Column(type="string", length=255)
   */
  private $sourceType;

  /**
   * @Column(type="string", length=255)
   */
  private $bAuthor;

  /**
   * @Column(type="string", length=255)
   */
  private $bTitle;

  /**
   * @Column(type="string", length=255)
   */
  private $bYear;

  /**
   * @Column(type="string", length=255)
   */
  private $bMonth;

  /**
   * @Column(type="string", length=255)
   */
  private $bDay;

  /**
   * @Column(type="string", length=255)
   */
  private $bJournal;

  /**
   * @Column(type="string", length=255)
   */
  private $bVolume;

  /**
   * @Column(type="string", length=255)
   */
  private $bNumber;

  /**
   * @Column(type="string", length=255)
   */
  private $bPublisher;

  /**
   * @Column(type="string", length=255)
   */
  private $bAddress;

  /**
   * @Column(type="string", length=255)
   */
  private $bSeries;

  /**
   * @Column(type="string", length=255)
   */
  private $bEdition;

  /**
   * @Column(type="string", length=255)
   */
  private $bBooktitle;

  /**
   * @Column(type="string", length=255)
   */
  private $bEditor;

  /**
   * @Column(type="string", length=255)
   */
  private $bPages;

  /**
   * @Column(type="string", length=255)
   */
  private $bIsbn;

  /**
   * @Column(type="string", length=255)
   */
  private $bIssn;

  /**
   * @Column(type="string", length=255)
   */
  private $bUrl;

  /**
   * @Column(type="string", length=255)
   */
  private $bKey;

  /**
   * @Column(type="string", length=255)
   */
  private $bNote;

  /**
   * @Column(type="string", length=255)
   */
  private $bWp;

  /**
   * This array defines in which order the fields are returned.
   * @var type 
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
    'wp'=>array('label'=>'Wikipedia', 'required'=>false)
  );
  
  public static $sourceTypes = array(
    'full'=>array('author', 'title', 'year', 'month', 'day', 'journal', 'volume', 'number', 'publisher', 'address', 'series', 'edition', 'booktitle', 'editor', 'pages', 'isbn', 'issn', 'url', 'key', 'note', 'wp'),
    'book'=>array('author', 'year', 'title', 'isbn', 'publisher', 'address', 'url', 'note'),
    'periodical'=>array('author', 'title', 'journal', 'volume', 'number', 'year', 'month', 'day', 'pages', 'publisher', 'issn', 'url', 'note'),
    'essay'=>array('author', 'title', 'year', 'volume', 'hrsg', 'pages', 'publisher', 'address', 'isbn', 'url', 'note')
  );

  public function __construct($data = array()){
    parent::__construct($data);
  }

  public function getDocument(){
    return $this->document;
  }

  public function setDocument(Application_Model_Document $document){
    $this->document = $document;
  }

  public function getSourceType(){
    return $this->sourceType;
  }

  public function setSourceType($sourceType){
    $this->sourceType = $sourceType;
  }

  /**
   *
   * @param string $fieldName
   * @return string
   * 
   * @todo Maybe magic __get and __set or even better classes for every bibtex type. 
   */
  public function getContent($fieldName){
    $fieldName = 'b' . ucfirst($fieldName);

    if(!empty($this->$fieldName)){
      return $this->$fieldName;
    }
    return '';
  }

  public function setContent($content, $fieldName){
    $fieldName = 'b' . ucfirst($fieldName);
    $this->$fieldName = $content;
  }

  public function getDirectLink(){
    
  }

  public function getDirectName(){
    
  }

}