<?php

class ReportController extends Unplagged_Controller_Versionable {

    public function init() {
        parent::init();

        $input = new Zend_Filter_Input(array('id' => 'Digits'), null, $this->_getAllParams());

        Zend_Layout::getMvcInstance()->sidebar = 'fragment-tools';
        Zend_Layout::getMvcInstance()->versionableId = $input->id;
    }

    public function indexAction() {
        //$this->_helper->redirector('list', 'report');
    }

    public function createAction() {
        $input = new Zend_Filter_Input(array('page' => 'Digits', 'content' => 'StripTags'), null, $this->_getAllParams());
        //$page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->page);

        $modifyForm = new Application_Form_Report_Modify();


        $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);

        // get current case
        $currentCase = $user->getCurrentCase();
        //$this->_helper->flashMessenger->addMessage( $currentCase);
        // get current case name
        $case = $currentCase->getPublishableName();

        $modifyForm->getElement("case")->setValue($case);

        $formData = $this->_request->getPost();
        //if($this->_request->isPost()){// && empty($input->page)){

        if ($modifyForm->isValid($formData)) {
//            require_once(BASE_PATH . '/library/dompdf/dompdf_config.inc.php');
//            spl_autoload_register('DOMPDF_autoload');
//
//            $casename = $formData['case'];
//            $note = $formData['note'];
//
//            // get files of current case
//            //$files = $currentCase->getFiles();
//            //$files = $user->getFiles();
//            //$documents = array();
//            // foreach($files as $file) {
//            // if( $file->getIsTarget()){
//            // $this->_helper->flashMessenger->addMessage( $file->getId());
//            // $fileId = $file->getId();
//            // $query = $this->_em->createQuery("SELECT d FROM Application_Model_Document d WHERE d.originalFile = $fileId");
//            // //$query = $this->_em->createQuery("SELECT t, a, s FROM Application_Model_Task t JOIN t.action a JOIN t.state s WHERE a.name = :action AND s.name = :state");
//            // $documents = $query->getResult();
//            // }
//            // }
//            // // get documents of target file
//            // foreach($documents as $document){
//            // //$this->_helper->flashMessenger->addMessage( $document->getId());
//            // // get bibtex
//            // $bibTex .= $document->getBibTex();
//            // $this->_helper->flashMessenger->addMessage($bibTex);
//            // // get fragments
//            // //$fragments = $document->getFragments();
//            // }
//
//            $query = $this->_em->createQuery("SELECT f FROM Application_Model_Document_Fragment f");
//            //$query = $this->_em->createQuery("SELECT t, a, s FROM Application_Model_Task t JOIN t.action a JOIN t.state s WHERE a.name = :action AND s.name = :state");
//            $fragments = $query->getResult();
//            $filename = Unplagged_Report::createReport($casename, $note, $fragments);          
//            if ($filename) {
//
//                $this->_helper->flashMessenger->addMessage('The report was created successfully.');
//                //$this->_helper->flashMessenger->addMessage($output);
//                $this->_helper->redirector('list', 'report');
//            }
            //Cron_Document_Page_Reportcreater::start();
            // Create a report_requested task
            $data = array();
            $data["initiator"] = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
            //$data["ressource"] = 198;
            $data["action"] = $this->_em->getRepository('Application_Model_Action')->findOneByName('report_requested');
            $data["state"] = $this->_em->getRepository('Application_Model_State')->findOneByName('task_scheduled');
            $task = new Application_Model_Task($data);
            $this->_em->persist($task);
            $this->_em->flush();

            // Inform the user that the process will be started
            //$this->_helper->flashMessenger->addMessage('The report-generating process has been started.');
            //$this->_helper->redirector('list', 'report');
        }

        $this->view->title = "Create report";
        $this->view->modifyForm = $modifyForm;
        //$this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'report'));
    }

    public function listAction() {
        $input = new Zend_Filter_Input(array('page' => 'Digits'), null, $this->_getAllParams());

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