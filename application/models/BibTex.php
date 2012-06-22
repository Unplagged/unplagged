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
   * The current state of the report.
   * 
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
  private $shortcut;

  /**
   * @Column(type="string", length=255)
   */
  private $author;

  /**
   * @Column(type="string", length=255)
   */
  private $title;

  /**
   * @Column(type="string", length=255)
   */
  private $newspaper;

  /**
   * @Column(type="string", length=255)
   */
  private $collection;

  /**
   * @Column(type="string", length=255)
   */
  private $hrsg;

  /**
   * @Column(type="string", length=255)
   */
  private $people;

  /**
   * @Column(type="string", length=255)
   */
  private $city;

  /**
   * @Column(type="string", length=255)
   */
  private $publisher;

  /**
   * @Column(type="string", length=255)
   */
  private $edition;

  /**
   * @Column(type="string", length=255)
   */
  private $year;

  /**
   * @Column(type="string", length=255)
   */
  private $month;

  /**
   * @Column(type="string", length=255)
   */
  private $day;

  /**
   * @Column(type="string", length=255)
   */
  private $number;

  /**
   * @Column(type="string", length=255)
   */
  private $pages;

  /**
   * @Column(type="string", length=255)
   */
  private $scope;

  /**
   * @Column(type="string", length=255)
   */
  private $row;

  /**
   * @Column(type="string", length=255)
   */
  private $note;

  /**
   * @Column(type="string", length=255)
   */
  private $isbn;

  /**
   * @Column(type="string", length=255)
   */
  private $issn;

  /**
   * @Column(type="string", length=255)
   */
  private $doi;

  /**
   * @Column(type="string", length=255)
   */
  private $url;

  /**
   * @Column(type="string", length=255)
   */
  private $rnl;

  /**
   * @Column(type="string", length=255)
   */
  private $wp;

  /**
   * @Column(type="string", length=255)
   */
  private $inlit;

  /**
   * @Column(type="string", length=255)
   */
  private $infn;

  /**
   * @Column(type="string", length=255)
   */
  private $skey;

  /**
   * This array defines in which order the fields are returned.
   * @var type 
   */
  public static $accessibleFields = array(
    'shortcut'=>array('label'=>'Shortcut', 'required'=>false, 'types'=>array('full')),
    'author'=>array('label'=>'Author', 'required'=>false, 'types'=>array('full', 'book', 'periodikum', 'aufsatz')),
    'title'=>array('label'=>'Title', 'required'=>false, 'types'=>array('full', 'book', 'periodikum', 'aufsatz')),
    'newspaper'=>array('label'=>'Newspaper', 'required'=>false, 'types'=>array('full', 'periodikum')),
    'collection'=>array('label'=>'Collection', 'required'=>false, 'types'=>array('full', 'aufsatz')),
    'hrsg'=>array('label'=>'Hrsg', 'required'=>false, 'types'=>array('full', 'aufsatz')),
    'people'=>array('label'=>'People', 'required'=>false, 'types'=>array('full')),
    'city'=>array('label'=>'City', 'required'=>false, 'types'=>array('full', 'book', 'periodikum', 'aufsatz')),
    'publisher'=>array('label'=>'Publisher', 'required'=>false, 'types'=>array('full', 'book', 'periodikum', 'aufsatz')),
    'edition'=>array('label'=>'Edition', 'required'=>false, 'types'=>array('full')),
    'year'=>array('label'=>'Year', 'required'=>false, 'types'=>array('full', 'book', 'periodikum', 'aufsatz')),
    'month'=>array('label'=>'Month', 'required'=>false, 'types'=>array('full', 'periodikum')),
    'day'=>array('label'=>'Day', 'required'=>false, 'types'=>array('full', 'periodikum')),
    'number'=>array('label'=>'Number', 'required'=>false, 'types'=>array('full', 'periodikum')),
    'pages'=>array('label'=>'Pages', 'required'=>false, 'types'=>array('full', 'periodikum', 'aufsatz')),
    'scope'=>array('label'=>'Scope', 'required'=>false, 'types'=>array('full')),
    'row'=>array('label'=>'Row', 'required'=>false, 'types'=>array('full')),
    'note'=>array('label'=>'Note', 'required'=>false, 'types'=>array('full', 'book', 'periodikum', 'aufsatz')),
    'isbn'=>array('label'=>'ISBN', 'required'=>false, 'types'=>array('full', 'book', 'aufsatz')),
    'issn'=>array('label'=>'ISSN', 'required'=>false, 'types'=>array('full', 'aufsatz')),
    'doi'=>array('label'=>'Doi', 'required'=>false, 'types'=>array('full')),
    'url'=>array('label'=>'URL', 'required'=>false, 'types'=>array('full', 'book', 'periodikum', 'aufsatz')), // verify
    'rnl'=>array('label'=>'RNL', 'required'=>false, 'types'=>array('full', 'book', 'periodikum', 'aufsatz')), // verify
    'wp'=>array('label'=>'WP', 'required'=>false, 'types'=>array('full')),
    'inlit'=>array('label'=>'Inlit', 'required'=>false, 'types'=>array('full', 'book', 'periodikum', 'aufsatz')), //verify
    'skey'=>array('label'=>'Key', 'required'=>false, 'types'=>array('full')),
    'infn'=>array('label'=>'Infn', 'required'=>false, 'types'=>array('full', 'book', 'periodikum', 'aufsatz')) //verify
  );

  public function __construct(array $data = null){
    
  }

  public function getDocument(){
    return $this->document;
  }

  public function setDocument($document){
    $this->document = $document;
  }

  public function getSourceType(){
    return $this->sourceType;
  }

  public function setSourceType($sourceType){
    $this->sourceType = $sourceType;
  }

  public function getContent($fieldName){
    if($this->$fieldName){
      return $this->$fieldName;
    }
    return null;
  }

  public function setContent($content, $fieldName){
    $this->$fieldName = $content;
    echo $content . "<br />";
  }

  public function getDirectLink(){
    
  }

  public function getDirectName(){
    
  }

}