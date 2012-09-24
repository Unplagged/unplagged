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
 * The form class generates a form for recovering a user password.
 */
class Application_Form_User_Remove extends Zend_Form{

  /**
   * Creates the form to register a new user.
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');
    $this->setAction("/user/remove-account");

    $passwordElement = new Zend_Form_Element_Password('password');
    $passwordElement->setLabel("Password");
    $passwordElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $passwordElement->addValidator('stringLength', false, array(8, 32));
    $passwordElement->setAttrib('maxLength', 32);
    $passwordElement->setRequired(true);
    
    $confirmationElement = new Zend_Form_Element_Checkbox('confirmation');
    $confirmationElement->setLabel('Do you really want to delete your account?');
    $confirmationElement->setRequired(true);
    
    $reasonElement = new Zend_Form_Element_Textarea('reason');
    $reasonElement->setLabel('Reason');
    $reasonElement->setAttrib('maxLength', 500);

    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Remove account');
    $submitElement->setOptions(array('class'=>'btn btn-primary'));

    $this->addElements(array(
      $passwordElement
      ,$reasonElement
      ,$confirmationElement
    ));
    
    $this->addDisplayGroup(array(
        'reason'
        )
        , 'reasonGroup'
        , array('legend'=>'Reason Information')
    );
    
    
    $this->addDisplayGroup(array(
      'confirmation',
      'password'
        )
        , 'credentialGroup'
        , array('legend'=>'Approval Information')
    );

    $this->addElements(array(
      $submitElement
    ));
  }

}