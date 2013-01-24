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

use Doctrine\ORM\Mapping as ORM;
use UnpCommon\Model\Base;
use UnpCommon\Model\Feature\ArrayCreator;
use UnpCommon\Model\Feature\CreatedTracker;
use UnpCommon\Model\Feature\Linkable;
use UnpCommon\Model\User;

/**
 * The class represents a single comment.
 * 
 * @ORM\Entity
 * @ORM\HasLifeCycleCallbacks
 * @ORM\Table(name="comment")
 */
class Comment implements Linkable, ArrayCreator, CreatedTracker{

  /**
   * @var int The notification action id.
   * @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @var string The date and time when the object was created initially.
   * @ORM\Column(type="datetime")
   */
  protected $created;

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
   * @ORM\Column(type="string", length=255)
   */
  private $text = '';

  /**
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\Base")
   * @ORM\JoinColumn(name="source_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $source;

  /**
   * Constructor.
   */
  public function __construct(User $author = null, Base $source = null, $title = '', $text = ''){
    $this->author = $author;
    $this->source = $source;
    $this->title = $title;
    $this->text = $text;
  }

  /**
   * @return int
   */
  public function getId(){
    return $this->id;
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
   * @return \UnpCommon\Model\User
   */
  public function getAuthor(){
    return $this->author;
  }

  /**
   * @return \UnpCommon\Model\Base
   */
  public function getSource(){
    return $this->source;
  }

  public function toArray(){
    $result = array(
        'id'=>$this->id,
        'text'=>$this->text,
        'title'=>$this->title,
        'author'=>$this->author instanceof ArrayCreator ? $this->author->toArray() : array(),
        'source'=>$this->source instanceof ArrayCreator ? $this->source->toArray() : array(),
        'created'=>$this->created,
        'type'=>'comment',
    );
    return $result;
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

  public function getDirectName(){
    return $this->source->getDirectName();
  }

  public function getDirectLink(){
    return $this->source->getDirectLink();
  }

  public function getIconClass(){
    return 'icon-comment';
  }

}