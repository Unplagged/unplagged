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
class Application_Form_Setting_Action_Modify extends Zend_Form{

  /**
   * Creates the form to add/edit an action.
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');
    $this->setAction("/setting/create-action");

    $nameElement = new Zend_Form_Element_Text('name');
    $nameElement->setLabel("Internal name");
    $nameElement->setRequired(true);

    $titleElement = new Zend_Form_Element_Text('title');
    $titleElement->setLabel("Title");
    $titleElement->setRequired(true);
    
    $descriptionElement = new Zend_Form_Element_Textarea('description');
    $descriptionElement->setLabel("Description");

    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Create action');
    $submitElement->setOptions(array('class'=>'btn btn-primary'));

    $this->addElements(array(
        $nameElement
      , $titleElement
      , $descriptionElement
    ));

    $this->addDisplayGroup(array('name', 'title', 'description')
        , 'settingGroup'
        , array('legend'=>'Settings')
    );

    $this->addElements(array(
      $submitElement
        )
    );
  }
}
?>