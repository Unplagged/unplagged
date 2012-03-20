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
class Application_Form_File_Upload extends Zend_Form{

  public function init(){
    $this->setName('file');
    $this->setAction("");
    $this->setAttrib('enctype', 'multipart/form-data');

    //Zend_Form_Element_File & SubmitButton
    $elementfile = new Zend_Form_Element_File('filepath');
    $elementfile->setLabel('Dateipfad:')->setRequired(true);

    $elementnewname = new Zend_Form_Element_Text('newName');
    $elementnewname->setLabel('Neuer Dateiname:');

    $elementsubmit = new Zend_Form_Element_Submit('submit');
    $elementsubmit->setLabel('Datei hochladen');
    $elementsubmit->removeDecorator('DtDdWrapper');

    $this->addElements(array(
      $elementfile
      , $elementnewname
    ));

    $this->addDisplayGroup(array(
      'filepath'
      , 'newName'
        )
        , 'fileGroup'
        , array('legend'=>'File Information')
    );

    $this->addElements(array(
      $elementsubmit
    ));
  }
}
?>
