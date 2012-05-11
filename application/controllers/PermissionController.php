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
  
  public function editRoleAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());
    $rolePermissions = $this->_em->getRepository('Application_Model_User_Role')->findOneById($input->id);
    $this->setTitle('Permissions for ' . $rolePermissions->getRoleId());
    
    $allPermissions = $this->_em->getRepository('Application_Model_Permission')->findAll();
    
    $outputPermissions = array();
    
    foreach($allPermissions as $possiblePermission){
      if($possiblePermission->getType() === 'action'){
        $permissionName = $possiblePermission->getName();
        
        $controllerNameEnd = strpos($permissionName, '_');
        $controllerName = substr($permissionName,0, $controllerNameEnd);

        $outputPermissions[$controllerName][] = substr($permissionName, $controllerNameEnd+1);
      } 
    }
    
    foreach($rolePermissions->getPermissions() as $allowedPermissions){
        var_dump($allowedPermissions);
    }
    
    
    $editForm = new Application_Form_Permission_EditRole(array('permissions'=>$outputPermissions));
    $this->view->allPermissions = $outputPermissions;
    $this->view->editForm = $editForm;
  }
  
  public function listAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());

    $inheritableRoles = $this->_em->getRepository('Application_Model_User_InheritableRole')->findAll();

    $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($inheritableRoles));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);
    
    $this->view->paginator = $paginator;
    
    //var_dump($inheritableRoles);
    Zend_Layout::getMvcInstance()->sidebar = null;
  }
}
?>
