<?php

// include the domPdf library
require_once(BASE_PATH.'/library/dompdf/dompdf_config.inc.php');
spl_autoload_register('DOMPDF_autoload');
				
class ReportController extends Unplagged_Controller_Versionable{

  public function init(){
    parent::init();

    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    Zend_Layout::getMvcInstance()->sidebar = 'fragment-tools';
    Zend_Layout::getMvcInstance()->versionableId = $input->id;
  }

  public function indexAction(){
    //$this->_helper->redirector('list', 'report');
  }
  
  public function createAction(){
	$input = new Zend_Filter_Input(array('page'=>'Digits', 'content'=>'StripTags'), null, $this->_getAllParams());
    //$page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->page);

     $modifyForm = new Application_Form_Report_Modify();
	 	 
	 $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
		
	// get current case
	$currentCase = $user->getCurrentCase();
	//$this->_helper->flashMessenger->addMessage( $currentCase);
	
	// get current case name
	$case = $currentCase->getPublishableName();	 
	$modifyForm->getElement("case")->setValue($case);
	
	// get files of current case
	$files = $currentCase->getFiles();
	$rfile = null;
	
	foreach($files as $file) {
			if( $file->getIsTarget()){
				//$this->_helper->flashMessenger->addMessage( $file->getId());
				$rfile = $file;
		}
	}
		
	$formData = $this->_request->getPost();
    //if($this->_request->isPost()){// && empty($input->page)){
	
	if($modifyForm->isValid($formData)){
				
		$casename =  $formData['case'];
		$note = $formData['note'];
		
		$filepath = BASE_PATH . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "reports";
		$filename = $filepath . DIRECTORY_SEPARATOR . "Report_". $casename . ".pdf";
		
		$query = $this->_em->createQuery("SELECT f FROM Application_Model_Document_Fragment f");
		$fragments = $query->getResult();
				
		// save report to database to get an Id
		$data = array();
		$data["title"] = $casename;
	    $data["state"] = $this->_em->getRepository('Application_Model_State')->findOneByName('report_generated');
	    $data["user"] = $user;
	    $data["file"] = $rfile;
		$data["filePath"] = $filename;
	    $report = new Application_Model_Report($data);
			
		$this->_em->persist($report);
		$this->_em->flush();		 
		 
		
		$html = Unplagged_HtmlLayout::htmlLayout($casename,$note,$fragments);
			      
		$dompdf = new DOMPDF();
		$dompdf->set_paper('a4', 'portrait');
		$dompdf->load_html($html);
		$dompdf->render();		
		//$dompdf->stream($filename);
		$output = $dompdf->output();
		file_put_contents($filename, $output);	 				
		
		
      if($output){	  
		$this->_helper->flashMessenger->addMessage('The report was created successfully.');
		//$this->_helper->flashMessenger->addMessage($output);
        $this->_helper->redirector('list', 'report');
      }
	   
    }
    
    $this->view->title = "Create report";
    $this->view->modifyForm = $modifyForm;
    //$this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'report'));
  }
	
	public function listAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());

    $query = $this->_em->createQuery("SELECT r FROM Application_Model_Report r");
    $count = $this->_em->createQuery("SELECT COUNT(r.id) FROM Application_Model_Report r");

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

    $this->view->paginator = $paginator;

    Zend_Layout::getMvcInstance()->sidebar = null;
    Zend_Layout::getMvcInstance()->versionableId = null;
  }
  
  public function downloadAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $report = $this->_em->getRepository('Application_Model_Report')->findOneById($input->id);
      if($report){
        // disable view
		
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);		
		$report_name = "Report_" . $report->getTitle() . ".pdf";
	    $downloadPath = $report->getFilePath();
		
	    // set headers
	    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	    header("Content-Description: File Transfer");
	    header("Content-Disposition: attachment; filename=\"" . $report_name . "\"");
	    header("Content-type: application/pdf");
	    header("Content-Transfer-Encoding: binary");

        readfile($downloadPath);
      }else{
        $this->_helper->FlashMessenger('No report found.');
        $this->_helper->redirector('list', 'report');
      }
    }else{
      $this->_helper->FlashMessenger('The report couldn\'t be found.');
      $this->_helper->redirector('list', 'report');
    }
  }
  
}
?>