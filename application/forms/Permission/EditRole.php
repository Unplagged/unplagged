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
class Application_Form_Permission_EditRole extends Zend_Form {

    private $permissions;

    public function init() {
        $this->setMethod('post');

        $displayGroups = array();

        foreach ($this->permissions as $permissionType => $permissionTypePermission) {
            foreach ($permissionTypePermission as $permissionId => $permissionData) {
                $checkboxElement = new Zend_Form_Element_Checkbox('p' . $permissionId, array('belongsTo' => 'permission'));
                $checkboxElement->setLabel($permissionData['action']);
                $class = 'btn btn-checkbox';
                if ($permissionData['inherited'] === true) {
                    $class .= ' inherited';
                }
                if ($permissionData['allowed'] === true) {
                    $class .= ' active';
                    $checkboxElement->setChecked(true);
                }
                if ($permissionData['inherited'] === true) {
                    $class .= ' btn-primary disabled';
                    $checkboxElement->setAttrib('disabled', 'disabled');
                }
                $checkboxElement->setOptions(array('class' => $class));

                $displayGroups[$permissionType . '_' . $permissionData['type']]['elements'][] = $checkboxElement;
                $displayGroups[$permissionType . '_' . $permissionData['type']]['label'] = $permissionData['type'];

                $this->addElement($checkboxElement);
            }
        }

        foreach ($displayGroups as $groupId => $data) {
            $this->addDisplayGroup($data['elements'], $groupId, array('legend' => $data['label']));
        }

        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setLabel('Save role');
        $submitElement->setOptions(array('class' => 'btn btn-primary'));

        $this->addElements(array($submitElement));
    }

    public function setPermissions($permissions) {
        $this->permissions = $permissions;
    }

}

?>
