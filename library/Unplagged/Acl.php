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
 *
 */
class Unplagged_Acl extends Zend_Acl{

  public function __construct($user, $em){

    $this->addRole($user->getRole());

    $permissions = $user->getRole()->getPermissions();
    
    $resources = $em->getRepository('Application_Model_Permission')->findAll();

    foreach($resources as $resource){
      if(!$this->has($resource->getName())){
        $this->add($resource);
      }
    }

    foreach($permissions as $permission){
      $resource = $permission;
      if(!$this->has($resource)){
        $this->add($resource);
      }

      $this->allow($user->getRole(), $permission);
    }

    return $this;
  }

}
?>
