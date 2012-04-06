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
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 */
class Application_Form_User_Password_Recover extends Zend_Form{

  /**
   * Creates the form to register a new user.
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');
    $this->setAction("/user/recover-password/");

    $emailElement = new Zend_Form_Element_Text('email');
    $emailElement->setLabel("E-Mail");
    $emailElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $emailElement->addValidator('stringLength', false, array(2, 64));
    $emailElement->addValidator('EmailAddress', true);
    $emailElement->addValidator(new Unplagged_Validate_RecordExists('Application_Model_User', 'email'));
    $emailElement->setAttrib('maxLength', 64);
    $emailElement->setRequired(true);

    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Recover password');
    $submitElement->setIgnore(true);
    $submitElement->setAttrib('class', 'submit');
    $submitElement->removeDecorator('DtDdWrapper');

    $this->addElements(array(
      $emailElement
    ));

    $this->addDisplayGroup(array(
      'email'
        )
        , 'credentialGroup'
        , array('legend'=>'Credential Information')
    );

    $this->addElements(array(
      $submitElement
    ));
  }

}
