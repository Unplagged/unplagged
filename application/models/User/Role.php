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
  protected $id;

  /**
   * @Column(type="string", nullable=true, unique=true)
   */
  protected $roleId;

  /**
   * A list of all the permissions that are allowed for an owner of this role.
   * @Column(type="array")
   */
  protected $permissions;

  /**
   * Stores the roles this role is extending.
   * 
   * @ManyToMany(targetEntity="Application_Model_User_InheritableRole")
   * @JoinTable(name="role_inherits",
   *      joinColumns={@JoinColumn(name="role_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="inherited_role_id", referencedColumnName="id")}
   *      )
   */
  protected $inheritedRoles;

  public function __construct(){
    $this->inheritedRoles = new \Doctrine\Common\Collections\ArrayCollection();

    //allow everything for now, since we don't have a mechanism to edit the permissions
    $defaultPermissions = array(
      "controller_auth_index",
      "controller_auth_login",
      "controller_auth_logout",
      "controller_case_index",
      "controller_case_create",
      "controller_case_edit",
      "controller_case_list",
      "controller_case_autocomplete-alias",
      "controller_case_files",
      "controller_case_add-file",
      "controller_comment_index",
      "controller_comment_create",
      "controller_comment_list",
      "controller_document_fragment_index",
      "controller_document_fragment_show",
      "controller_document_fragment_create",
      "controller_document_fragment_edit",
      "controller_document_fragment_list",
      "controller_document_fragment_diff",
      "controller_document_fragment_delete",
      "controller_document_page_index",
      "controller_document_page_list",
      "controller_document_page_detection-reports",
      "controller_document_page_show",
      "controller_document_page_de-hyphen",
      "controller_document_page_edit",
      "controller_document_page_delete",
      "controller_document_page_stopwords",
      "controller_document_page_simtext-reports",
      "controller_document_page_simtext",
      "controller_document_index",
      "controller_document_edit",
      "controller_document_list",
      "controller_document_delete",
      "controller_document_detect-plagiarism",
      "controller_document_response-plagiarism",
      "controller_error_error",
      "controller_file_index",
      "controller_file_upload",
      "controller_file_list",
      "controller_file_download",
      "controller_file_set-target",
      "controller_file_unset-target",
      "controller_file_parse",
      "controller_file_delete",
      "controller_image_index",
      "controller_image_show",
      "controller_index_index",
      "controller_notification_index",
      "controller_notification_recent-activity",
      "controller_notification_list",
      "controller_notification_comments",
      "controller_simtext_index",
      "controller_simtext_compare",
      "controller_simtext_download-report",
      "controller_simtext_ajax",
      "controller_tag_index",
      "controller_tag_autocomplete-titles",
      "controller_user_index",
      "controller_user_register",
      "controller_user_files",
      "controller_user_add-file",
      "controller_user_verify",
      "controller_user_recover-password",
      "controller_user_edit",
      "controller_user_set-current-case",
      "controller_user_autocomplete-names",
      "controller_user_remove-account"
    );

    $this->permissions = $defaultPermissions;
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

  /**
   *
   * @return array
   */
  public function getPermissions(){
    $permissions = array();
    
    $inheritedRoles = $this->getInheritedRoles();
    
    if(count($inheritedRoles)>0){
      foreach($this->getInheritedRoles() as $inheritedRole){
        $permissions = array_merge ($inheritedRole->getPermissions(), $permissions); 
      }
    }
    $permissions = array_merge($permissions, $this->permissions);
    
    return $permissions;
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
