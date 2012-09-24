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

    // general group
    $titleElement = new Zend_Form_Element_Text('title');
    $titleElement->setLabel("Title");
    $titleElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $titleElement->addValidator('stringLength', false, array(2, 64));
    $titleElement->setRequired(true);

    // bibTex group
    $typeElement = new Zend_Form_Element_Select('bibSourceType');
    $typeElement->setLabel("Document type: ");
    $typeElement->addMultiOptions(array('full'=>'Full', 'book'=>'Book', 'periodical'=>'Periodical', 'essay'=>'Essay'));

    $fieldIs = array();
    $fieldIs[] = 'bibSourceType';
    
    foreach(Application_Model_BibTex::$accessibleFields as $fieldName=>$field){
      $fieldId = 'bib' . ucfirst($fieldName);
      $fieldIs[] = $fieldId;
      $bibTexElement = new Zend_Form_Element_Text($fieldId);
      $bibTexElement->setLabel($field['label']);

      $classes = array();
      foreach(Application_Model_BibTex::$sourceTypes as $name => $fields){
        if(in_array($fieldName, Application_Model_BibTex::$sourceTypes[$name])){
          $classes[] = $name;
        }
      }
      $bibTexElement->setOptions(array('class'=>'bibtex ' . implode(' ', $classes)));
      if($field['required']){
        $bibTexElement->setRequired(true);
      }

      $this->addElement($bibTexElement);
    }

    //submit
    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Create document');
    $submitElement->setOptions(array('class'=>'btn btn-primary'));

    $this->addElements(array(
      $titleElement
      , $typeElement
    ));

    $this->addDisplayGroup(array('title')
        , 'generalGroup'
        , array('legend'=>'Document Information')
    );


    $this->addDisplayGroup($fieldIs
        , 'bibTexGroup'
        , array('legend'=>'BiBTex Information')
    );

    $this->addElements(array(
      $submitElement
        )
    );
  }

}