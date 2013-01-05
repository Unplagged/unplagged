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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;

/**
 * The class represents a base class for any type of item that can receive 
 * comments or can be the source of a notification.
 * 
 * @ORM\Entity 
 * @ORM\HasLifeCycleCallbacks
 * @ORM\Table(name="bases") 
 * @ORM\InheritanceType("JOINED") 
 * @ORM\DiscriminatorColumn(name="type", type="string") 
 * @ORM\DiscriminatorMap({ 
 *  "case" = "\UnpCommon\Model\PlagCase"
 * ,"comment" = "\UnpCommon\Model\Comment"
 * ,"cron_task" = "\UnpCommon\Model\Task"
 * ,"detection_report" = "\UnpCommon\Model\Document_Page_DetectionReport"
 * ,"document" = "\UnpCommon\Model\Document"
 * ,"document_fragment" = "\UnpCommon\Model\Document_Fragment"
 * ,"document_fragment_partial" = "\UnpCommon\Model\Document_Fragment_Partial"
 * ,"document_page" = "\UnpCommon\Model\Document_Page"
 * ,"file" = "\UnpCommon\Model\File"
 * ,"notification" = "\UnpCommon\Model\Notification"
 * ,"simtext_report" = "\UnpCommon\Model\Simtext_Report"
 * ,"report" = "\UnpCommon\Model\Report"
 * ,"tag" = "\UnpCommon\Model\Tag"
 * ,"user" = "\UnpCommon\Model\User"
 * ,"versionable_version" = "\UnpCommon\Model\Versionable_Version"
 * ,"document_page_line" = "\UnpCommon\Model\Document_Page_Line"
 * ,"rating" = "\UnpCommon\Model\Rating"
 * ,"bibtex" = "\UnpCommon\Model\BibTex"
 * })
 */
abstract class Base implements Linkable{

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
   * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") 
   */
  protected $id;

  /**
   * @var string The date and time when the object was created initially.
   * 
   * @ORM\Column(type="datetime")
   */
  protected $created;

  /**
   * @var string The base element comments.
   * 
   * @ORM\OneToMany(targetEntity="\UnpCommon\Model\Comment", mappedBy="source")
   * @ORM\JoinColumn(name="comment_id", referencedColumnName="id")
   */
  private $comments;

  /**
   * @var string The base element comments.
   * 
   * @ORM\ManyToMany(targetEntity="\UnpCommon\Model\Tag", cascade={"persist"})
   * @ORM\JoinTable(name="base_has_tag",
   *      joinColumns={@JoinColumn(name="base_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="tag_id", referencedColumnName="id")}
   *      )
   */
  private $tags;

  /**
   * @var string The base element ratings.
   * 
   * @ORM\OneToMany(targetEntity="\UnpCommon\Model\Rating", mappedBy="source")
   * @ORM\JoinColumn(name="rating_id", referencedColumnName="id")
   */
  private $ratings;

  /**
   * @var string The base element permissions.
   * 
   * @ORM\OneToMany(targetEntity="\UnpCommon\Model\ModelPermission", mappedBy="base")
   * @ORM\JoinColumn(name="permission_id", referencedColumnName="id")
   */
  private $permissions;

  /**
   * @var ArrayCollection The notifications related to this object.
   * 
   * @ORM\OneToMany(targetEntity="\UnpCommon\Model\Notification", mappedBy="source")
   * 
   * @todo private without getter and setter? Could be a doctrine thing
   */
  private $notifications;
  protected $conversationTypes = array('comment');

  /**
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\State")
   * @ORM\JoinColumn(name="state_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $state;

  public function __construct() {
    $this->comments = new ArrayCollection();
    $this->ratings = new ArrayCollection();
    $this->tags = new ArrayCollection();
    $this->permissions = new ArrayCollection();
  }
  
  public function getId() {
    return $this->id;
  }

  /**
   * Sets the creation time to the current time, if it is null.
   * This will be auto called the first time the object is persisted by doctrine.
   * 
   * @ORM\PrePersist
   */
  public function created() {
    if ($this->created == null) {
      $this->created = new DateTime('now');
    }
  }

  /**
   * Creates the permission objects. so that users can gain access to this particular object.
   * 
   * @ORM\PrePersist 
   */
  public function storePermissions() {
    /*$em = $this->entityManager;

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
    }*/
  }



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
  public function getIconClass() {
    $iconClass = '';
    $childClass = get_called_class();

    if ($childClass::ICON_CLASS !== null) {
      $iconClass = $childClass::ICON_CLASS;
    }

    return $iconClass;
  }
/*
  public function getPermissionType() {
    $childClass = get_called_class();

    return strtolower(str_replace('_', '-', substr($childClass, strlen('\UnpCommon\Model\'))));
  }

  public function getComments() {
    return $this->comments;
  }

  public function getRatings() {
    return $this->ratings;
  }

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
  public function getTags() {
    return $this->tags;
  }

  public function getCreated() {
    return $this->created;
  }
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

  public function getTagIds() {
    $tagIds = array();
    foreach ($this->tags as $tag) {
      $tagIds[] = $tag->getId();
    }
    return $tagIds;
  }

  public function addTag(Application\Model\Tag $tag) {
    $this->tags->add($tag);
  }

  public function removeTag(Application\Model\Tag $tag) {
    $this->tags->removeElement($tag);
  }

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

  public function clearTags() {
    $this->tags->clear();
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
  private function addPermission($permission) {
    if ($this->permissions && !$this->permissions->contains($permission)) {
      $this->permissions->add($permission);
    }
  }

  public function getState() {
    return $this->state;
  }

  public function setState(Application\Model\State $state) {
    $this->state = $state;
  }

}