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
class Application_Form_Rating_Modify extends Zend_Form{

  /**
   * Creates the form to add/edit a rating.
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');

    $ratingElement = new Zend_Form_Element_Select('rating');
    $ratingElement->addMultiOption('1', 'Approve');
    $ratingElement->addMultiOption('0', 'Decline');
    
    $ratingElement->setLabel("Rating");
    $ratingElement->setRequired(true);

    $reasonElement = new Zend_Form_Element_Textarea('reason');
    $reasonElement->setLabel("Reason");

    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Create rating');
    $submitElement->setOptions(array('class'=>'btn btn-primary'));

    $this->addElements(array(
        $reasonElement
      , $ratingElement
    ));

    $this->addDisplayGroup(array('rating', 'reason')
        , 'detailsGroup'
        , array('legend'=>'Rating details')
    );

    $this->addElements(array(
      $submitElement
        )
    );
  }
}
?>