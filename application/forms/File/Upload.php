<?php

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
