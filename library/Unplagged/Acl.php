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
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class Unplagged_Acl extends Zend_Acl{

  public function __construct($user, $em){
    
    $this->addRole($user->getRole());
    
    $permissions = $user->getRole()->getPermissions();
    
    $resources = array(
      'auth'  
    );
    
    $this->add(new Zend_Acl_Resource('auth'));
    $this->add(new Zend_Acl_Resource('auth_login'));
    $this->add(new Zend_Acl_Resource('logout'), 'auth');
    $this->add(new Zend_Acl_Resource('user'));
    $this->add(new Zend_Acl_Resource('user_recover-password'));
    $this->add(new Zend_Acl_Resource('register'), 'user');
    $this->add(new Zend_Acl_Resource('edit-profile'), 'user');
    $this->add(new Zend_Acl_Resource('error'));
    $this->add(new Zend_Acl_Resource('error_error'));
    $this->add(new Zend_Acl_Resource('index'));
    $this->add(new Zend_Acl_Resource('index_index'));
    $this->add(new Zend_Acl_Resource('document'));
    $this->add(new Zend_Acl_Resource('list'), 'document');
    $this->add(new Zend_Acl_Resource('simtext'), 'document');
    $this->add(new Zend_Acl_Resource('response-plagiarism'), 'document');
    $this->add(new Zend_Acl_Resource('files'));
    $this->add(new Zend_Acl_Resource('files_view_private'));
    $this->add(new Zend_Acl_Resource('file'));
    $this->add(new Zend_Acl_Resource('file_list'));
    $this->add(new Zend_Acl_Resource('case_view_files'));
    $this->add(new Zend_Acl_Resource('googlesearch'));
    $this->add(new Zend_Acl_Resource('case'));
    $this->add(new Zend_Acl_Resource('case_create'));
    $this->add(new Zend_Acl_Resource('case_list'));
    $this->add(new Zend_Acl_Resource('document_page'));
    $this->add(new Zend_Acl_Resource('document_fragment'));
    $this->add(new Zend_Acl_Resource('document_fragment_list'));
    $this->add(new Zend_Acl_Resource('image'));
    $this->add(new Zend_Acl_Resource('notification'));
    $this->add(new Zend_Acl_Resource('notification_recent-activity'));
    $this->add(new Zend_Acl_Resource('comment'));
    $this->add(new Zend_Acl_Resource('activity_stream_public'));

    foreach($permissions as $permission){
      $resource = new Zend_Acl_Resource($permission);
      if(!$this->has($resource)){
        $this->add($resource);
      }
      $this->allow($user->getRole(), $permission);  
    }
    
    $this->allow($user->getRole(), 'user_register');
    $this->allow($user->getRole(), 'user_recover-password');
    $this->allow($user->getRole(), 'notification_recent-activity');
    $this->allow($user->getRole(), 'document_fragment_list');
    $this->allow($user->getRole(), 'case_list');
    $this->allow($user->getRole(), 'case_create');
    $this->allow($user->getRole(), 'index_index');
    
    return $this;
  }
}
?>
