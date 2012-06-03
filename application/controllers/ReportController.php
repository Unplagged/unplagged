<?php
require_once(realpath(dirname(__FILE__)) . "/../models/Base.php");
class ReportController extends Unplagged_Controller_Versionable {

    public function init() {
        parent::init();
    }

    public function indexAction() {
    }

    public function listAction() {

        $input = new Zend_Filter_Input(array('page' => 'Digits', 'content' => 'StripTags'), null, $this->_getAllParams());
        $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);

        // get current case
        $currentCase = $user->getCurrentCase();
        if ($currentCase) {

            // get current case name
            $case = $currentCase->getPublishableName();
            $this->view->title = "Create report of " . $case;

            $formData = $this->_request->getPost();

            //Cron_Document_Page_Reportcreater::start();
            // Create a report_requested task
            $data = array();
            $data["initiator"] = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
            $data["action"] = $this->_em->getRepository('Application_Model_Action')->findOneByName('report_requested');
            $data["state"] = $this->_em->getRepository('Application_Model_State')->findOneByName('task_scheduled');
            $task = new Application_Model_Task($data);
            $this->_em->persist($task);
            $this->_em->flush();

            // Inform the user that the process will be started
            $this->_helper->flashMessenger->addMessage(array('success'=>array('The report generation for "%s" has been started.', array($case))));
        } else {
            $this->_helper->flashMessenger->addMessage('You have to select a case, before you can start the report creation.');
            $this->_helper->viewRenderer->setNoRender(true);
            Zend_Layout::getMvcInstance()->sidebar = null;
        }

        $input = new Zend_Filter_Input(array('page' => 'Digits'), null, $this->_getAllParams());
        $query = $this->_em->createQuery("SELECT r FROM Application_Model_Report r WHERE r.case = :caseId");
        $query->setParameter('caseId', $currentCase->getId());
        $count = $this->_em->createQuery("SELECT COUNT(r.id) FROM Application_Model_Report r WHERE r.case = :caseId");
        $count->setParameter('caseId', $currentCase->getId());

        $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
        $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
        $paginator->setCurrentPageNumber($input->page);

        $this->view->paginator = $paginator;

//        Zend_Layout::getMvcInstance()->sidebar = null;
//        Zend_Layout::getMvcInstance()->versionableId = null;
    }

    public function downloadAction() {
        $input = new Zend_Filter_Input(array('id' => 'Digits'), null, $this->_getAllParams());

        if (!empty($input->id)) {
            $report = $this->_em->getRepository('Application_Model_Report')->findOneById($input->id);
            if ($report) {
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
            } else {
                $this->_helper->FlashMessenger('No report found.');
                $this->_helper->redirector('list', 'report');
            }
        } else {
            $this->_helper->FlashMessenger('The report couldn\'t be found.');
            $this->_helper->redirector('list', 'report');
        }
    }

}

?>