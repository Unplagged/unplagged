<?php

class ReportController extends Unplagged_Controller_Versionable{

  public function init(){
    parent::init();
  }

  public function indexAction(){
    
  }

  public function listAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits', 'content'=>'StripTags'), null, $this->_getAllParams());
    $case = Zend_Registry::getInstance()->user->getCurrentCase();

    $permissionAction = 'read';
    $query = 'SELECT b FROM Application_Model_Report b';
    $count = 'SELECT COUNT(b.id) FROM Application_Model_Report b';

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count, array('b.case'=>$case->getId()), null, $permissionAction));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

    $this->view->paginator = $paginator;
  }

  public function createAction(){
    $case = Zend_Registry::getInstance()->user->getCurrentCase();

    if($case){
      // get current case name
      $this->view->title = "Create report of " . $case->getPublishableName();

      $formData = $this->_request->getPost();

      //Cron_Document_Page_Reportcreater::start();
      // Create a report_requested task
      $data = array();
      $data["initiator"] = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
      $data["action"] = $this->_em->getRepository('Application_Model_Action')->findOneByName('report_requested');
      $data["state"] = $this->_em->getRepository('Application_Model_State')->findOneByName('task_scheduled');
      $data["ressource"] = $case;
      $task = new Application_Model_Task($data);
      $this->_em->persist($task);
      $this->_em->flush();

      // Inform the user that the process will be started
      $this->_helper->flashMessenger->addMessage(array('success'=>'The report-generating process has been started.'));
    }else{
      $this->_helper->flashMessenger->addMessage('You have to select a case, before you can start the report creation.');
      $this->_helper->viewRenderer->setNoRender(true);
      Zend_Layout::getMvcInstance()->sidebar = null;
    }
    $this->_helper->redirector('list', 'report');
  }

  public function downloadAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $report = $this->_em->getRepository('Application_Model_Report')->findOneById($input->id);
      if($report){
        // disable view

        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $report_name = "Report_" . $report->getTitle() . "_" . $report->getCreated()->format("Y-m-d") . ".pdf";
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
