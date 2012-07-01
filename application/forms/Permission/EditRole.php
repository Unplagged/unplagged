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
class Application_Form_Permission_EditRole extends Zend_Form{

  private $permissions;

  public function init(){
    $this->setMethod('post');
    foreach($this->permissions as $permissionKey=>$permissionType){
      foreach($permissionType as $groupLabel=>$permissionGroup){
        $elements = array();

        foreach($permissionGroup as $permissionName=>$permissionData){
          $checkboxElement = new Zend_Form_Element_Checkbox('permission_' . $permissionData['id'], array('belongsTo'=>$permissionKey . '_' .$groupLabel));
          $checkboxElement->setLabel($permissionName);
          $class = 'btn btn-checkbox';
          if($permissionData['allowed'] === true){
            $class .= ' active';
            $checkboxElement->setChecked(true);
          }
          if($permissionData['inherited'] === true){
            $class .= ' btn-primary';
          }
          $checkboxElement->setOptions(array('class'=>$class));
          $elements[] = $checkboxElement;
          $this->addElement($checkboxElement);
        }
        $this->addDisplayGroup($elements, $permissionKey . '_' .$groupLabel, array('legend'=>$groupLabel));
      }
    }
    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Save role');
    $submitElement->setOptions(array('class'=>'btn btn-primary'));

    $this->addElements(array($submitElement));
  }

  public function setPermissions($permissions){
    $this->permissions = $permissions;
  }

}
?>
