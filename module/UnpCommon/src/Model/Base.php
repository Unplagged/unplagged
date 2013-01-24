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

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use UnpCommon\Model\Feature\Commentable;
use UnpCommon\Model\Feature\CreatedTracker;
use UnpCommon\Model\State;
use UnpCommon\Model\Tag;

/**
 * The class represents a base class for any type of item that can receive 
 * comments or can be the source of a notification.
 * 
 * @ORM\Entity 
 * @ORM\HasLifeCycleCallbacks
 * @ORM\Table(name="base") 
 * @ORM\InheritanceType("JOINED") 
 * @ORM\DiscriminatorColumn(name="type", type="string") 
 * @ORM\DiscriminatorMap({ 
 *   "bibliographic_information" = "\UnpCommon\Model\BibliographicInformation",
 *   "case" = "\UnpCommon\Model\PlagiarismCase",
 *   "document" = "\UnpCommon\Model\Document",
 *   "file" = "\UnpCommon\Model\File",
 *   "activity" = "\UnpCommon\Model\Activity",
 *   "report" = "\UnpCommon\Model\Report",
 *   "tag" = "\UnpCommon\Model\Tag",
 *   "task" = "\UnpCommon\Model\Task",
 *   "user" = "\UnpCommon\Model\User",
 * })
 * ,"comment" = "\UnpCommon\Model\Comment"
 * ,"detection_report" = "\UnpCommon\Model\Document_Page_DetectionReport"
 * ,"document_fragment" = "\UnpCommon\Model\Document_Fragment"
 * ,"document_fragment_partial" = "\UnpCommon\Model\Document_Fragment_Partial"
 * ,"document_page" = "\UnpCommon\Model\Document_Page"
 * ,"simtext_report" = "\UnpCommon\Model\Simtext_Report"
 * ,"versionable_version" = "\UnpCommon\Model\Versionable_Version"
 * ,"document_page_line" = "\UnpCommon\Model\Document_Page_Line"
 * ,"rating" = "\UnpCommon\Model\Rating"
 */
abstract class Base implements CreatedTracker, Commentable{

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
   * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") 
   */
  protected $id;

  /**
   * @var string The date and time when the object was created initially.
   * @ORM\Column(type="datetime")
   */
  protected $created;

  /**
   * @var string The base element comments.
   * 
   * ORM\OneToMany(targetEntity="\UnpCommon\Model\Comment", mappedBy="source")
   * ORM\JoinColumn(name="comment_id", referencedColumnName="id")
   */
  private $comments;

  /**
   * @var string The base element comments.
   * 
   * ORM\ManyToMany(targetEntity="\UnpCommon\Model\Tag", cascade={"persist"})
   * ORM\JoinTable(name="base_has_tag",
   *      joinColumns={@ORM\JoinColumn(name="base_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
   *      )
   */
  private $tags;

  /**
   * @var string The base element ratings.
   * 
   * ORM\OneToMany(targetEntity="\UnpCommon\Model\Rating", mappedBy="source")
   * ORM\JoinColumn(name="rating_id", referencedColumnName="id")
   */
  private $ratings;

  /**
   * @var string The base element permissions.
   * 
   * ORM\OneToMany(targetEntity="\UnpCommon\Model\ModelPermission", mappedBy="base")
   * ORM\JoinColumn(name="permission_id", referencedColumnName="id")
   */
  private $permissions;

  /**
   * @var ArrayCollection The notifications related to this object.
   * 
   * ORM\OneToMany(targetEntity="\UnpCommon\Model\Notification", mappedBy="source")
   * 
   * @todo private without getter and setter? Could be a doctrine thing
   */
  protected $notifications;
  protected $conversationTypes = array('comment');

  /**
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\State")
   * @ORM\JoinColumn(name="state_id", referencedColumnName="id", onDelete="CASCADE")
   */
  protected $state;

  public function __construct(){
    $this->comments = new ArrayCollection();
    $this->ratings = new ArrayCollection();
    $this->tags = new ArrayCollection();
    $this->permissions = new ArrayCollection();
  }

  /**
   * @return int
   */
  public function getId(){
    return $this->id;
  }


  /**
   * Creates the permission objects. so that users can gain access to this particular object.
   * 
   * @ORM\PrePersist 
   */
  public function storePermissions(){
    /* $em = $this->entityManager;

      $user = null;
      if ($this->getPermissionType() === 'user') {
      $user = $this;
      } else {
      $user = Zend_Registry::getInstance()->user;
      }

      foreach (self::$permissionTypes as $permissionType) {
      if (!in_array($this->getPermissionType(), self::$blacklist)) {
      $permission = new \UnpCommon\Model\ModelPermission($this->getPermissionType(), $permissionType, $this);

      $this->addPermission($permission);
      $user->getRole()->addPermission($permission);
      $em->persist($permission);
      }
      } */
  }

  /*
    public function getPermissionType() {
    $childClass = get_called_class();

    return strtolower(str_replace('_', '-', substr($childClass, strlen('\UnpCommon\Model\'))));
    }
   */

  public function getComments(){
    return $this->comments->toArray();
  }

  public function getRatings(){
    return $this->ratings;
  }

  /*
    public function addRating(\Application\Model\Rating $rating) {
    $this->ratings->add($rating);
    }

    public function countRatingsByRating($ratingRating) {
    $count = 0;
    $this->ratings->filter(function($rating) use (&$count, &$ratingRating) {
    if ($rating->getRating() == $ratingRating) {
    $count++;
    return true;
    }
    return false;
    });

    return $count;
    }
   */
  /*
    public function getConversationTypes() {
    return $this->conversationTypes;
    }

    public function isRatedByUser($user) {
    foreach ($this->ratings as $rating) {
    if ($rating->getUser()->getId() == $user->getId()) {
    return true;
    }
    }
    return false;
    }

   */

  public function getTagIds(){
    $tagIds = array();
    foreach($this->tags as $tag){
      $tagIds[] = $tag->getId();
    }
    return $tagIds;
  }

  /**
   * @return ArrayCollection
   */
  public function getTags(){
    return $this->tags;
  }

  /**
   * @param Tag $tag
   */
  public function addTag(Tag $tag){
    $this->tags->add($tag);
  }

  /**
   * @param Tag $tag
   */
  public function removeTag(Tag $tag){
    $this->tags->removeElement($tag);
  }

  /*
    public function setTags($tagIds = array()) {
    $removedTags = array();

    // 1) search all tags that already exist by their id
    if (!empty($this->tags)) {
    $this->tags->filter(function($tag) use (&$tagIds, &$removedTags) {
    if (in_array($tag->getId(), $tagIds)) {
    $tagIds = array_diff($tagIds, array($tag->getId()));
    return true;
    }
    $removedTags[] = $tag;
    return false;
    });
    }

    // 2) create new tags for those that don't exist yet
    foreach ($tagIds as $tagId) {
    if (is_numeric($tagId)) {
    $tag = $this->entityManager->getRepository('Application\Model\Tag')->findOneById($tagId);
    } else {
    $data['title'] = $tagId;
    $tag = new \UnpCommon\Model\Tag($data);
    }

    $this->addTag($tag);
    }

    // 3) remove tags that belonged to the element before, but not anymore
    foreach ($removedTags as $tag) {
    $this->removeTag($tag);
    }
    }

    public function getPermissions($instanceType = null) {
    if (empty($instanceType)) {
    return $this->permissions;
    } else {
    $permissions = array();
    $this->permissions->filter(function($permission) use (&$permissions, &$instanceType) {
    if ($permission instanceof $instanceType) {
    $permissions[] = $permission;
    return true;
    }
    return false;
    });
    return $permissions;
    }
    }

    public function remove() {
    $this->state = $this->entityManager->getRepository('\UnpCommon\Model\State')->findOneByname('deleted');
    }
   */

  private function addPermission($permission){
    if($this->permissions && !$this->permissions->contains($permission)){
      $this->permissions->add($permission);
    }
  }

  /**
   * @return State
   */
  public function getState(){
    return $this->state;
  }

  /**
   * @param State $state
   */
  public function setState(State $state){
    $this->state = $state;
  }
  
  /**
   * @ORM\PrePersist
   */
  public function created(){
    if($this->created == null){
      $this->created = new DateTime('now');
    }
  }

  public function getCreated(){
    return $this->created;
  }

}