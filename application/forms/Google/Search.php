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
class Application_Form_Google_Search extends Zend_Form{

  public function init(){
    $this->setMethod('GET');
    $this->setAction("http://www.google.de/search");
    //$this->setAttrib('target', '_top');
    $this->setAttrib('target', 'test');


    // $textarea = new Zend_Form_Element_Textarea('test');
    $searchinput = new Zend_Form_Element_Text('q');
    $searchinputhidden = new Zend_Form_Element_Hidden('hl');
    $searchinputhidden->setValue('de');
    $submit = new Zend_Form_Element_Submit('btnG');
    $submit->setValue("Google Search");
    //$submit->setAttrib('onclick', 'javascript&#058;location.reload(true)');
    $submit->setLabel('Google Suche');

    $this->addElements(array($searchinput, $submit, /* $textarea */));
  }
}
?>
