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
		 if($this->_request->isPost()){
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
	  
	 
		
		// // start task
		// // $data = array();
		// // $data["initiator"] = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
		// // $data["ressource"] = $report;
		// // $data["action"] = $this->_em->getRepository('Application_Model_Action')->findOneByName('page_simtext');
		// // $data["state"] = $this->_em->getRepository('Application_Model_State')->findOneByName('task_scheduled');
		// // $task = new Application_Model_Task($data);

		 
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

    // // generate the action dropdown for each fragment
    // foreach($paginator as $fragment):
      // $fragment->actions = array();

      // $action['link'] = '/document_fragment/edit/id/' . $fragment->getId();
      // $action['title'] = 'Edit fragment';
      // $action['icon'] = 'images/icons/pencil.png';
      // $fragment->actions[] = $action;

      // $action['link'] = '/document_fragment/delete/id/' . $fragment->getId();
      // $action['title'] = 'Remove fragment';
      // $action['icon'] = 'images/icons/delete.png';
      // $fragment->actions[] = $action;
    // endforeach;

    $this->view->paginator = $paginator;

    Zend_Layout::getMvcInstance()->sidebar = null;
    Zend_Layout::getMvcInstance()->versionableId = null;
  }
  
}
?>