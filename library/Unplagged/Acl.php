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

  public function __construct(){
    
  }
  
  public static function constructBasic(){
    $instance = new self();
    
    $instance->addRole(new Application_Model_User_GuestRole());
    $instance->addRole(new Zend_Acl_Role('user'));
    $instance->addRole(new Zend_Acl_Role('admin'), 'user');
    
    $instance->add(new Zend_Acl_Resource('auth'));
    $instance->add(new Zend_Acl_Resource('login'), 'auth');
    $instance->add(new Zend_Acl_Resource('logout'), 'auth');
    $instance->add(new Zend_Acl_Resource('user'));
    $instance->add(new Zend_Acl_Resource('register'), 'user');
    $instance->add(new Zend_Acl_Resource('edit-profile'), 'user');
    $instance->add(new Zend_Acl_Resource('error'));
    $instance->add(new Zend_Acl_Resource('index'));
    $instance->add(new Zend_Acl_Resource('document'));
    $instance->add(new Zend_Acl_Resource('list'), 'document');
    $instance->add(new Zend_Acl_Resource('simtext'), 'document');
    $instance->add(new Zend_Acl_Resource('response-plagiarism'), 'document');
    $instance->add(new Zend_Acl_Resource('files'));
    $instance->add(new Zend_Acl_Resource('file'));
    $instance->add(new Zend_Acl_Resource('googlesearch'));
    $instance->add(new Zend_Acl_Resource('case'));
    $instance->add(new Zend_Acl_Resource('document_page'));
    $instance->add(new Zend_Acl_Resource('document_fragment'));
    $instance->add(new Zend_Acl_Resource('image'));
    $instance->add(new Zend_Acl_Resource('notification'));
    $instance->add(new Zend_Acl_Resource('comment'));

    $instance->allow('guest', 'index');
    $instance->allow('guest', 'googlesearch');
    $instance->allow('guest', 'error');
    $instance->allow('guest', 'user', 'register');
    $instance->allow('guest', 'user', 'verify');
    $instance->allow('guest', 'user', 'recover-password');
    $instance->allow('guest', 'user', 'reset-password');
    $instance->allow('guest', 'document', 'response-plagiarism');
    
    $instance->allow('user', null);
    
    return $instance;
  }
  
  public static function constructFromUser(Application_Model_User $user){
    $instance = new self();
    
    $instance->addRole($user->getRole());
    
    $permissions = $user->getRole()->getPermissions();
    
    $instance->add(new Zend_Acl_Resource('auth'));
    $instance->add(new Zend_Acl_Resource('login'), 'auth');
    $instance->add(new Zend_Acl_Resource('logout'), 'auth');
    $instance->add(new Zend_Acl_Resource('user'));
    $instance->add(new Zend_Acl_Resource('register'), 'user');
    $instance->add(new Zend_Acl_Resource('edit-profile'), 'user');
    $instance->add(new Zend_Acl_Resource('error'));
    $instance->add(new Zend_Acl_Resource('index'));
    $instance->add(new Zend_Acl_Resource('document'));
    $instance->add(new Zend_Acl_Resource('list'), 'document');
    $instance->add(new Zend_Acl_Resource('simtext'), 'document');
    $instance->add(new Zend_Acl_Resource('response-plagiarism'), 'document');
    $instance->add(new Zend_Acl_Resource('files'));
    $instance->add(new Zend_Acl_Resource('file'));
    $instance->add(new Zend_Acl_Resource('googlesearch'));
    $instance->add(new Zend_Acl_Resource('case'));
    $instance->add(new Zend_Acl_Resource('document_page'));
    $instance->add(new Zend_Acl_Resource('document_fragment'));
    $instance->add(new Zend_Acl_Resource('image'));
    $instance->add(new Zend_Acl_Resource('notification'));
    $instance->add(new Zend_Acl_Resource('comment'));
    $instance->add(new Zend_Acl_Resource('activity_stream_public'));

    foreach($permissions as $permission){
      $resource = new Zend_Acl_Resource($permission);
      if(!$instance->has($resource)){
        $instance->add($resource);
      }
      $instance->allow($user->getRole(), $permission);  
    }
    
    $instance->allow($user->getRole(), 'user', 'register');
    
    return $instance;
  }
}
?>
