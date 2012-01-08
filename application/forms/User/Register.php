<?php

/*
 * Form for user registration.
 */

/**
 * The form class generates a form for registering a new user.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 */
class Application_Form_User_Register extends Zend_Form{

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
    $emailElement->addValidator(new Unplagged_Validate_NoRecordExists('Application_Model_User','email'));
    $emailElement->setAttrib('maxLength', 64);
    $emailElement->setRequired(true);

    $usernameElement = new Zend_Form_Element_Text('username');
    $usernameElement->setLabel("Shown username");
    $usernameElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $usernameElement->addValidator('stringLength', false, array(2, 64));
    $usernameElement->addValidator(new Unplagged_Validate_NoRecordExists('Application_Model_User','username'));
    $usernameElement->setAttrib('maxLength', 64);
    $usernameElement->setRequired(true);

    $passwordElement = new Zend_Form_Element_Password('password');
    $passwordElement->setLabel("Password");
    $passwordElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $passwordElement->addValidator('stringLength', false, array(8, 32));
    $passwordElement->setAttrib('maxLength', 32);
    $passwordElement->setRequired(true);


    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Register');
    $submitElement->setIgnore(true);
    $submitElement->setAttrib('class', 'submit');
    $submitElement->removeDecorator('DtDdWrapper');

    $this->addElements(array(
      $emailElement
      , $usernameElement
      , $passwordElement
    ));

    $this->addDisplayGroup(array(
      'email'
      , 'password'
      , 'username'
        )
        , 'credentialGroup'
        , array('legend'=>'Credential Information')
    );

    $this->addElements(array(
      $submitElement
    ));
  }

}

