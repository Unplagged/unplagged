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
    //@todo findAll could be overkill here, as we currently only use those of type 'action'
    $allPermissions = $this->_em->getRepository('Application_Model_Permission')->findAll();

    $outputPermissions = array();

    foreach($allPermissions as $possiblePermission){
      if($possiblePermission->getType() === 'action'){
        $permissionName = $possiblePermission->getName();

        $controllerNameEnd = strpos($permissionName, '_');
        $controllerName = substr($permissionName, 0, $controllerNameEnd);

        $outputPermissions[$controllerName][substr($permissionName, $controllerNameEnd + 1)] = array('allowed'=>false, 'inherited'=>false);
      }
    }

    //set all permissions that this role got as active and inherited
    foreach($rolePermissions->getPermissions() as $allowedPermission){
      if($allowedPermission->getName() === '*'){
        foreach($outputPermissions as $permissionGroupKey=>$permissionGroup){
          foreach($permissionGroup as $outputPermissionKey=>$outputPermission){
            $outputPermissions[$permissionGroupKey][$outputPermissionKey]['allowed'] = true;
          }
        }
      }else{
        $permissionGroupNameEnd = strpos($allowedPermission->getName(), '_');
        $permissionGroupName = substr($allowedPermission->getName(), 0, $permissionGroupNameEnd);

        if(isset($outputPermissions[$permissionGroupName])){
          $permissionName = substr($allowedPermission->getName(), $permissionGroupNameEnd + 1);

          if(isset($outputPermissions[$permissionGroupName][$permissionName])){
            $outputPermissions[$permissionGroupName][$permissionName]['allowed'] = true;
            $outputPermissions[$permissionGroupName][$permissionName]['inherited'] = true;
          }
        }
      }
    }

    //remove the inherited flag for every permission that this role got on it's own
    foreach($rolePermissions->getBasicPermissions() as $basicPermissions){
      if($basicPermissions->getName() === '*'){
        foreach($outputPermissions as $permissionGroupKey=>$permissionGroup){
          foreach($permissionGroup as $outputPermissionKey=>$outputPermission){
            $outputPermissions[$permissionGroupKey][$outputPermissionKey]['inherited'] = false;
          }
        }
      }else{
        $permissionGroupNameEnd = strpos($basicPermissions->getName(), '_');
        $permissionGroupName = substr($basicPermissions->getName(), 0, $permissionGroupNameEnd);

        if(isset($outputPermissions[$permissionGroupName])){
          $permissionName = substr($basicPermissions->getName(), $permissionGroupNameEnd + 1);

          if(isset($outputPermissions[$permissionGroupName][$permissionName])){

            $outputPermissions[$permissionGroupName][$permissionName]['inherited'] = false;
          }
        }
      }
    }

    $editForm = new Application_Form_Permission_EditRole(array('permissions'=>$outputPermissions));

    if($this->_request->isPost()){
      $success = $this->handleEditData($editForm, $rolePermissions);

      if($success){
        $this->_helper->FlashMessenger(array('success'=>'The role was updated successfully.'));
      }else{
        $this->_helper->FlashMessenger(array('error'=>'An error occured while updating the role.'));
      }

      $this->_helper->redirector('list', 'permission');
    }else{
      $this->view->allPermissions = $outputPermissions;
      $this->view->editForm = $editForm;
    }
  }

  public function handleEditData(Application_Form_Permission_EditRole $editForm, Application_Model_User_Role $role){
    $formData = $this->_request->getPost();
    if($editForm->isValid($formData)){

      foreach($formData as $permissionGroup){
        if(is_array($permissionGroup)){
          foreach($permissionGroup as $permissionName=>$value){
            $permission = $this->_em->getRepository('Application_Model_Permission')->findOneByName($permissionName);
            if($permission){
              if($value === '1'){
                $role->addPermission($permission);
                //die('hier');
              }else{
                $role->removePermission($permission);
              }
            }
          }
        }
      }

      $this->_em->persist($role);
      $this->_em->flush();

      return true;
    }

    return false;
  }

  public function listAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());

    $inheritableRoles = $this->_em->getRepository('Application_Model_User_Role')->findAll();

    $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($inheritableRoles));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

    $this->view->paginator = $paginator;

    $this->setTitle('Roles');
    //var_dump($inheritableRoles);
    Zend_Layout::getMvcInstance()->sidebar = null;
  }

  /**
   * Selects 5 users based on matching first and lastname with the search string and sends their ids as json string back.
   * @param String from If defined it selects only users of a specific rank.
   */
  public function autocompleteAction(){
    $input = new Zend_Filter_Input(array('term'=>'Alnum', 'case'=>'Digits', 'skip'=>'StringTrim'), null, $this->_getAllParams());

    if(!empty($input->skip)){
      $input->skip = ' AND r.id NOT IN (' . $input->skip . ')';
    }
    $caseCondition = '';
    if(!empty($input->case)){
      $caseCondition = ' :caseId MEMBER OF u.cases AND';
    }

    // skip has to be passed in directly and can't be set as a parameter due to a doctrine bug
    $query = $this->_em->createQuery("SELECT r.id value, u.username label FROM Application_Model_User_Role r JOIN r.user u WHERE " . $caseCondition . " u.username LIKE :term" . $input->skip);
    $query->setParameter('term', '%' . $input->term . '%');
    if(!empty($input->case)){
      $query->setParameter('caseId', $input->case);
    }
    $query->setMaxResults(5);

    $result = $query->getArrayResult();
    $this->_helper->json($result);
  }

  public function editAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());
    $base = $this->_em->getRepository('Application_Model_Base')->findOneById($input->id);

    if($base){
      $this->setTitle('Manage permissions');
      $this->view->subtitle = $base->getDirectName();
        if(!Zend_Registry::getInstance()->user->hasPermission(new Application_Model_Permission($base->getPermissionType(), 'authorize', $input->id))){
          $this->redirectToLastPage(true);
        }

      $modifyForm = new Application_Form_Permission_Modify();
      $modifyForm->setAction("/permission/edit/id/" . $input->id);

      $permissions = array();
      foreach($base->getPermissions() as $permission){
        $permissions[$permission->getAction()] = $permission->getRoleIds();
      }
      $modifyForm->getElement("permissions")->setValue($permissions);


      if($this->_request->isPost()){
        $result = $this->handleModifyData($modifyForm, $base);

        if($result){
          // notification
          // $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
          // Unplagged_Helper::notify("case_updated", $result, $user);

          $this->_helper->FlashMessenger(array('success'=>'The permissions were updated successfully.'));
          $this->redirectToLastPage();
        }
      }

      $this->view->modifyForm = $modifyForm;
    }else{
      $this->_helper->FlashMessenger(array('error'=>'The specified element does not exist.'));
      $this->redirectToLastPage();
    }
  }

  private function handleModifyData(Application_Form_Permission_Modify $modifyForm, Application_Model_Base $base = null){
    $formData = $this->_request->getPost();

    if($modifyForm->isValid($formData)){
      foreach($base->getPermissions() as $permission){
        if(isset($formData[$permission->getAction()])){
          $permission->setRoles($formData[$permission->getAction()]);
          $this->_em->persist($permission);
        }
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
