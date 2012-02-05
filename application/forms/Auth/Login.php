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
 */
class Application_Form_Auth_Login extends Zend_Form{

  /**
   * Creates the login form.
   * 
   * @see Zend_Form::init()
   */
  public function init(){

    $this->setMethod('post');
    $this->setAction("/auth/login/");

    $usernameElement = new Zend_Form_Element_Text('username');
    $usernameElement->setLabel("Username");
    $usernameElement->addValidator('regex', false, array('/^[a-z0-9]/i'));
    $usernameElement->addValidator('stringLength', false, array(2, 64));
    $usernameElement->setAttrib('maxLength', 64);
    $usernameElement->setRequired(true);

    $passwordElement = new Zend_Form_Element_Password('password');
    $passwordElement->setLabel("Password");
    $passwordElement->addValidator('regex', false, array('/^[a-z0-9]/i'));
    $passwordElement->addValidator('stringLength', false, array(8, 32));
    $passwordElement->setAttrib('maxLength', 32);
    $passwordElement->setRequired(true);

    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Log in');
    $submitElement->setIgnore(true);
    $submitElement->setAttrib('class', 'submit');
    $submitElement->removeDecorator('DtDdWrapper');

    $this->addElements(array(
      $usernameElement
      , $passwordElement
    ));

    $this->addDisplayGroup(array(
      'username'
      , 'password'
        )
        , 'credentialGroup'
        , array('legend'=>'Credential Information')
    );

    $this->addElements(array(
      $submitElement
    ));
  }

}
?>
