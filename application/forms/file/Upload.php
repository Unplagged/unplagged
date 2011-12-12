<?php

class Application_Form_File_Upload extends Zend_Form{

  public function init(){
    $this->setName('file');
    $this->setAction("");
    $this->setAttrib('enctype', 'multipart/form-data');

    //Zend_Form_Element_File & SubmitButton
    $elementfile = new Zend_Form_Element_File('filepath');
    $elementfile->setLabel('Filepath')->setRequired(true);

    $elementnewname = new Zend_Form_Element_Text('newName');
    $elementnewname->setLabel('New filename');

    $elementsubmit = new Zend_Form_Element_Submit('submit');
    
    $elementsubmit->setLabel('Upload file');
    $elementsubmit->removeDecorator('DtDdWrapper');

    $this->addElements(array(
      $elementfile
      ,$elementnewname
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
