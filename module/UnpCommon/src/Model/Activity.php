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
use \UnpCommon\Model\Base;
use \UnpCommon\Model\Feature\ArrayCreator;
use \UnpCommon\Model\Feature\Linkable;

/**
 * The class represents a log entry.
 *
 * @ORM\Entity(repositoryClass="\UnpCommon\Repository\ActivityRepository")
 * @ORM\Table(name="activity")
 */
class Activity extends Base implements Linkable, ArrayCreator{

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $message = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $actorMessage = '';

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $targetMessage = '';

  /**
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\User")
   * @ORM\JoinColumn(name="actor_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $actor;

  /**
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\Base")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $target;

  /**
   * The element this notification is related to.
   *
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\Base", inversedBy="notifications")
   * @ORM\JoinColumn(name="source_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $result;

  /**
   * The element the permission is checked on.
   *
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\Base", inversedBy="notifications")
   * @ORM\JoinColumn(name="permission_source_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $permissionSource;

  public function __construct($message, \UnpCommon\Model\User $actor, $actorMessage = '', $targetMessage = '',
          \UnpCommon\Model\Base $target = null, \UnpCommon\Model\Base $result = null){
    parent::__construct();

    $this->message = $message;
    $this->actorMessage = $actorMessage;
    $this->targetMessage = $targetMessage;
    $this->actor = $actor;
    $this->target = $target;
    $this->result = $result;
  }

  public function getActor(){
    return $this->actor;
  }

  public function getTarget(){
    return $this->target;
  }

  public function getResult(){
    return $this->result;
  }

  public function getMessage(){
    return $this->message;
  }

  public function getTargetMessage(){
    return $this->message;
  }

  public function getActorMessage(){
    return $this->message;
  }

  public function toArray(){
    $result = array(
        'id'=>$this->id,
    );

    return $result;
  }

  public function getDirectName(){
    return 'activity';
  }

  public function getDirectLink(){
    return '/notification/show/id/' . $this->id;
  }

  public function getIconClass(){
    return 'icon-notification';
  }

}