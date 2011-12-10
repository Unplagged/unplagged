<?php

/**
 * 
 */

/**
 * The form class generates a form for registering a new user.
 * 
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 */
class Application_Form_Case_Create extends Zend_Form{

  /**
   * Creates the form to create a new case.
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');
    $this->setAction("/case/create");

    $nameElement = new Zend_Form_Element_Text('name');
    $nameElement->setLabel("Name");
    $nameElement->setRequired(true);

    $aliasElement = new Zend_Form_Element_Text('alias');
    $aliasElement->setLabel("Alias");
    $aliasElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $aliasElement->addValidator('stringLength', false, array(2, 64));
    $aliasElement->setAttrib('maxLength', 64);
    $aliasElement->setRequired(false);

    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Create');
    $submitElement->setIgnore(true);
    $submitElement->setAttrib('class', 'submit');
    $submitElement->removeDecorator('DtDdWrapper');

    $this->addElements(array(
      $nameElement
      , $aliasElement 
    ));

    $this->addDisplayGroup(array('name', 'alias')
      , 'credentialGroup'
      , array('legend'=>'Case creation')
    );

    $this->addElements(array(
      $submitElement
      )
        );
  }

}

?>