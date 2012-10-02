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
 * ,"simtext_report" = "Application_Model_Simtext_Report"
 * ,"report" = "Application_Model_Report"
 * ,"tag" = "Application_Model_Tag"
 * ,"user" = "Application_Model_User"
 * ,"versionable_version" = "Application_Model_Versionable_Version"
 * ,"document_page_line" = "Application_Model_Document_Page_Line"
 * ,"rating" = "Application_Model_Rating"
 * ,"bibtex" = "Application_Model_BibTex"
 * })
 */
abstract class Application_Model_Base{

  const ICON_CLASS = '';
  const PERMISSION_TYPE = 'base';

  public static $permissionTypes = array(
    'read',
    'update',
    'delete',
    'authorize'
  );

  /**
   * @var array An array containing all classes that don't need permission management. 
   */
  public static $blacklist = array(
    'task',
    'document-fragment-type',
    'document-fragment-partial',
    'notification',
    'tag',
    'versionable',
    'document-page',
    'document-page-line',
    'rating',
    'versionable-version',
    'simtext-report',
    'document-page-detectionreport',
  );

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
   * @var string The base element comments.
   * 
   * @ManyToMany(targetEntity="Application_Model_Tag", cascade={"persist"})
   * @JoinTable(name="base_has_tag",
   *      joinColumns={@JoinColumn(name="base_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="tag_id", referencedColumnName="id")}
   *      )
   */
  private $tags;

  /**
   * @var string The base element ratings.
   * 
   * @OneToMany(targetEntity="Application_Model_Rating", mappedBy="source")
   * @JoinColumn(name="rating_id", referencedColumnName="id")
   */
  private $ratings;

  /**
   * @var string The base element permissions.
   * 
   * @OneToMany(targetEntity="Application_Model_ModelPermission", mappedBy="base")
   * @JoinColumn(name="permission_id", referencedColumnName="id")
   */
  private $permissions;

  /**
   * @var ArrayCollection The notifications related to this object.
   * 
   * @OneToMany(targetEntity="Application_Model_Notification", mappedBy="source")
   * 
   * @todo private without getter and setter? Could be a doctrine thing
   */
  private $notifications;
  protected $conversationTypes = array('comment');

  /**
   * @ManyToOne(targetEntity="Application_Model_State")
   * @JoinColumn(name="state_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $state;
  
  private $entityManager;

  public function __construct(array $data = array()){
    $this->entityManager = Zend_Registry::getInstance()->entitymanager;
    
    if(isset($data['state'])){
      $this->state = $data['state'];
    }else{
      //@todo dependency injection for entitymanager
      $this->state = $this->entityManager->getRepository('Application_Model_State')->findOneByName('created');
    }

    $this->comments = new ArrayCollection();
    $this->ratings = new ArrayCollection();
    $this->tags = new ArrayCollection();
    $this->permissions = new ArrayCollection();
  }

  public function getId(){
    return $this->id;
  }

  /**
   * Sets the creation time to the current time, if it is null.
   * This will be auto called the first time the object is persisted by doctrine.
   * 
   * @PrePersist
   */
  public function created(){
    if($this->created == null){
      $this->created = new DateTime('now');
    }
  }

  /**
   * Creates the permission objects. so that users can gain access to this particular object.
   * 
   * @PrePersist 
   */
  public function storePermissions(){
    $em = $this->entityManager;

    $user = null;
    if($this->getPermissionType() === 'user'){
      $user = $this;
    }else{
      $user = Zend_Registry::getInstance()->user;
    }

    foreach(self::$permissionTypes as $permissionType){
      if(!in_array($this->getPermissionType(), self::$blacklist)){
        $permission = new Application_Model_ModelPermission($this->getPermissionType(), $permissionType, $this);

        $this->addPermission($permission);
        $user->getRole()->addPermission($permission);
        $em->persist($permission);
      }
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

  public function getPermissionType(){
    $childClass = get_called_class();

    return strtolower(str_replace('_', '-', substr($childClass, strlen('Application_Model_'))));
  }

  public function getComments(){
    return $this->comments;
  }

  public function getRatings(){
    return $this->ratings;
  }

  public function addRating(Application_Model_Rating $rating){
    $this->ratings->add($rating);
  }

  public function countRatingsByRating($ratingRating){
    $count = 0;
    $this->ratings->filter(function($rating) use (&$count, &$ratingRating){
          if($rating->getRating() == $ratingRating){
            $count++;
            return true;
          }
          return false;
        });

    return $count;
  }

  public function getTags(){
    return $this->tags;
  }

  public function getCreated(){
    return $this->created;
  }

  public function getConversationTypes(){
    return $this->conversationTypes;
  }

  public function isRatedByUser($user){
    foreach($this->ratings as $rating){
      if($rating->getUser()->getId() == $user->getId()){
        return true;
      }
    }
    return false;
  }

  public function getTagIds(){
    $tagIds = array();
    foreach($this->tags as $tag){
      $tagIds[] = $tag->getId();
    }
    return $tagIds;
  }

  public function addTag(Application_Model_Tag $tag){
    $this->tags->add($tag);
  }

  public function removeTag(Application_Model_Tag $tag){
    $this->tags->removeElement($tag);
  }

  public function setTags($tagIds = array()){
    $removedTags = array();

    // 1) search all tags that already exist by their id
    if(!empty($this->tags)){
      $this->tags->filter(function($tag) use (&$tagIds, &$removedTags){
            if(in_array($tag->getId(), $tagIds)){
              $tagIds = array_diff($tagIds, array($tag->getId()));
              return true;
            }
            $removedTags[] = $tag;
            return false;
          });
    }

    // 2) create new tags for those that don't exist yet
    foreach($tagIds as $tagId){
      if(is_numeric($tagId)){
        $tag = $this->entityManager->getRepository('Application_Model_Tag')->findOneById($tagId);
      }else{
        $data['title'] = $tagId;
        $tag = new Application_Model_Tag($data);
      }

      $this->addTag($tag);
    }

    // 3) remove tags that belonged to the element before, but not anymore
    foreach($removedTags as $tag){
      $this->removeTag($tag);
    }
  }

  public function clearTags(){
    $this->tags->clear();
  }

  public function getPermissions($instanceType = null){
    if(empty($instanceType)){
      return $this->permissions;
    }else{
      $permissions = array();
      $this->permissions->filter(function($permission) use (&$permissions, &$instanceType){
            if($permission instanceof $instanceType){
              $permissions[] = $permission;
              return true;
            }
            return false;
          });
          return $permissions;
    }
  }

  public function remove(){
    $this->state = $this->entityManager->getRepository('Application_Model_State')->findOneByname('deleted');
  }

  private function addPermission($permission){
    if($this->permissions && !$this->permissions->contains($permission)){
      $this->permissions->add($permission);
    }
  }

  public function getState(){
    return $this->state;
  }

  public function setState(Application_Model_State $state){
    $this->state = $state;
  }

}