<?php

/*
* To change this template, choose Tools | Templates
* and open the template in the editor.
*/
class GoogleSearchController extends Zend_Controller_Action
{
    
    public function indexAction()
    {
        // action body
        $searchForm = new Application_Form_Google_Search;
       /* $searchForm->setAction('http://www.google.de/search');
$searchForm->setMethod('GET');*/

        $this->view->searchForm = $searchForm;
    }
    
    public function searchAction()
    {
        
    }
    
 
 }
?>