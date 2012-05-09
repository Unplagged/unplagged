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
 * 
 * @author Unplagged
 */
class PermissionController extends Unplagged_Controller_Action{

  public function editGuestAction(){
    $guestRole = $this->_em->getRepository('Application_Model_User_GuestRole')->findOneByRoleId('guest'); 
    var_dump($guestRole->getPermissions());
  }

  
  public function editRoleAction(){
    $roleId = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());
  }
  
  public function listAction(){
    $inheritableRoles = $this->_em->getRepository('Application_Model_User_InheritableRole')->findAll();
    
    var_dump($inheritableRoles);
  }
}
?>
