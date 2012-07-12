<?php

class ReportController extends Unplagged_Controller_Versionable {

    public function init() {
        parent::init();

        $case = Zend_Registry::getInstance()->user->getCurrentCase();
        if (!$case) {
            $errorText = 'You have to select a case, before you can start the report creation.';
            $this->_helper->FlashMessenger(array('error' => $errorText));
            $this->redirectToLastPage();
        } elseif (!$case->getTarget()) {
            $errorText = 'You have to define a target document in your case, before you can start the report creation.';
            $this->_helper->FlashMessenger(array('error' => $errorText));
            $this->_helper->redirector('list', 'document');
        }
    }

    public function listAction() {
        $input = new Zend_Filter_Input(array('page' => 'Digits', 'content' => 'StripTags'), null, $this->_getAllParams());
        $case = Zend_Registry::getInstance()->user->getCurrentCase();

        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type' => 'report', 'action' => 'read', 'base' => null));
        $query = 'SELECT b FROM Application_Model_Report b';
        $count = 'SELECT COUNT(b.id) FROM Application_Model_Report b';

        $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count, array('b.case' => $case->getId()), null, $permission));
        $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
        $paginator->setCurrentPageNumber($input->page);

        foreach ($paginator as $report) {
            if ($report->getState()->getName() == 'scheduled') {
                // find the associated task and get percentage
                $state = $this->_em->getRepository('Application_Model_State')->findOneByName('running');
                $task = $this->_em->getRepository('Application_Model_Task')->findOneBy(array('ressource' => $report->getId(), 'state' => $state));
                if (!$task) {
                    $percentage = 0;
                } else {
                    $percentage = $task->getProgressPercentage();
                }
                $report->outputState = '<div class="progress"><div class="bar" style="width: ' . $percentage . '%;"></div></div>';
            } else {
                $report->outputState = $report->getState()->getTitle();
            }
        }

        $this->setTitle('List of final reports');
        $this->view->paginator = $paginator;
    }

    /**
     * Schedules the cronjob task for the requested report generation. 
     */
    public function createAction() {
        $registry = Zend_Registry::getInstance();
        $case = $registry->user->getCurrentCase();

        if ($case) {
            if ($this->_request->isPost()) {
                //var_dump($this->_request->getPost());
                //$report = new Application_Model_Report($this->_request->getPost());

                $report = $this->createReport($registry->user, $this->_request->getPost());
                $this->saveAction($report);

                //echo $report->getReportEvaluation();
            } else {
                $this->view->modifyForm = new Application_Form_Report_Modify();
            }
        } else {
            $this->_helper->FlashMessenger(array('error' => 'You have to select a case, before you can start the report creation.'));
            $this->_helper->redirector('list', 'report');
        }
    }

    public function saveAction($report) {
        $registry = Zend_Registry::getInstance();
        $case = $registry->user->getCurrentCase();

        //create an empty report to show the user something in the list
        //$emptyReport = $this->createEmptyReport($registry->user, $case);
        $this->_em->persist($report);

        $task = $this->createTask($registry->user, $report);
        $this->_em->persist($task);

        $case->addReport($report);
        $this->_em->persist($case);

        $this->_em->flush();

        $this->_helper->FlashMessenger(array('success' => 'The report generation has been scheduled.'));
        $this->_helper->redirector('list', 'report');
    }

    public function deleteAction() {
        $input = new Zend_Filter_Input(array('id' => 'Digits'), null, $this->_getAllParams());
        
        if (!empty($input->id)) {
            $report = $this->_em->getRepository('Application_Model_Report')->findOneById($input->id);
            if ($report) {
                /* $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type' => 'document', 'action' => 'delete', 'base' => $report));
                  if (!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)) {
                  $this->redirectToLastPage(true);
                  } */

                Unplagged_Helper::notify('report_removed', $report, Zend_Registry::getInstance()->user);

                $report->remove();
                $this->_em->persist($report);
                $this->_em->flush();

                $this->_helper->FlashMessenger(array('success' => 'The report was deleted successfully.'));
            } else {
                $this->_helper->FlashMessenger('The report does not exist.');
            }
        } else {
            $this->_helper->FlashMessenger('The report id should not be empty.');
        }

        $this->_helper->redirector('list', 'report');
        // disable view
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * Create an empty report on which the PDF creation in the cronjob will be based.
     * 
     * @param Application_Model_User $user
     * @param Application_Model_Case $case
     * @return Application_Model_Report 
     */
    private function createReport(Application_Model_User $user, $report) {
        echo $user->getCurrentCase()->getTarget()->getId();
        $data = array(
            'user' => $user,
            'case' => $user->getCurrentCase(),
            'target' => $user->getCurrentCase()->getTarget(),
            'title' => $user->getCurrentCase()->getPublishableName(),
            'state' => $this->_em->getRepository('Application_Model_State')->findOneByName('scheduled')
        );

        foreach ($report as $key => $value) {
            $data[$key] = $value;
        }

        $report = new Application_Model_Report($data);

        return $report;
    }

    /**
     * Creates the task for the cronjob to create the actual report PDF.
     * 
     * @param Application_Model_User $user
     * @param Application_Model_Case $case 
     */
    private function createTask(Application_Model_User $user, Application_Model_Report $report) {

        $data = array();
        $data['initiator'] = $user;
        $data["action"] = $this->_em->getRepository('Application_Model_Action')->findOneByName('report_requested');
        $data["state"] = $this->_em->getRepository('Application_Model_State')->findOneByName('scheduled');
        $data["ressource"] = $report;

        $task = new Application_Model_Task($data);
        return $task;
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
                $this->_helper->FlashMessenger(array('error' => "Sorry, we couldn't find the requested report."));
                $this->_helper->redirector('list', 'report');
            }
        } else {
            $this->_helper->FlashMessenger(array('error' => "Sorry, we couldn't find the requested report."));
            $this->_helper->redirector('list', 'report');
        }
    }

}