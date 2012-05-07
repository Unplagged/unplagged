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
 * Creates a form to select documents for simtext.
 */
class Application_Form_Document_Page_Simtext extends Zend_Form{

  private $documents = array();

  public function __construct(){
    $em = Zend_Registry::getInstance()->entitymanager;

    $query = $em->createQuery("SELECT d FROM Application_Model_Document d");
    $documents = $query->getResult();

    $params["documents"] = array();
    foreach($documents as $document){
      $params["documents"][$document->getId()] = $document->getTitle();
    }

    $this->documents = $params['documents'];

    parent::__construct();
  }
  
  /**
   * Creates the form to simtext a document page.
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');

    $titleElement = new Zend_Form_Element_Text('title');
    $titleElement->setLabel("Report Title");
    $titleElement->setRequired(true);
    
    $documentsElement = new Zend_Form_Element_Multiselect('documents');
    $documentsElement->setLabel("Documents");
    $documentsElement->addMultiOptions($this->documents);
    $documentsElement->addValidator('regex', false, array('/^[0-9]/i'));
    $documentsElement->setRequired(true);

    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Simtext page');
    $submitElement->setOptions(array('class'=>'btn btn-primary'));

    $this->addElements(array(
       $titleElement
      ,$documentsElement
    ));

    $this->addDisplayGroup(array('title', 'documents')
        , 'documentsGroup'
        , array('legend'=>'Document selection')
    );

    $this->addElements(array(
      $submitElement
        )
    );
  }
}
?>