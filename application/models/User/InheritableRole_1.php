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

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * This class has nearly the same functionality as Application_Model_User_Role, it
 * just removes the ability to inherit roles in order to avoid infinite loops while
 * looking up the permissions.
 * 
 * This essentially means that there can be only one level of role inheritance, which
 * is achieved through the fact that Application_Model_User_Role only allows objects of
 * this type to be inherited. 
 * 
 * @Entity
 */
class Application_Model_User_InheritableRole extends Application_Model_User_Role{

  public function getInheritedRoles(){
    return new ArrayCollection();  
  }
  
  /**
   * This function does nothing and shouldn't be called. It's just here out of necessity to stop multiple inheritance.
   * 
   * @param Application_Model_User_InheritableRole $inheritedRole 
   */
  public function addInheritedRole(Application_Model_User_InheritableRole $inheritedRole){
    //do nothing here, to avoid multi level inheritance 
  }
}