<?php

class Application_Form_Google_Search extends Zend_Form
{
     public function init(){
        $this->setMethod('GET');
        $this->setAction("http://www.google.de/search");
        //$this->setAttrib('target', '_top');
        $this->setAttrib('target', 'test');

        
       // $textarea = new Zend_Form_Element_Textarea('test');
        $searchinput = new Zend_Form_Element_Text('q');
        $searchinputhidden = new Zend_Form_Element_Hidden('hl');
        $searchinputhidden->setValue('de');
        $submit= new Zend_Form_Element_Submit('btnG');
        $submit->setValue("Google Search");
        //$submit->setAttrib('onclick', 'javascript&#058;location.reload(true)');
        $submit->setLabel('Google Suche');
        
        $this->addElements(array($searchinput, $submit, /*$textarea*/ ));
     }
}

?>
