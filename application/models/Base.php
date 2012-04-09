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
 * The class represents a base class for any type of item that can receive 
 * comments or can be the source of a notification.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <benjamin.oertel@me.com>
 * @version 1.0
 * 
 * @Entity 
 * @Table(name="bases") 
 * @InheritanceType("JOINED") 
 * @DiscriminatorColumn(name="type", type="string") 
 * @DiscriminatorMap({ 
 *  "case" = "Application_Model_Case"
 * ,"file" = "Application_Model_File"
 * ,"user" = "Application_Model_User"
 * ,"document" = "Application_Model_Document"
 * ,"document_page" = "Application_Model_Document_Page"
 * ,"detection_report" = "Application_Model_Document_Page_DetectionReport"
 * ,"comment" = "Application_Model_Comment"
 * ,"tag" = "Application_Model_Tag"
 * ,"notification" = "Application_Model_Notification"})
 * 
 */
abstract class Application_Model_Base{

  /**
   * @Id
   * @GeneratedValue
   * @Column(type="integer") 
   */
  protected $id;

  /**
   * The base element comments.
   * @var string The base element comments.
   * 
   * @OneToMany(targetEntity="Application_Model_Comment", mappedBy="source")
   * @JoinColumn(name="comment_id", referencedColumnName="id")
   */
  private $comments;
  
  
  public function __construct(){
    $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
  }
  
  public function getId() {
    return $this->id;
  }
  
  /**
   * Returns a direct link to an element by id. 
   */
  abstract public function getDirectLink();
  
    /**
   * Returns a direct name of the type of element. 
   */
  abstract public function getDirectName();
  
  /**
   * Returns a class for a direct link icon of this element. 
   */
  abstract public function getIconClass();
  
  public function getComments(){
    return $this->comments;
  }

}

?>
