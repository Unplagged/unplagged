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
class Application_Form_Permission_Modify extends Zend_Form{

  /**
   * Creates the form to create a new case.
   * 
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');
    $this->setAction("/permission/edit");

    $collaboratorsElement = new Zend_Form_Element_Text('permissions[]');
    $collaboratorsElement->setLabel('People having access to the element');
    // @todo: add filter only for some permission types
    $collaboratorsElement->setDecorators(array(array('ViewScript', array(
      'viewScript' => '/permission/_element.phtml',
      'callback' => '/permission/autocomplete', //@todo /case/caseId can be added as param if needed
      'disabled' => false,
      'permissionActions' => array('authorize', 'delete', 'update', 'read')
    ))));

    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Save permissions');
    $submitElement->setOptions(array('class'=>'btn btn-primary'));

    $this->addElements(array(
      $collaboratorsElement
    ));

    $this->addDisplayGroup(array('permissions')
        , 'credentialGroup'
        , array('legend'=>'Permissions Information')
    );

    $this->addElements(array(
      $submitElement
        )
    );
  }

}
?>