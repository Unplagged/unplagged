<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Google_Results extends Zend_Form
{
     public function init(){
      /*  $this->setMethod('GET');
        $this->setAction("http://www.google.de/search");
        //$this->setAttrib('target', '_self');
          $this->setAttrib('target', '../../views/scripts/googleresultsiframe/');


        $searchinput = new Zend_Form_Element_Text('q');
        $searchinputhidden = new Zend_Form_Element_Hidden('hl');
        $searchinputhidden->setValue('de');
        $submit= new Zend_Form_Element_Submit('btnG');
        $submit->setValue("Google Search");
        $submit->setLabel('Google Suche');
        
        $this->addElements(array($searchinput, $submit ));*/
         ?>

            <iframe src="<?= $this->url(array(
    'module'=>'Results',
    'controller'=>'Googleresults',
    'action'=>'index'),
    'default', true); ?>  ">

    <?= $this->render('../views/scripts/google/googleresults/index.phtml'); ?>
</iframe>
    <?php


     }
     
    /* public function indexAction(){
         
     }*/
}
?>
