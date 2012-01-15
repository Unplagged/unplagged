<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class GoogleresultsController extends Zend_Controller_Action
{
    
    public function indexAction()
    {
        // action body
        $resultForm = new Application_Form_Google_Results;
        $this->view->resultForm = $resultForm;
    }
     
 }
?>
