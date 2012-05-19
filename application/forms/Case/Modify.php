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
class Application_Form_Case_Modify extends Zend_Form{

  /**
   * Creates the form to create a new case.
   * 
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');
    $this->setAction("/case/create");

    $nameElement = new Zend_Form_Element_Text('name');
    $nameElement->setLabel("Name");
    $nameElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $nameElement->addValidator('stringLength', false, array(2, 64));
    $nameElement->setRequired(true);

    $aliasElement = new Zend_Form_Element_Text('alias');
    $aliasElement->setLabel("Pseudonym");
    $aliasElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $aliasElement->addValidator('stringLength', false, array(2, 64));
    $aliasElement->setAttrib('maxLength', 64);
    $aliasElement->setRequired(false);
    
    $abbreviationElement = new Zend_Form_Element_Text('abbreviation');
    $abbreviationElement->setLabel("Abbreviation");
    $abbreviationElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $abbreviationElement->addValidator('stringLength', false, array(2, 5));
    $abbreviationElement->setAttrib('maxLength', 5);
    $abbreviationElement->setRequired(true);

    $tagsElement = new Zend_Form_Element_Text('tags[]');
    $tagsElement->setLabel('Tags');
    $tagsElement->setDecorators(array(array('ViewScript', array(
      'viewScript' => '/tag/_element.phtml',
      'callback' => '/tag/autocomplete',
      'disabled' => false
    ))));

    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Create case');
    $submitElement->setOptions(array('class'=>'btn btn-primary'));

    $this->addElements(array(
      $nameElement
      , $aliasElement
      , $abbreviationElement
      , $tagsElement
    ));

    $this->addDisplayGroup(array('name', 'alias', 'abbreviation', 'tags', 'collaborator')
        , 'credentialGroup'
        , array('legend'=>'Case Information')
    );

    $this->addElements(array(
      $submitElement
        )
    );
  }

}
?>