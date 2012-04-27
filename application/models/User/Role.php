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
 * to the user even if an inherited role changes and allows it.
 * 
 * @author Unplagged
 * 
 * @Entity
 * @table(name="roles")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"user_role" = "Application_Model_User_Role", "user_guest_role" = "Application_Model_User_GuestRole", "user_inheritable_role" = "Application_Model_User_InheritableRole"})
 */
class Application_Model_User_Role implements Zend_Acl_Role_Interface{
  
  /**
   * @Id
   * @GeneratedValue
   * @Column(type="integer") 
   */
  private $id;
  
  private $roleId = 'user';
  
  /**
   * A list of all the permissions that are allowed for an owner of this role.
   * @Column(type="array")
   */
  private $permissions;
  
  /**
   * Stores the roles this role is extending.
   * 
   * @ManyToMany(targetEntity="Application_Model_User_InheritableRole")
   * @JoinTable(name="role_inherits",
   *      joinColumns={@JoinColumn(name="role_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="inherited_role_id", referencedColumnName="id")}
   *      )
   */
  private $inheritedRoles;

  public function __construct(){
    $this->inheritedRoles = new \Doctrine\Common\Collections\ArrayCollection();
    
    $defaultPermissions = array(
      'index',
      'error',
      'case',
      'activity_stream_public',
      'files_view_public',
      'user_register',
      'auth',
      'auth_login',
      'auth_logout',
      'user',
      'user_register',
      'document',
      'document_list',
      'document_simtext',
      'document_response-plagiarism',
      'files',
      'file',
      'googlesearch',
      'document-page',
      'document-fragment',
      'image',
      'notification',
      'comment',
      'activity_stream_public',
      'files_view_private'
      );
    
    $this->permissions = $defaultPermissions;
  }
  
  public function getId() {
    return $this->id;
  }
  
  public function getRoleId(){
    if($this->roleId === null){
      return $this->getId()->toString();
    } else {
      return $this->roleId;  
    }  
  }
  
  public function getPermissions(){
    return $this->permissions;  
  }
  
  public function addPermission($permission){
    $this->permissions[] = $permission; 
  }
  
  public function addInheritedRole(Unplagged_Model_User_InheritableRole $inheritedRole){
    $this->inheritedRoles->add($inheritedRole);
  }
  
  public function getInheritedRoles(){
    return $this->inheritedRoles;  
  }
}
?>
