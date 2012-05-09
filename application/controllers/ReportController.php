<?php
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
   
    if($this->_request->isPost()){// && empty($input->page)){
   	 require_once(BASE_PATH.'/library/dompdf/dompdf_config.inc.php');
		 //require_once('C:/xampp/unplagged/library/dompdf/dompdf_config.inc.php');
		 spl_autoload_register('DOMPDF_autoload');
		
		$html = 
	      '<html><body>'.
	      '<p>Put your html here, or generate it with your favourite '.
	      'templating system.</p>'.
	      '</body></html>';
		
		$dompdf = new DOMPDF();
		$dompdf->set_paper('a4', 'portrait');
		$dompdf->load_html($html);
		$dompdf->render();
		$filepath = BASE_PATH . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "reports";
		$filename = $filepath . DIRECTORY_SEPARATOR . "Brochure.pdf";
		//$dompdf->stream($filename);
		$output = $dompdf->output();
		 file_put_contents($filename, $output);
	 
      if($filename){
    
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

    $query = $this->_em->createQuery("SELECT f FROM Application_Model_Document_Fragment f");
    $count = $this->_em->createQuery("SELECT COUNT(f.id) FROM Application_Model_Document_Fragment f");

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