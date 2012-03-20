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
 * The form class generates a form for editing a user profile and upgrading rights.
 *
 * @author Unplagged
 */
class Application_Form_User_Profile extends Zend_Form{

  /**
   * The id of the user thats data is loaded to the form.
   * @var integer A user id, data of that user is loaded to the form.
   */
  private $userId;

  /**
   * Initializes the form.
   * @param integer $userId The id of an user whos data is loaded to the form.
   */
  public function __construct($userId){
    $this->userId = $userId;
    parent::__construct();
  }

  /**
   * Creates the form to edit a user profile.
   * @see Zend_Form::init()
   */
  public function init(){
    $em = Zend_Registry::getInstance()->entitymanager;
    $defaultNamespace = new Zend_Session_Namespace('Default');

    $user = $em->getRepository('Application_Model_User')->findOneById($this->userId);
    $this->setMethod('post');
    $this->setAction("/user/edit/id/" . $this->userId);
    
    $usernameElement = new Zend_Form_Element_Text('username');
    $usernameElement->setLabel("Username");
    $usernameElement->setValue($user->getUsername());
    $usernameElement->setIgnore(true);

    $emailElement = new Zend_Form_Element_Text('email');
    $emailElement->setLabel("E-Mail");
    $emailElement->setValue($user->getEmail());
    $emailElement->setIgnore(true);

    $firstnameElement = new Zend_Form_Element_Text('firstname');
    $firstnameElement->setLabel("Firstname");
    $firstnameElement->setValue($user->getFirstname());
    $firstnameElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $firstnameElement->addValidator('stringLength', false, array(2, 64));
    $firstnameElement->setAttrib('maxLength', 64);
    $firstnameElement->setRequired(true);

    $lastnameElement = new Zend_Form_Element_Text('lastname');
    $lastnameElement->setLabel("Lastname");
    $lastnameElement->setValue($user->getLastname());
    $lastnameElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $lastnameElement->addValidator('stringLength', false, array(2, 64));
    $lastnameElement->setAttrib('maxLength', 64);
    $lastnameElement->setRequired(true);

    $avatarfilepath = new Zend_Form_Element_File('avatarfilepath');
    $avatarfilepath->setLabel('Avatar-Dateipfad:');
    $avatarfilepath->setRequired(true);
    
    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Save');
    $submitElement->setIgnore(true);
    $submitElement->setAttrib('class', 'submit');
    $submitElement->removeDecorator('DtDdWrapper');
   

    $this->addElements(array(
      $emailElement
      , $usernameElement
      , $firstnameElement
      , $lastnameElement
    ));

    $this->addElement($avatarfilepath);
    
    $this->addDisplayGroup(array(
      'email'
      , 'username'
      , 'firstname'
      , 'lastname'
      , 'avatarfilepath'
        )
        , 'personalGoup'
        , array('legend'=>'Personal Information')
    );

    $this->addElements(array(
      $submitElement
    ));
  }

}
