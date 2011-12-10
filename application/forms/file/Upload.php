<?php

class Application_Form_File_Upload extends Zend_Form
{
         public function init()
	 {
		 $this->setName('file');
                 $this->setAction("");
		 $this->setAttrib('enctype', 'multipart/form-data');
		 
		 //Zend_Form_Element_File & SubmitButton
		 $elementfile = new Zend_Form_Element_File('filepath');
		 $elementfile->setLabel('Dateipfad:')->setRequired(true);
		 $elementsubmit = new Zend_Form_Element_Submit('submit');
		 $elementsubmit->setLabel('Datei hochladen')->setAttrib('id', 'submitbutton');
                 $elementnewname = new Zend_Form_Element_Text('newName');
                 $elementnewname->setLabel('(optional) Neuen Dateinamen eingeben:');

		// Elemente werden zusammengefuegt
		$this->addElements(array($elementfile, $elementnewname,$elementsubmit));
	 }
    
}
?>
