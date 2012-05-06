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
 * 
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <benjamin.oertel@me.com>
 * @version 1.0
 * 
 * @Entity 
 * @HasLifeCycleCallbacks
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
 * ,"notification" = "Application_Model_Notification"
 * ,"document_fragment" = "Application_Model_Document_Fragment"
 * ,"versionable_version" = "Application_Model_Versionable_Version"
 * ,"document_fragment_partial" = "Application_Model_Document_Fragment_Partial"
 * ,"simtext_report" = "Application_Model_Document_Page_SimtextReport"
 * ,"cron_task" = "Application_Model_Task"
 * ,"document_page_line" = "Application_Model_Document_Page_Line"
 * })
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
   * The date and time when the object was created initially.
   * 
   * @var string The inital persistence date and time.
   * 
   * @Column(type="datetime")
   */
  protected $created;
  
  /**
   * The base element comments.
   * 
   * @var string The base element comments.
   * 
   * @OneToMany(targetEntity="Application_Model_Comment", mappedBy="source")
   * @JoinColumn(name="comment_id", referencedColumnName="id")
   */
  private $comments;
  
   /**
   * The notifications related to this object.
   * 
   * @OneToMany(targetEntity="Application_Model_Notification", mappedBy="source")
   */
  private $notifications;
  
  public function __construct(){
    $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
  }
  
  public function getId() {
    return $this->id;
  }
  
  public function setId($id){
    $this->id = $id;
  }
    
  /**
   * Sets the creation time to the current time, if it is null.
   * This will normally be auto called the first time the object is persisted by doctrine.
   * 
   * @PrePersist
   */
  public function created(){
    if($this->created == null){
      $this->created = new DateTime("now");
    }
  }
  
  /**
   * Returns a direct link to an element by id. 
   */
  abstract public function getDirectLink();
  
  /**
   * Returns the title or name of the specific object of the element. 
   */
  abstract public function getDirectName();
  
  /**
   * Returns a class for a direct link icon of this element. 
   */
  abstract public function getIconClass();
  
  public function getComments(){
    return $this->comments;
  }
  
  public function getCreated(){
    return $this->created;
  }

}