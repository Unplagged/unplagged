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
 * The class represents a line of text in a document.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 * 
 * @Entity 
 * @Table(name="documents")
 * @HasLifeCycleCallbacks
 */
class Application_Model_Document{

  /**
   * The documentId is an unique identifier for each document.
   * @var string The documentId.
   * 
   * @Id @GeneratedValue @Column(type="integer")
   */
  protected $id;
  
  /**
   * The date when the document was created.
   * @var string The creation date.
   * 
   * @Column(type="datetime")
   */
  protected $created;
    /**
   * The title of the document.
   * @var string The title.
   * 
   * @Column(type="string", length=64)
   */
  protected $title;
  
  /**
   * @ManyToOne(targetEntity="Application_Model_File")
   * @JoinColumn(name="original_file_id", referencedColumnName="id", onDelete="SET NULL")
   */
  protected $originalFile;

  /**
   * The lines in the document.
   * 
   * @OneToMany(targetEntity="Application_Model_Document_Page", mappedBy="document")
   * @OrderBy({"pageNumber" = "ASC"})
   */
  protected $pages;

  public function __construct(array $data=null){
    if(isset($data["file"])){
      $this->originalFile = $data["file"];
    }

    if(isset($data["title"])){
      $this->title = $data["title"];
    }    $this->pages = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * Method auto-called when object is persisted to database for the first time.
   * 
   * @PrePersist
   */
  public function created(){
    $this->created = new DateTime("now");
  }

  public function getId(){
    return $this->id;
  }

  public function getOriginalData(){
    return 'originalData';
  }
  
  public function getTitle(){
    return $this->title;
  }
  
  public function addPage(Application_Model_Document_Page $page){
    $page->setDocument($this);
    $this->pages->add($page);
  }
  public function getPages(){
    return $this->pages;
  }
}

?> 