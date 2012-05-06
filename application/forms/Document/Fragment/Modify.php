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
 * The form class generates a form for creating a new fragment.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 */
class Application_Form_Document_Fragment_Modify extends Zend_Form{

  private $types = array();
  private $documents = array();

  public function __construct(){
    $em = Zend_Registry::getInstance()->entitymanager;

    $query = $em->createQuery("SELECT t FROM Application_Model_Document_Fragment_Type t");
    $types = $query->getResult();

    $params["types"] = array();
    foreach($types as $type){
      $params["types"][$type->getId()] = $type->getName();
    }

    $query = $em->createQuery("SELECT d FROM Application_Model_Document d");
    $documents = $query->getResult();

    $params["documents"] = array();
    foreach($documents as $document){
      $params["documents"][$document->getId()] = $document->getTitle();
    }

    $this->types = $params['types'];
    $this->documents = $params['documents'];

    parent::__construct();
  }
// @todo add validator for line numbers (line to has to be bigger than line from)
  /**
   * Creates the form to register a new user.
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');
    $this->setAction("/document_fragment/create/");

    // general group
    $typeElement = new Zend_Form_Element_Select('type');
    $typeElement->setLabel("Type");
    $typeElement->addMultiOptions($this->types);
    $typeElement->setRequired(true);

    $noteElement = new Zend_Form_Element_Textarea('note');
    $noteElement->setLabel("Note");

    // candidate group    
    $candidateDocumentElement = new Zend_Form_Element_Select('candidateDocument');
    $candidateDocumentElement->setLabel("Document");
    $candidateDocumentElement->addMultiOptions($this->documents);

    $candidatePageFromElement = new Zend_Form_Element_Select('candidatePageFrom');
    $candidatePageFromElement->setLabel("Page from");
    $candidatePageFromElement->setRequired(true);

    $candidateLineFromElement = new Zend_Form_Element_Select('candidateLineFrom');
    $candidateLineFromElement->setLabel("Line from");
    $candidateLineFromElement->setRequired(true);

    $candidatePageToElement = new Zend_Form_Element_Select('candidatePageTo');
    $candidatePageToElement->setLabel("Page to");
    $candidatePageToElement->setRequired(true);
    
    $candidateLineToElement = new Zend_Form_Element_Select('candidateLineTo');
    $candidateLineToElement->setLabel("Line to");
    $candidateLineToElement->setRequired(true);

    // source group
    $sourceDocumentElement = new Zend_Form_Element_Select('sourceDocument');
    $sourceDocumentElement->setLabel("Document");
    $sourceDocumentElement->addMultiOptions($this->documents);

    $sourcePageFromElement = new Zend_Form_Element_Select('sourcePageFrom');
    $sourcePageFromElement->setLabel("Page from");
    $sourcePageFromElement->setRequired(true);

    $sourceLineFromElement = new Zend_Form_Element_Select('sourceLineFrom');
    $sourceLineFromElement->setLabel("Line from");
    $sourceLineFromElement->setRequired(true);

    $sourcePageToElement = new Zend_Form_Element_Select('sourcePageTo');
    $sourcePageToElement->setLabel("Page to");
    $sourcePageToElement->setRequired(true);
    
    $sourceLineToElement = new Zend_Form_Element_Select('sourceLineTo');
    $sourceLineToElement->setLabel("Line to");
    $sourceLineToElement->setRequired(true);


    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Create fragment');
    $submitElement->setIgnore(true);
    $submitElement->setAttrib('class', 'submit');
    $submitElement->removeDecorator('DtDdWrapper');

    $this->addElements(array(
      $typeElement
      , $noteElement
      
      , $candidateDocumentElement
      , $candidatePageFromElement
      , $candidatePageToElement
      , $candidateLineFromElement
      , $candidateLineToElement
      
      , $sourceDocumentElement
      , $sourcePageFromElement
      , $sourcePageToElement
      , $sourceLineFromElement
      , $sourceLineToElement
    ));

    $this->addDisplayGroup(array(
      'type'
      , 'note'
        )
        , 'generalGroup'
        , array('legend'=>'General Information')
    );

    $this->addDisplayGroup(array(
        'candidateDocument'
      , 'candidatePageFrom'
      , 'candidateLineFrom'
      , 'candidatePageTo'
      , 'candidateLineTo'
        )
        , 'candidateGroup'
        , array('legend'=>'Candidate Information', 'class'=>'two-column-form')
    );

    $this->addDisplayGroup(array(
        'sourceDocument'
      , 'sourcePageFrom'
      , 'sourceLineFrom'
      , 'sourcePageTo'
      , 'sourceLineTo'
        )
        , 'sourceGroup'
        , array('legend'=>'Source Information', 'class'=>'two-column-form')
    );

    $this->addElements(array(
      $submitElement
    ));
  }

}
