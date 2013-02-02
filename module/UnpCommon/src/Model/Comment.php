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

use \Doctrine\ORM\Mapping as ORM;
use \UnpCommon\Model\Base;
use \UnpCommon\Model\Feature\ArrayCreator;
use \UnpCommon\Model\Feature\Linkable;
use \UnpCommon\Model\SimpleEntity;
use \UnpCommon\Model\User;

/**
 * The class represents a single comment.
 * 
 * @ORM\Entity
 * @ORM\Table(name="comment")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"rating" = "Rating", "comment" = "Comment"})
 */
class Comment extends SimpleEntity implements Linkable, ArrayCreator{

  /**
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\User")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $author;

  /**
   * @var string The title of the comment.
   * @ORM\Column(type="string", nullable=true)
   */
  private $title = '';

  /**
   * @var string The text of the comment.
   * @ORM\Column(type="text")
   */
  private $text = '';

  /**
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\Base")
   * @ORM\JoinColumn(name="comment_target_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $commentTarget;

  /**
   * Constructor.
   */
  public function __construct(User $author = null, Base $commentTarget = null, $title = '', $text = ''){
    $this->author = $author;
    $this->commentTarget = $commentTarget;
    $this->title = $title;
    $this->text = $text;
  }

  /**
   * @return string
   */
  public function getTitle(){
    return $this->title;
  }

  /**
   * @return string
   */
  public function getText(){
    return $this->text;
  }

  /**
   * @return User
   */
  public function getAuthor(){
    return $this->author;
  }

  /**
   * @return Base
   */
  public function getCommentTarget(){
    return $this->commentTarget;
  }

  public function toArray(){
    $result = array(
        'id'=>$this->id,
        'text'=>$this->text,
        'title'=>$this->title,
        'author'=>$this->author instanceof ArrayCreator ? $this->author->toArray() : array(),
        'comment_target'=>$this->commentTarget instanceof ArrayCreator ? $this->commentTarget->toArray() : array(),
        'created'=>$this->created,
        'type'=>'comment',
    );
    return $result;
  }

  public function getDirectName(){
    $directName = '';
    if($this->commentTarget instanceof Linkable){
      $directName = $this->commentTarget->getDirectName();
    }
    return $directName;
  }

  public function getDirectLink(){
    $directLink = '';
    if($this->commentTarget instanceof Linkable){
      $directLink = $this->commentTarget->getDirectLink();
    }
    return $directLink;
  }

  public function getIconClass(){
    return 'fam-icon-comment';
  }

}