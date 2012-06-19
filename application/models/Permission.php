<?php
/*
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
 * @Entity
 * @Table(name="permissions")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"page_permission" = "Application_Model_PagePermission", "model_permission" = "Application_Model_ModelPermission"})
 */
abstract class Application_Model_Permission implements Zend_Acl_Resource_Interface{
  
  /**
   * @Id
   * @GeneratedValue
   * @Column(type="integer") 
   */
  private $id;

  /**
   * @Column(type="string", length=255)
   */
  private $type;

  /**
   * @Column(type="string", length=255, nullable=true)
   */
  private $action = '';

  /**
   * @ManyToOne(targetEntity="Application_Model_Base")
   * @JoinColumn(name="base_id", referencedColumnName="id") 
   */
  private $base;

  /**
   * @var string The base element permissions.
   * 
   * @ManyToMany(targetEntity="Application_Model_User_Role", mappedBy="permissions", cascade={"persist"})
   */
  private $roles;

  public function __construct($type, $action, Application_Model_Base $base = null){
    $this->type = $type;
    $this->base = $base;
    $this->action = $action;
    $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
  }

  
  public function getId(){
    return $this->id;
  } 
  
  public function getBase() {
    return $this->base;
  }
  
  public function getAction(){
    return $this->action;
  }

  public function getType(){
    return $this->type;
  }

  public function getResourceId(){
    return $this->type . '_' . $this->action;
  }

  public function getRoleIds(){
    $roleIds = array();
    foreach($this->roles as $role){
      $roleIds[] = $role->getId();
    }
    return $roleIds;
  }

  public function addRole(Application_Model_User_Role $role){
    $role->addPermission($this);
    $this->roles->add($role);
  }

  public function removeRole(Application_Model_User_Role $role){
    $role->removePermission($this);
    $this->roles->removeElement($role);
  }

  public function setRoles($roleIds = array()){
    $removedRoles = array();

    // 1) search all roles that already exist by their id
    if(!empty($this->roles)){
      $this->roles->filter(function($role) use (&$roleIds, &$removedRoles){
            if(in_array($role->getId(), $roleIds)){
              $roleIds = array_diff($roleIds, array($role->getId()));
              return true;
            }
           // if($role->getUser())
            $removedRoles[] = $role;
            return false;
          });
    }

    // 2) add new roles that don't exist yet
    foreach($roleIds as $roleId){
      $role = Zend_Registry::getInstance()->entitymanager->getRepository('Application_Model_User_Role')->findOneById($roleId);
      $this->addRole($role);
    }

    // 3) remove roles that belonged to the permission before, but not anymore
    foreach($removedRoles as $role){
     // echo $role->getUser()->getUsername() . '...';
      $this->removeRole($role);
    }
  }

  public function clearRoles(){
    $this->roles->clear();
  }
}
?>
