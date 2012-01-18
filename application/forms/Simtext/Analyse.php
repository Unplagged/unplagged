<?php

class Application_Form_Simtext_Analyse extends Zend_Form
{
     public function init(){
        $this->setMethod('POST');
        $this->setAction("/simtext/compare");
		
        $submit= new Zend_Form_Element_Submit('submit');
        
        $submit->setLabel('Text analyse');
        
        $this->addElements(array($submit ));
     }
}

?>