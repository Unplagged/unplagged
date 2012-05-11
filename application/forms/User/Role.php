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
 * The form class generates a form for registering a new user.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 */
class Application_Form_User_Role extends Zend_Form{

  /**
   * Creates the form to register a new user.
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');
    $this->setAction("/user/register/");

    $emailElement = new Zend_Form_Element_Text('email');
    $emailElement->setLabel("E-Mail");
    $emailElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $emailElement->addValidator('stringLength', false, array(2, 64));
    $emailElement->addValidator('EmailAddress', true);
    $emailElement->addValidator(new Unplagged_Validate_NoRecordExists('Application_Model_User', 'email'));
    $emailElement->setAttrib('maxLength', 64);
    $emailElement->setRequired(true);

    $cb1Element = new Zend_Form_Element_Checkbox('cb1');
    $cb1Element->setLabel("edit");
    $cb1Element->setChecked(true);
    $cb1Element->setOptions(array('class'=>'btn btn-checkbox'));
    
    $cb2Element = new Zend_Form_Element_Checkbox('cb2');
    $cb2Element->setLabel("create");
    $cb2Element->setOptions(array('class'=>'btn btn-primary inherited btn-checkbox'));

    $cb3Element = new Zend_Form_Element_Checkbox('cb3');
    $cb3Element->setLabel("upload");
    $cb3Element->setOptions(array('class'=>'btn btn-primary inherited btn-checkbox'));
    
   
    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Register');
    $submitElement->setOptions(array('class'=>'btn btn-primary'));

    $this->addElements(array(
      $cb1Element
      , $cb2Element
      , $cb3Element
    ));

    $this->addDisplayGroup(array(
      'cb1'
      , 'cb2'
      , 'cb3'
        )
        , 'credentialGroup'
        , array('legend'=>'Credential Information')
    );

    $this->addElements(array(
      $submitElement
    ));
  }

}
