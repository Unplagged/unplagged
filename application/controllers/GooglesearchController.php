<?php

/*
* To change this template, choose Tools | Templates
* and open the template in the editor.
*/
class GooglesearchController extends Zend_Controller_Action
{
    public function init()
    {
 
        $this->view->headScript()->appendFile('path/to/the/javascript/file');
    }
    
    public function indexAction()
    {
        // action body
        $searchForm = new Application_Form_Google_Search;
        $this->view->searchForm = $searchForm;
        //$this->_helper->redirector('index', 'googlesearch');
    }
    
    public function searchAction()
    {
        
    }
 }
?>