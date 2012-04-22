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
class Application_Form_Document_Page_Modify extends Zend_Form{

  /**
   * Creates the form to add/edit a document page.
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');

    $pageNumberElement = new Zend_Form_Element_Text('pageNumber');
    $pageNumberElement->setLabel("Seitenzahl");
    $pageNumberElement->addValidator('regex', false, array('/^[0-9]/i'));
    $pageNumberElement->setRequired(true);

    $contentElement = new Zend_Form_Element_Textarea('content');
    $contentElement->setLabel("Content");

    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Save page');
    $submitElement->setIgnore(true);
    $submitElement->setAttrib('class', 'submit');
    $submitElement->removeDecorator('DtDdWrapper');

    $this->addElements(array(
      $pageNumberElement
      , $contentElement
    ));

    $this->addDisplayGroup(array('pageNumber', 'content')
        , 'detailsGroup'
        , array('legend'=>'Page details')
    );

    $this->addElements(array(
      $submitElement
        )
    );
  }
}
?>