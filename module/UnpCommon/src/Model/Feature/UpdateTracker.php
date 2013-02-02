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
namespace UnpCommon\Model\Feature;

/**
 * An implementing class is able to keep track of the time of it's last update.
 * 
 * If this is used with Doctrine, you need to declare @ORM\HasLifecycleCallbacks
 * in the class doc comment and @ORM\PrePersist like you can see below in the 
 * created method.
 */
interface UpdateTracker{
  
  /**
   * Changes the time of the last update to the current time.
   * 
   * @ORM\PrePersist
   */
  public function updated();
  
  /**
   * @return DateTime The time when the last update was performed.
   */
  public function getUpdated();
}