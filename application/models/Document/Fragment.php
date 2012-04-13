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
class Application_Model_Document_Fragment extends Application_Model_Base{

  /**
   * The title of the document.
   * @var string The title.
   * 
   * @Column(type="string", length=64)
   */
  private $title;

  /**
   * The starting position in the document.
   *
   * @OneToOne(targetEntity="Application_Model_Document_Page_Position")
   * @JoinColumn(name="page_position_start_id", referencedColumnName="id")
   */
  private $posStart;

  /**
   * The ending position in the document.
   *  
   * @OneToOne(targetEntity="Application_Model_Document_Page_Position")
   * @JoinColumn(name="page_position_end_id", referencedColumnName="id")
   */
  private $posEnd;

  public function __construct(array $data = null){
  }

  public function getDirectName(){
    return "document_fragment";
  }

  public function getDirectLink(){
    return "/document-fragment/show/id/" . $this->id;
  }

  public function getIconClass(){
    return "document-icon";
  }
  
  public function getTitle(){
    return $this->title;
  }

  public function getPosStart(){
    return $this->posStart;
  }

  public function getPosEnd(){
    return $this->posEnd;
  }



}