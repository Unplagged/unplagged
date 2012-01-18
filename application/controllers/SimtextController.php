<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class SimtextController extends Zend_Controller_Action
{
    public function init(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
    $this->_defaultNamespace = new Zend_Session_Namespace('Default');
    $this->view->flashMessages = $this->_helper->flashMessenger->getMessages();
	}

	public function indexAction(){
		$query = $this->_em->createQuery('SELECT d FROM Application_Model_Document d');
		$documents = $query->getResult();

		$this->view->listDocuments = $documents;        
	}
    
	
    
	// calling simtext action
    public function compareAction()
    {
		$simForm = new Application_Form_Simtext_Analyse;
		$request = $this->getRequest();
		if($request->isPost())
		{
			$this->_helper->flashMessenger->addMessage('Text analyse running');
			//$query = $this->_em->createQuery('SELECT f FROM Application_Model_File f');//WHERE filename=\'a.txt\'');
			//$text1 = $query->getResult();
			
			$fileDirectory = "storage" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR;
			$reportDirectory = "storage" . DIRECTORY_SEPARATOR . "reports" . DIRECTORY_SEPARATOR;
						
			$file1_path = APPLICATION_PATH . DIRECTORY_SEPARATOR . $fileDirectory . "a.txt";
			$file2_path = APPLICATION_PATH . DIRECTORY_SEPARATOR . $fileDirectory . "b.txt";
			
			if($file1_path!="" && $file2_path!="")
			{
				$report_name = "test_report.txt";
				$report_path = APPLICATION_PATH . DIRECTORY_SEPARATOR . $reportDirectory. "$report_name";
				
				// create file in report directory
				$reportfile = fopen($report_path, 'w') or die("can't open file");
				//$reporter_name = "test.txt";
				$this->_helper->flashMessenger->addMessage($file1_path);
				$this->_helper->flashMessenger->addMessage($file2_path);
				$this->_helper->flashMessenger->addMessage($report_path);
				
				// running simtext and return a report file
				 $simtext = new Unplagged_Simtext_SimtextRun();
				 $report = $simtext->runSimtext($file1_path,$file2_path,$report_path);
				
				//$this->_helper->flashMessenger->addMessage($report);
				
				if(empty($report)){
				  $this->_helper->flashMessenger->addMessage('The report could not be created.');
				}else{
				  //$this->_em->persist($report);
				  //$this->_em->flush();
				  $this->_helper->flashMessenger->addMessage('The report was successfully created.');
				  $this->_helper->redirector('compare', 'simtext');
				  
				  // close file stream
				  fclose($reportfile);
				}
			}
			else{
				//$this->_helper->flashMessenger->addMessage($text1);
				$this->_helper->flashMessenger->addMessage('Comparing failed');
				$this->_helper->redirector('compare', 'simtext');
				
				
			}
		}
		
		$this->view->simForm = $simForm;
		    
        
    }
    
 
 }
?>
