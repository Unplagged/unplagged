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
   *      joinColumns={@JoinColumn(name="role_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="permission_id", referencedColumnName="id")}
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
    $this->inheritedRoles = new \Doctrine\Common\Collections\ArrayCollection();
    $this->permissions = new \Doctrine\Common\Collections\ArrayCollection();
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

  public function getInheritedPermissions(){
    $permissions = array();

    $inheritedRoles = $this->getInheritedRoles();

    if($inheritedRoles->count() > 0){
      foreach($this->getInheritedRoles() as $inheritedRole){
        $permissions = array_merge($inheritedRole->getPermissions(), $permissions);
      }
    }

    return $permissions;
  }

  /**
   * Returns all permissions of this Role including the inherited.
   * 
   * @return array
   */
  public function getPermissions(){
    $inheritedPermissions = $this->getInheritedPermissions();
    $permissions = array_merge($inheritedPermissions, $this->permissions->toArray());
    return $permissions;
  }

  public function hasPermission(Application_Model_Permission $permission){
    $user = Zend_Registry::getInstance()->user;
    $case = $user->getCurrentCase();

    // check the main user role on that right
    if($this->getBasicPermissions(true)->contains($permission)){
      return true;
    }
    if($case){
      // when a role without a base was sent, we do not need to select it
      if($permission->getBase()){
        $condition = array(
          'type'=>$permission->getType(),
          'action'=>$permission->getAction(),
          'action'=>$permission->getAction());

        $permissionAny = Zend_Registry::getInstance()->entitymanager->getRepository('Application_Model_Permission')->findOneBy($condition);
      }else{
        $permissionAny = $permission;
      }
      foreach($case->getDefaultRoles() as $caseRole){
        if($this->getInheritedRoles()->contains($caseRole)){
          if($caseRole->getBasicPermissions(true)->contains($permissionAny)){
            return true;
          }
        }
      }
    }
    return false;
  }

  /**
   * Returns only the explicitly set permissions for the current object.
   * 
   * @return array
   */
  public function getBasicPermissions($asCollection = false){
    if($asCollection){
      return $this->permissions;
    }
    return $this->permissions->toArray();
  }

  public function addPermission(Application_Model_Permission $permission){
    if(!$this->permissions->contains($permission)){
      $this->permissions->add($permission);
    }
  }

  public function removePermission(Application_Model_Permission $permission){
    if($this->permissions->contains($permission)){
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

}