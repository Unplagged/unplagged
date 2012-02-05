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
class Application_Form_Document_Page_Dehyphen extends Zend_Form{

  private $pageLines;

  /**
   * Creates the form to dehyphen a document page.
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');

    foreach($this->pageLines as $lineNumber=>$pageLine){
      if(($pageLine["hasHyphen"])){
        $this->addElement('Checkbox', $lineNumber . "", array('belongsTo'=>'pageLine', 'decorators'=>array(
            array('ViewHelper'),
            array('Label', array('placement'=>'APPEND', 'separator'=>' ')),
            array('HtmlTag', array('tag'=>'div', 'class'=>'page-line highlight'))
            )))->$lineNumber->setLabel($pageLine["content"]);
      }else{
        $this->addElement('Hidden', $lineNumber . "", array('belongsTo'=>'pageLine', 'decorators'=>array(
            array('ViewHelper'),
            array('Label', array('placement'=>'APPEND', 'separator'=>' ')),
            array('HtmlTag', array('tag'=>'div', 'class'=>'page-line empty'))
            )))->$lineNumber->setLabel($pageLine["content"]);
      }
    }

    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('De-hyphen');
    $submitElement->setIgnore(true);
    $submitElement->setAttrib('class', 'submit');
    $submitElement->removeDecorator('DtDdWrapper');


    $this->addElements(array(
      $submitElement
        )
    );
  }

  public function setPageLines($pageLines){
    $this->pageLines = $pageLines;
  }

}
?>