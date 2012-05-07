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
class Application_Form_User_Password_Reset extends Zend_Form{

    /**
   * The hash of the user thats password shall be reset.
   * @var string The user's verification hash.
   */
  private $hash;

  /**
   * Initializes the form.
   * @param integer $hash The users hash, to reset the password for.
   */
  public function __construct($hash){
    $this->hash = $hash;
    parent::__construct();
  }
  
  /**
   * Creates the form to register a new user.
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');
    $this->setAction("/user/recover-password/hash/" . $this->hash);

    $passwordElement = new Zend_Form_Element_Password('password');
    $passwordElement->setLabel("Password");
    $passwordElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $passwordElement->addValidator('stringLength', false, array(8, 32));
    $passwordElement->setAttrib('maxLength', 32);
    $passwordElement->setRequired(true);

    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Reset password');
    $submitElement->setOptions(array('class'=>'btn btn-primary'));

    $this->addElements(array(
      $passwordElement
    ));

    $this->addDisplayGroup(array(
      'password'
        )
        , 'credentialGroup'
        , array('legend'=>'Credential Information')
    );

    $this->addElements(array(
      $submitElement
    ));
  }

}
