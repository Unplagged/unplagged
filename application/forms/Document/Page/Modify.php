<?php

class Application_Form_Document_Page_Modify extends Zend_Form{

  /**
   * Creates the form to add/edit a document page.
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');

    $pageNumberElement = new Zend_Form_Element_Text('pageNumber');
    $pageNumberElement->setLabel("Seitenzahl");
    $pageNumberElement->addValidator('regex', false, array('/^[0-9]/i'));
    $pageNumberElement->setRequired(true);

    $contentElement = new Zend_Form_Element_Textarea('content');
    $contentElement->setLabel("Content");

    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Save');
    $submitElement->setIgnore(true);
    $submitElement->setAttrib('class', 'submit');
    $submitElement->removeDecorator('DtDdWrapper');

    $this->addElements(array(
      $pageNumberElement
      , $contentElement 
    ));

    $this->addDisplayGroup(array('pageNumber', 'content')
      , 'detailsGroup'
      , array('legend'=>'Page details')
    );

    $this->addElements(array(
      $submitElement
      )
        );
  }

}

?>