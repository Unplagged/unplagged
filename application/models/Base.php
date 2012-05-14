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

use Doctrine\Common\Collections\ArrayCollection;

/**
 * The class represents a base class for any type of item that can receive 
 * comments or can be the source of a notification.
 * 
 * It defines also the structure of the database table for the ORM.
 *
 * @author Unplagged
 * 
 * @Entity 
 * @HasLifeCycleCallbacks
 * @Table(name="bases") 
 * @InheritanceType("JOINED") 
 * @DiscriminatorColumn(name="type", type="string") 
 * @DiscriminatorMap({ 
 *  "case" = "Application_Model_Case"
 * ,"comment" = "Application_Model_Comment"
 * ,"cron_task" = "Application_Model_Task"
 * ,"detection_report" = "Application_Model_Document_Page_DetectionReport"
 * ,"document" = "Application_Model_Document"
 * ,"document_fragment" = "Application_Model_Document_Fragment"
 * ,"document_fragment_partial" = "Application_Model_Document_Fragment_Partial"
 * ,"document_page" = "Application_Model_Document_Page"
 * ,"file" = "Application_Model_File"
 * ,"notification" = "Application_Model_Notification"
 * ,"simtext_report" = "Application_Model_Document_Page_SimtextReport"
 * ,"report" = "Application_Model_Report"
 * ,"tag" = "Application_Model_Tag"
 * ,"user" = "Application_Model_User"
 * ,"versionable_version" = "Application_Model_Versionable_Version"
 * ,"document_page_line" = "Application_Model_Document_Page_Line"
 * ,"rating" = "Application_Model_Rating"
 * })
 */
abstract class Application_Model_Base{

  const ICON_CLASS = '';
  
  /**
   * @Id @GeneratedValue @Column(type="integer") 
   */
  protected $id;

  /**
   * @var string The date and time when the object was created initially.
   * 
   * @Column(type="datetime")
   */
  protected $created;

  /** 
   * @var string The base element comments.
   * 
   * @OneToMany(targetEntity="Application_Model_Comment", mappedBy="source")
   * @JoinColumn(name="comment_id", referencedColumnName="id")
   */
  private $comments;
  
    /** 
   * @var string The base element ratings.
   * 
   * @OneToMany(targetEntity="Application_Model_Rating", mappedBy="source")
   * @JoinColumn(name="rating_id", referencedColumnName="id")
   */
  private $ratings;

  /**
   * @var ArrayCollection The notifications related to this object.
   * 
   * @OneToMany(targetEntity="Application_Model_Notification", mappedBy="source")
   * 
   * @todo private without getter and setter?
   */
  private $notifications;
  
  protected $conversationTypes = array('comment');

  public function __construct(){
    $this->comments = new ArrayCollection();
    $this->ratings = new ArrayCollection();
  }

  public function getId(){
    return $this->id;
  }

  /**
   * @todo do we really need setId? I thought this would always be handled by doctrine, which uses reflection
   */
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
   * Returns a class name for a direct link icon of this element. When no icon is used the return will be an
   * empty string.
   * 
   * An extending class is supposed to set the ICON_CLASS constant like this inside the classes scope:
   * 
   * <code>
   * const ICON_CLASS = 'my-icon-class';
   * </code>
   */
  public function getIconClass(){
    $childClass = get_called_class();
    
    if($childClass::ICON_CLASS !== null){
      return $childClass::ICON_CLASS; 
    }
  }

  public function getComments(){
    return $this->comments;
  }
  
  public function getRatings(){
    return $this->ratings;
  }

  public function getCreated(){
    return $this->created;
  }
  
  public function getConversationTypes(){
    return $this->conversationTypes;
  }
    
  public function isRatedByUser($user) {
    foreach($this->ratings as $rating) {
      if($rating->getUser()->getId() == $user->getId()) {
        return true;
      }
    }
    return false;
  }

}
