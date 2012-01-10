<?php

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