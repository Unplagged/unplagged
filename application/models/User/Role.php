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
 * This class can be used to store a list of permissions to certain resources.
 * 
 * @author Unplagged
 */
class Application_Model_User_Role implements Zend_Acl_Role_Interface{
  
  /**
   * @Id
   * @GeneratedValue
   * @Column(type="integer") 
   */
  protected $id;
  
  /**
   * A list of all the permissions that were allowed customly for this role.
   * 
   * @var type 
   * 
   * @ManyToMany(targetEntity
   */
  private $permissions;
  
  /**
   * Stores the roles this role is extending.
   * 
   * @ManyToMany(targetEntity="Application_Model_User_Role")
   * @JoinTable(name="roles_inherited",
   *      joinColumns={@JoinColumn(name="role_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="", referencedColumnName="id")}
   *      )
   */
  private $inheritedRoles;

  public function __construct(){
    $this->inheritedRoles = new \Doctrine\Common\Collections\ArrayCollection();
  }
  
  public function getId() {
    return $this->id;
  }
  
  public function getRoleId(){
    return $this->getId();
  }
  
  public function getPermissions(){
    return $this->permissions;  
  }
  
  public function addInheritedRole(Unplagged_Model_User_InheritableRole $inheritedRole){
    $this->inheritedRoles->add($inheritedRole);
  }
  
  public function getInheritedRoles(){
    return $this->inheritedRoles;  
  }
}
?>
