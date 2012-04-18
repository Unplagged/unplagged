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
 * This class reprents a form for creating and updating a document.
 */
class Application_Form_Document_Modify extends Zend_Form{

  /**
   * Creates the form to create a new case.
   * 
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');
    $this->setAction("/document/create");

    $titleElement = new Zend_Form_Element_Text('title');
    $titleElement->setLabel("Title");
    $titleElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $titleElement->addValidator('stringLength', false, array(2, 64));
    $titleElement->setRequired(true);


    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Create document');
    $submitElement->setIgnore(true);
    $submitElement->setAttrib('class', 'submit');
    $submitElement->removeDecorator('DtDdWrapper');

    $this->addElements(array(
      $titleElement
    ));

    $this->addDisplayGroup(array('title')
        , 'generalGroup'
        , array('legend'=>'Document Information')
    );

    $this->addElements(array(
      $submitElement
        )
    );
  }

}
?>