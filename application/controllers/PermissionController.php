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

    /**
     * Selects a list of all permissions and flags the ones the user has.
     * 
     * @param type $permissions
     * @param type $role
     * @param type $instanceType Whether it is a pagePermission or a modelPermission
     * @return boolean
     */
    private function initPermissionData($allPermissions, $role, $instanceType) {
        $outputPermissions = array();

        foreach ($allPermissions as $permission) {
            if ($permission instanceof $instanceType) {
                // the global flag can be set for user and global roles only, not allowed on case level
                if(!in_array($role->getType(), array('user', 'global')) && $permission->getType() == 'global' && $permission->getAction() == '*') {
                    continue;
                }
                
                $outputPermissions[$permission->getId()] = array(
                    'allowed' => false
                    , 'inherited' => false
                    , 'type' => $permission->getType()
                    , 'action' => $permission->getAction());
            }
        }

        // set all permissions that this role got as active and inherited
        foreach ($role->getInheritedPermissions() as $permission) {
            if (isset($outputPermissions[$permission->getId()])) {
                $outputPermissions[$permission->getId()]['inherited'] = true;
                $outputPermissions[$permission->getId()]['allowed'] = true;
            }
        }

        // set the inherited flag to false on the permissions the user has as basic ones
        foreach ($role->getBasicPermissions() as $permission) {
            if (isset($outputPermissions[$permission->getId()])) {
                $outputPermissions[$permission->getId()]['inherited'] = false;
                $outputPermissions[$permission->getId()]['allowed'] = true;
            }
        }

        return $outputPermissions;
    }

    /**
     * Saves the previousely selected permissions to the user role.
     * 
     * @param Application_Form_Permission_EditRole $editForm
     * @param Application_Model_User_Role $role
     * @return boolean
     */
    private function handleEditData(Application_Form_Permission_EditRole $editForm, Application_Model_User_Role $role) {
        $formData = $this->_request->getPost();

        if ($editForm->isValid($formData)) {
            foreach ($formData['permission'] as $permissionId => $value) {
                $permissionId = substr($permissionId, 1);

                $permission = $this->_em->getRepository('Application_Model_Permission')->findOneById($permissionId);
                if ($role->hasInheritedPermission($permission)) {
                    continue;
                } else {
                    if ($value == '1') {
                        $role->addPermission($permission);
                    } else {
                        $role->removePermission($permission);
                    }
                }
            }

            $this->_em->persist($role);
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
