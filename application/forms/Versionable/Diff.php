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
 * The form class generates a form for choosing the versions to diff of a versionable element.
 */
class Application_Form_Versionable_Diff extends Zend_Form{

  private $versions = array();
  private $action;
  
  public function __construct($params = array())
  {
    $this->versions = $params['versions'];
    $this->action = $params['action'];
    
    parent::__construct();
  }
  
  /**
   * Creates the form to diff to versionable elements.
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');
    $this->setAction($this->action);
    
    // first version
    $firstVersionElement = new Zend_Form_Element_Select('firstVersion');
    $firstVersionElement->setLabel("Version");
    $firstVersionElement->addMultiOptions($this->versions);
    $firstVersionElement->setRequired(true);
    
    // source group
    $secondVersionElement = new Zend_Form_Element_Select('secondVersion');
    $secondVersionElement->setLabel("Version");
    $secondVersionElement->addMultiOptions($this->versions);
    $secondVersionElement->setRequired(true);
    

    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Show changes');
    $submitElement->setOptions(array('class'=>'btn btn-primary'));

    $this->addElements(array(
      $firstVersionElement
      , $secondVersionElement
    ));
    
   $this->addDisplayGroup(array(
      'firstVersion'
        )
        , 'firstVersionGroup'
        , array('legend'=>'First version', 'class' => 'two-column-form clearfix')
    );
        
    $this->addDisplayGroup(array(
      'secondVersion'
        )
        , 'secondVersionGroup'
        , array('legend'=>'Second version', 'class' => 'two-column-form clearfix')
    );

    $this->addElements(array(
      $submitElement
    ));
  }

}
