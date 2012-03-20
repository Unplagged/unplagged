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
class Application_Form_Notes_Entry extends Zend_Form{

    private $userId;
    
  public function __construct($userId){
    $this->userId = $userId;
    parent::__construct();
  }
    
  public function init(){
    $em = Zend_Registry::getInstance()->entitymanager;
    $defaultNamespace = new Zend_Session_Namespace('Default');
    
    $this->setMethod('post');
    $this->setAction("entry");
    $this->setAttrib('enctype', 'multipart/form-data');

    $textAreaRead =     new Zend_Form_Element_Textarea('notesTextareaRead');
    $textAreaInput = new Zend_Form_Element_Textarea('notesTextareaInput');
    $textAreaRead->setValue($this->userId);
    // Save button
    $submit = new Zend_Form_Element_Submit('saveEntry');
    $submit->setValue("saveEntry");
    $submit->setLabel('save entry');

    $this->addElements(array(      
        $textAreaRead,
        $textAreaInput,
        $submit));
  }
}
?>