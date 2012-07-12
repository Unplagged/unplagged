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
class PermissionController extends Unplagged_Controller_Action {

    public function editRoleAction() {
        //get the role to edit
        $input = new Zend_Filter_Input(array('id' => 'Digits'), null, $this->_getAllParams());
        $role = $this->_em->getRepository('Application_Model_User_Role')->findOneById($input->id);

        $this->setTitle('Permissions for %s', array($role->getRoleId()));

        //find all permissions to display
        $pagePermissions = $this->_em->getRepository('Application_Model_PagePermission')->findByBase(null);
        $modelPermissions = $this->_em->getRepository('Application_Model_ModelPermission')->findByBase(null);

        //combine all permissions with the current state of the users allowed permissions
        $outputPermissions = array();
        $outputPermissions['pagePermissions'] = $this->initPermissionData($pagePermissions, $role, 'Application_Model_PagePermission');
        $outputPermissions['modelPermissions'] = $this->initPermissionData($modelPermissions, $role, 'Application_Model_ModelPermission');

        $editForm = new Application_Form_Permission_EditRole(array('permissions' => $outputPermissions));

        if ($this->_request->isPost()) {
            $success = $this->handleEditData($editForm, $role);

            if ($success) {
                $this->_helper->FlashMessenger(array('success' => 'The role was updated successfully.'));
            } else {
                $this->_helper->FlashMessenger(array('error' => 'An error occured while updating the role.'));
            }

            $this->_helper->redirector('list', 'permission');
        } else {
            $this->view->allPermissions = $outputPermissions;
            $this->view->editForm = $editForm;
        }
    }

    private function initPermissionData($permissions, $role, $instanceType) {
        $outputPermissions = array();

        foreach ($permissions as $possiblePermission) {
            if ($possiblePermission instanceof $instanceType) {
                $permissionName = $possiblePermission->getAction();
                $controllerName = $possiblePermission->getType();

                $outputPermissions[$controllerName][$permissionName] = array('allowed' => false, 'inherited' => false, 'id' => $possiblePermission->getId());
            }
        }

        //set all permissions that this role got as active and inherited
        foreach ($role->getPermissions(true) as $allowedPermission) {

            if ($allowedPermission instanceof $instanceType) {
                if ($allowedPermission->getAction() === '*' && $allowedPermission->getType() === 'global') {
                    foreach ($outputPermissions as $permissionGroupKey => $permissionGroup) {
                        foreach ($permissionGroup as $outputPermissionKey => $outputPermission) {
                            $outputPermissions[$permissionGroupKey][$outputPermissionKey]['allowed'] = true;
                        }
                    }
                } else {
                    $permissionGroupName = $allowedPermission->getType();

                    if (isset($outputPermissions[$permissionGroupName])) {
                        $permissionName = $allowedPermission->getAction();

                        if (isset($outputPermissions[$permissionGroupName][$permissionName])) {
                            $outputPermissions[$permissionGroupName][$permissionName]['allowed'] = true;
                            $outputPermissions[$permissionGroupName][$permissionName]['inherited'] = true;
                        }
                    }
                }
            }
        }

        //remove the inherited flag for every permission that this role got on it's own
        foreach ($role->getBasicPermissions(true, true) as $basicPermission) {
            if ($basicPermission instanceof $instanceType) {
                if ($basicPermission->getAction() === '*' && $basicPermission->getType() === 'global') {
                    foreach ($outputPermissions as $permissionGroupKey => $permissionGroup) {
                        foreach ($permissionGroup as $outputPermissionKey => $outputPermission) {
                            $outputPermissions[$permissionGroupKey][$outputPermissionKey]['inherited'] = false;
                        }
                    }
                } else {
                    $permissionGroupName = $basicPermission->getType();
                    if (isset($outputPermissions[$permissionGroupName])) {
                        $permissionName = $basicPermission->getAction();
                        if (isset($outputPermissions[$permissionGroupName][$permissionName])) {

                            $outputPermissions[$permissionGroupName][$permissionName]['inherited'] = false;
                        }
                    }
                }
            }
        }
        return $outputPermissions;
    }

    private function handleEditData(Application_Form_Permission_EditRole $editForm, Application_Model_User_Role $role) {
        $formData = $this->_request->getPost();
        if ($editForm->isValid($formData)) {

            foreach ($formData as $permissionGroup) {
                if (is_array($permissionGroup)) {
                    foreach ($permissionGroup as $permissionName => $value) {
                        $permissionId = substr($permissionName, strrpos($permissionName, '_') + 1);

                        $inherited = false;
                        if (substr($permissionId, -9) == 'inherited') {
                            $permissionId = substr($permissionId, -9);
                            $inherited = true;
                        }
                        $permission = $this->_em->getRepository('Application_Model_Permission')->findOneById($permissionId);

                        if ($permission) {
                            // skip active inherited roles
                            if ($inherited) {
                                continue;
                            }
                            if ($value == '1') {
                                $role->addPermission($permission);
                            } else {
                                $role->removePermission($permission);
                            }
                            $this->_em->persist($role);
                        }
                    }
                }
            }

            $this->_em->flush();

            return true;
        }

        return false;
    }

    public function listAction() {
        $caseRoles = $this->_em->getRepository('Application_Model_User_InheritableRole')->findByType('case-default');
        $userRoles = $this->_em->getRepository('Application_Model_User_Role')->findByType('user');
        $systemRoles = $this->_em->getRepository('Application_Model_User_Role')->findByType('global');
        $caseInheritedRoles = $this->_em->getRepository('Application_Model_User_Role')->findByType('case');

        $userRolePaginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($userRoles));
        $userRolePaginator->setItemCountPerPage(-1);

        $this->view->userRoles = $userRolePaginator;

        $caseRolePaginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($caseRoles));
        $caseRolePaginator->setItemCountPerPage(-1);

        $this->view->caseRoles = $caseRolePaginator;

        $caseInheritedRolePaginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($caseInheritedRoles));
        $caseInheritedRolePaginator->setItemCountPerPage(-1);

        $this->view->caseInheritedRoles = $caseInheritedRolePaginator;

        $systemRolePaginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($systemRoles));
        $systemRolePaginator->setItemCountPerPage(-1);

        $this->view->systemRoles = $systemRolePaginator;


        $this->setTitle('Roles');
    }

    /**
     * Selects 5 users based on matching first and lastname with the search string and sends their ids as json string back.
     * @param String from If defined it selects only users of a specific rank.
     */
    public function autocompleteAction() {
        $input = new Zend_Filter_Input(array('term' => 'Alnum', 'case' => 'Digits', 'skip' => 'StringTrim'), null, $this->_getAllParams());

        if (!empty($input->skip)) {
            $input->skip = ' AND r.id NOT IN (' . $input->skip . ')';
        }
        $caseCondition = '';
        if (!empty($input->case)) {
            $caseCondition = ' :caseId MEMBER OF u.cases AND';
        }

        // skip has to be passed in directly and can't be set as a parameter due to a doctrine bug
        $query = $this->_em->createQuery("SELECT r.id value, u.username label FROM Application_Model_User_Role r JOIN r.user u WHERE " . $caseCondition . " u.username LIKE :term" . $input->skip);
        $query->setParameter('term', '%' . $input->term . '%');
        if (!empty($input->case)) {
            $query->setParameter('caseId', $input->case);
        }
        $query->setMaxResults(5);

        $result = $query->getArrayResult();
        $this->_helper->json($result);
    }

    public function editAction() {
        $input = new Zend_Filter_Input(array('id' => 'Digits'), null, $this->_getAllParams());
        $base = $this->_em->getRepository('Application_Model_Base')->findOneById($input->id);

        if ($base) {
            $this->setTitle('Manage permissions');
            $this->view->subtitle = $base->getDirectName();

            $permissionActions = array('read', 'update', 'delete', 'authorize');

            $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type' => $base->getPermissionType(), 'action' => 'authorize', 'base' => $base));
            if (!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)) {
                $this->redirectToLastPage(true);
            }

            $modifyForm = new Application_Form_Permission_Modify();
            $modifyForm->setAction("/permission/edit/id/" . $input->id);

            $case = Zend_Registry::getInstance()->user->getCurrentCase();

            // get the permission directly on a specific base
            $permissions = array();
            // get users that have the global right due to their default role of the case
            $inheritedPermissions = array();
            if ($case) {
                foreach ($base->getPermissions('Application_Model_ModelPermission') as $permission) {
                    $permissions[$permission->getAction()] = $permission->getRoleIds();

                    foreach ($permissionActions as $permissionAction) {
                        foreach ($case->getCollaborators() as $collaborator) {
                            $accessAllPermission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type' => $base->getPermissionType(), 'action' => $permissionAction, 'base' => null));
                            if ($accessAllPermission) {
                                if ($collaborator->getRole()->hasPermission($accessAllPermission)) {
                                    if (!in_array($collaborator->getRole()->getId(), $permissions[$permission->getAction()])) {
                                        $permissions[$permissionAction][] = $collaborator->getRole()->getId();
                                    }
                                    $inheritedPermissions[$permissionAction][] = $collaborator->getRole()->getId();
                                }
                            }
                        }
                    }
                }
            }
            $value = array('inherited' => $inheritedPermissions, 'default' => $permissions);
            $modifyForm->getElement("permissions")->setValue($value);


            if ($this->_request->isPost()) {
                $result = $this->handleModifyData($modifyForm, $base);

                if ($result) {
                    // notification
                    $this->_helper->FlashMessenger(array('success' => 'The permissions were updated successfully.'));
                    $this->redirectToLastPage();
                }
            }

            $this->view->modifyForm = $modifyForm;
        } else {
            $this->_helper->FlashMessenger(array('error' => 'The specified element does not exist.'));
            $this->redirectToLastPage();
        }
    }

    private function handleModifyData(Application_Form_Permission_Modify $modifyForm, Application_Model_Base $base = null) {
        $formData = $this->_request->getPost();

        if ($modifyForm->isValid($formData)) {
            foreach ($base->getPermissions() as $permission) {
                if (isset($formData[$permission->getAction()])) {
                    $permission->setRoles($formData[$permission->getAction()]);
                } else {
                    $permission->setRoles(array());
                }
                $this->_em->persist($permission);
            }

            // write back to persistence manager and flush it
            $this->_em->persist($base);
            $this->_em->flush();

            return $base;
        }

        return false;
    }

}

?>
