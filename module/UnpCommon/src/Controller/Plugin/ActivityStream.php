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
namespace UnpCommon\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * @link http://peoplepods.net/readme/activity Loosely based on this blog post.
 */
class ActivityStream extends AbstractPlugin{

  private $entityManager = null;
  
  public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager){
    $this->entityManager = $entityManager;
  }
  
  /**
   * Publishes a new activity in the activity stream.
   * 
   * @param string $message The message as seen by a non involved user.
   * @param string $actorMessage A special message for the triggering user.
   * @param string $targetMessage A special message for the targeted user.
   * @param \UnpCommon\Model\User $actor The user that triggered this activity.
   * @param \UnpCommon\Model\User $target The user that is the target of this activity.
   * @param \UnpCommon\Model\Base $result The entity that resulted from this action.
   */
  public function publishActivity($message, \UnpCommon\Model\User $actor, $actorMessage = '', $targetMessage = '',
          \UnpCommon\Model\Base $target = null, \UnpCommon\Model\Base $result = null){
    $activity = new \UnpCommon\Model\Activity($message, $actor, $actorMessage, $targetMessage, $target, $result);

    $this->entityManager->persist($activity);
    $this->entityManager->flush();
  }

}