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

use Doctrine\Common\Collections\ArrayCollection;

/**
 * This class can be used to store a list of permissions to certain resources, which are identified by a simple string.
 * 
 * It is able to "inherit" an unlimited number of InheritableRoles, this means it has all permissions that are also set
 * for the inherited role. Please note that changes that are made to the inherited role will also change the permissions
 * that were made for this role.
 * 
 * @todo We could think about adding a blacklist of permissions later on, so that admin could refuse certain permissions
 * to the user even if an inherited role changes and allows it.´
 * 
 * @Entity
 * @table(name="roles")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"user_role" = "Application_Model_User_Role", "user_guest_role" = "Application_Model_User_GuestRole", "user_inheritable_role" = "Application_Model_User_InheritableRole"})
 */
class Application_Model_User_Role implements Zend_Acl_Role_Interface{

  const TYPE_GLOBAL = 'global';
  const TYPE_CASE = 'case';
  const TYPE_USER = 'user';
  const TYPE_CASE_DEFAULT = 'case-default';

  /**
   * @Id
   * @GeneratedValue
   * @Column(type="integer") 
   */
  protected $id;

  /**
   * @Column(type="string", nullable=true, unique=true)
   */
  protected $roleId;

  /**
   * A list of all the permissions that are allowed for an owner of this role.
   *
   * @ManyToMany(targetEntity="Application_Model_Permission")
   * @JoinTable(name="role_has_permission",
   *      joinColumns={@JoinColumn(name="role_id", referencedColumnName="id", onDelete="CASCADE")},
   *      inverseJoinColumns={@JoinColumn(name="permission_id", referencedColumnName="id", onDelete="CASCADE")}
   *      )
   */
  protected $permissions;

  /**
   * Stores the roles this role is extending.
   * 
   * @ManyToMany(targetEntity="Application_Model_User_Role")
   * @JoinTable(name="role_inherits",
   *      joinColumns={@JoinColumn(name="role_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="inherited_role_id", referencedColumnName="id")}
   *      )
   */
  protected $inheritedRoles;

  /**
   * @var string
   * 
   * @Column(type="string")
   */
  protected $type;

  /**
   * @OneToOne(targetEntity="Application_Model_User", mappedBy="role")
   */
  protected $user;

  public function __construct($type = null){
    $this->inheritedRoles = new ArrayCollection();
    $this->permissions = new ArrayCollection();
    if($type === null){
      $this->type = self::TYPE_USER;
    }else{
      $this->type = $type;
    }
  }

  public function getId(){
    return $this->id;
  }

  public function getRoleId(){
    if($this->roleId === null){
      $this->roleId = (string) $this->getId();
    }

    return $this->roleId;
  }

  public function setRoleId($roleId){
    $this->roleId = $roleId;
  }

  public function getType(){
    return $this->type;
  }

  public function getUser(){
    return $this->user;
  }

  public function setId($id){
    $this->id = $id;
  }

  public function setType($type){
    $this->type = $type;
  }
  
  
  
  
  public function getInheritedPermissions($global = false){
    $permissions = array();

    $inheritedRoles = $this->getInheritedRoles();

    if($inheritedRoles->count() > 0){
      foreach($inheritedRoles as $inheritedRole){
        $permissions = array_merge($inheritedRole->getPermissions($global), $permissions);
      }
    }

    return $permissions;
  }

  /**
   * Returns all permissions of this Role including the inherited ones.
   * 
   * @param bool $global Whether to check for the global permissions only (e.g. any page or any any case can be modified)
   * 
   * @return array All permissions the role does have
   */
  public function getPermissions($global = false){
    $inheritedPermissions = $this->getInheritedPermissions($global);

    $permissions = array();
    if(!$global){
      $permissions = array_merge($inheritedPermissions, $this->permissions->toArray());
    }else{
      // we need only permissions with base null when global is true
      $this->permissions->filter(function($permission) use (&$permissions){
            if(null == $permission->getBase()){
              $permissions[] = $permission;
              return true;
            }
            return false;
          });

      $permissions = array_merge($inheritedPermissions, $permissions);
    }
    
    return $permissions;
  }

  
  /**
   * Returns only the explicitly set permissions for the current object.
   * 
   * @param bool $asCollection Whether the permissions should be returned as collection of objects or array.
   * @param bool $global Whether to check for the global permissions only (e.g. any page or any any case can be modified)
   * 
   * @return array
   */
  public function getBasicPermissions($asCollection = false, $global = false){
    if($asCollection){
      if(!$global){
        return $this->permissions;
      }else{
        // we need only permissions with base null when global is true
        $permissions = array();
        $this->permissions->filter(function($permission) use (&$permissions){
              if(null == $permission->getBase()){
                $permissions[] = $permission;
                return true;
              }
              return false;
            });

        return $permissions;
      }
    }
    return $this->permissions->toArray();
  }
  
  public function hasInheritedPermission(Application_Model_Permission $permission) {
      foreach($this->inheritedRoles as $role) {
          if($role->hasPermission($permission)) {
              return true;
          }
      }
      
      return false;
  }
  
  
  public function hasPermission(Application_Model_Permission $permission){
    $user = Zend_Registry::getInstance()->user;
    $case = $user->getCurrentCase();

    // 1) check if the resource related to this element is already removed.
    if($permission->getBase() && $permission->getBase()->getState()->getName() == 'removed'){
      return false;
    }

    // 2) check the main user role on that specific permission
    if($this->getBasicPermissions(true, false)->contains($permission)){
      return true;
    }
    
    // 3) check the main user role on the 'any permission' (e.g. access to read all files
      if($permission->getBase()){
        $condition = array(
          'type'=>$permission->getType(),
          'action'=>$permission->getAction());

        $instanceType = ($permission instanceof Application_Model_PagePermission) ? 'Application_Model_PagePermission' : 'Application_Model_ModelPermission';
        
        $permissionAny = Zend_Registry::getInstance()->entitymanager->getRepository($instanceType)->findOneBy($condition);
      }else{
        $permissionAny = $permission;
      }

      // check the main user role on that right
      if($this->getBasicPermissions(true, false)->contains($permissionAny)){
        return true;
      }
    

    
    // when a case is selected, check if the user has a right in this case
    if($case){
      // check if the user has one of the case default roles as an inherited role and if this role has the permission
      foreach($case->getDefaultRoles() as $caseRole){
        if($this->getInheritedRoles()->contains($caseRole) && $caseRole->getBasicPermissions(true)->contains($permissionAny)){
          return true;
        }
      }
    }
    return false;
  }


  public function addPermission(Application_Model_Permission $permission){
    if(!$this->permissions->contains($permission)){
      $this->permissions->add($permission);
    }
  }

  public function removePermission(Application_Model_Permission $permission){
    /* echo $permission->getId() . '___' . $this->permissions->count() . '.....<br />';
      foreach($this->permissions as $perm) {
      echo $this->getId() . '...' . $perm->getId() . '__?????????<br/>';
      }exit;
     */
    if($this->permissions->contains($permission)){

      // $permission->getId() . '.....<br />';
      $this->permissions->removeElement($permission);
    }
  }

  public function addInheritedRole(Application_Model_User_InheritableRole $inheritedRole){
    if(!$this->inheritedRoles->contains($inheritedRole)){
      $this->inheritedRoles->add($inheritedRole);
    }
  }

  public function removeInheritedRole(Application_Model_User_InheritableRole $inheritedRole){
    $this->inheritedRoles->removeElement($inheritedRole);
  }

  public function getInheritedRoles(){
    return $this->inheritedRoles;
  }


}
