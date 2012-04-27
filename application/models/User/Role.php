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
      "auth_index",
      "auth_login",
      "auth_logout",
      "case_index",
      "case_create",
      "case_edit",
      "case_list",
      "case_autocomplete-alias",
      "case_files",
      "case_add-file",
      "comment_index",
      "comment_create",
      "comment_list",
      "document_fragment_index",
      "document_fragment_show",
      "document_fragment_create",
      "document_fragment_edit",
      "document_fragment_list",
      "document_fragment_diff",
      "document_fragment_delete",
      "document_page_index",
      "document_page_list",
      "document_page_detection-reports",
      "document_page_show",
      "document_page_de-hyphen",
      "document_page_edit",
      "document_page_delete",
      "document_page_stopwords",
      "document_page_simtext-reports",
      "document_page_simtext",
      "document_index",
      "document_edit",
      "document_list",
      "document_delete",
      "document_detect-plagiarism",
      "document_response-plagiarism",
      "error_error",
      "file_index",
      "file_upload",
      "file_list",
      "file_download",
      "file_set-target",
      "file_unset-target",
      "file_parse",
      "file_delete",
      "image_index",
      "image_show",
      "index_index",
      "notification_index",
      "notification_recent-activity",
      "notification_list",
      "notification_comments",
      "simtext_index",
      "simtext_compare",
      "simtext_download-report",
      "simtext_ajax",
      "tag_index",
      "tag_autocomplete-titles",
      "user_index",
      "user_register",
      "user_files",
      "user_add-file",
      "user_verify",
      "user_recover-password",
      "user_edit",
      "user_set-current-case",
      "user_autocomplete-names",
      "user_remove-account"
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
