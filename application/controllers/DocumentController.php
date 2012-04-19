<?php

/**
 * Unplagged - The plagiarism detection cockpit.
 * Copyright (C) 2012 Unplagged
 *  
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *  
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of DocumentController
 *
 * @author benjamin
 */
class DocumentController extends Zend_Controller_Action{

  public function init(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
    $this->_defaultNamespace = new Zend_Session_Namespace('Default');
    $this->view->flashMessages = $this->_helper->flashMessenger->getMessages();
  }

  public function indexAction(){
    $this->_helper->redirector('list', 'document');
  }

  public function editAction(){
    $input = new Zend_Filter_Input(array('id'=>'digits'), null, $this->_getAllParams());

    $document = $this->_em->getRepository('Application_Model_Document')->findOneById($input->id);

    if($document){
      $modifyForm = new Application_Form_Document_Modify();
      $modifyForm->setAction("/document/edit/id/" . $input->id);

      $modifyForm->getElement("title")->setValue($document->getTitle());
      $modifyForm->getElement("submit")->setLabel("Save document");

      if($this->_request->isPost()){
        $result = $this->handleModifyData($modifyForm, $document);

        if($result){
          $this->_helper->flashMessenger->addMessage('The document was updated successfully.');
          $this->_helper->redirector('list', 'document');
        }
      }

      $this->view->title = "Edit case";
      $this->view->modifyForm = $modifyForm;
      $this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'case'));
    }else{
      $this->_helper->redirector('list', 'document');
    }
  }

  public function listAction(){
    // @todo: clean input
    $page = $this->_getParam('page');

    $query = $this->_em->createQuery("SELECT d FROM Application_Model_Document d");
    $count = $this->_em->createQuery("SELECT COUNT(d.id) FROM Application_Model_Document d");

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($page);

    $this->view->paginator = $paginator;
  }

  public function deleteAction(){
    $documentId = $this->_getParam('id');

    if(!empty($documentId)){
      $documentId = preg_replace('/[^0-9]/', '', $documentId);
      $document = $this->_em->getRepository('Application_Model_Document')->findOneById($documentId);
      if($document){
        $this->_em->remove($document);
        $this->_em->flush();
      }else{
        $this->_helper->flashMessenger->addMessage('The document does not exist.');
      }
    }

    $this->_helper->flashMessenger->addMessage('The document was deleted successfully.');
    $this->_helper->redirector('list', 'document');

    // disable view
    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

  /**
   * Initializes an automated plagiarism detection.
   */
  public function detectPlagiarismAction(){
    $documentId = $this->_getParam('id');

    if(!empty($documentId) && $this->_defaultNamespace->userId){
      $documentId = preg_replace('/[^0-9]/', '', $documentId);
      $document = $this->_em->getRepository('Application_Model_Document')->findOneById($documentId);
      if($document){
        $pages = $document->getPages();

        $successPages = array();
        $errorPages = array();
        foreach($pages as $page){
          $detector = Unplagged_Detector::factory();

          $data["user"] = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
          ;
          $data["page"] = $page;
          $data["state"] = "running";
          $data["servicename"] = $detector->getServiceName();

          $report = new Application_Model_Document_Page_DetectionReport($data);
          $this->_em->persist($report);
          $this->_em->flush();

          $started = $detector->detect($report);
          if($started){
            $successPages[] = $page->getPageNumber();
          }else{
            $errorPages[] = $page->getPageNumber();

            $this->_em->remove($report);
            $this->_em->flush();
          }
        }

        $successPagesStr = implode(", ", $successPages);
        $errorPagesStr = implode(", ", $errorPages);

        $successText = 'The detection was started successfully for the following pages: %s, you will be notified, when it is finished.';
        $errorText = 'The detection could not be started for the following pages: %s, please try again later.';

        if(!empty($successPagesStr)){
          $this->_helper->flashMessenger->addMessage(sprintf($successText, $successPagesStr));
        }
        if(!empty($errorPagesStr)){
          $this->_helper->flashMessenger->addMessage(sprintf($errorText, $errorPagesStr));
        }
      }else{
        $this->_helper->flashMessenger->addMessage("Dcument does not exist.");
      }
    }else{
      $this->_helper->flashMessenger->addMessage("No document selected.");
    }
    $this->_helper->redirector('list', 'document');
  }

  /**
   * Initializes an automated plagiarism detection.
   */
  public function responsePlagiarismAction(){
    $params = $this->getRequest()->getParams();
    $detector = Unplagged_Detector::factory($params["detector"]);
    $report = $detector->handleResult($params);

    $this->_em->persist($report);
    $this->_em->flush();

    // send registration mail
    Unplagged_Mailer::sendDetectionReportAvailable($report);

    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

  private function handleModifyData(Application_Form_Document_Modify $modifyForm, Application_Model_Document $document = null){
    if(!($document)){
      $document = new Application_Model_Document();
    }

    $formData = $this->_request->getPost();
    if($modifyForm->isValid($formData)){

      $document->setTitle($formData['title']);

      // write back to persistence manager and flush it
      $this->_em->persist($document);
      $this->_em->flush();

      /*      // notification @todo: add notification
        $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
        Unplagged_Helper::notify("case_created", $case, $user);
       */
      return true;
    }
    
    return false;
  }

}

?>
