<?php

/**
 * File for class {@link Acl}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class Unplagged_Acl extends Zend_Acl{

  public function __construct(){
    $this->addRole(new Zend_Acl_Role('guest'));
    $this->addRole(new Zend_Acl_Role('user'));
    $this->addRole(new Zend_Acl_Role('admin'), 'user');
    
    $this->add(new Zend_Acl_Resource('auth'));
    $this->add(new Zend_Acl_Resource('login'), 'auth');
    $this->add(new Zend_Acl_Resource('logout'), 'auth');
    $this->add(new Zend_Acl_Resource('user'));
    $this->add(new Zend_Acl_Resource('register'), 'user');
    $this->add(new Zend_Acl_Resource('edit-profile'), 'user');
    $this->add(new Zend_Acl_Resource('error'));
    $this->add(new Zend_Acl_Resource('index'));
    $this->add(new Zend_Acl_Resource('document'));
    $this->add(new Zend_Acl_Resource('list'), 'document');
    $this->add(new Zend_Acl_Resource('simtext'), 'document');
    $this->add(new Zend_Acl_Resource('files'));
    $this->add(new Zend_Acl_Resource('file'));
    $this->add(new Zend_Acl_Resource('googlesearch'));
    $this->add(new Zend_Acl_Resource('case'));
    $this->add(new Zend_Acl_Resource('document_page'));
    $this->add(new Zend_Acl_Resource('image'));
    //$this->add(new Zend_Acl_Resource('edit-profile'));
    
    
    //$this->add(new Zend_Acl_Resource('user'));
    //$this->add(new Zend_Acl_Resource('register'), 'user');


    $this->allow(null, 'index');
    $this->allow(null, 'login');
    $this->allow(null, 'error');
    $this->allow(null, 'googlesearch');
    $this->allow(null, 'user');
    //removed because no matching file exists currently, maybe not commited?
    //$this->deny(null, 'edit-profile');
    
    $this->allow('user', null);
  }

}
?>
