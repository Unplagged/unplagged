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
class DocumentController extends Unplagged_Controller_Action{

  public function init(){
    parent::init();

    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    Zend_Layout::getMvcInstance()->sidebar = 'document-tools';
    Zend_Layout::getMvcInstance()->versionableId = $input->id;
  }

  public function indexAction(){
    $this->_helper->redirector('list', 'document');
  }

  public function editAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

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
          $params = array('id'=>$document->getId());
          $this->_helper->redirector('list', 'document_page', '', $params);
        }
      }

      $this->view->title = "Edit document";
      $this->view->modifyForm = $modifyForm;
      $this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'document'));
    }else{
      $this->_helper->redirector('list', 'document');
    }
  }

  /**
   * Lists all documents. 
   */
  public function listAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());

    $query = $this->_em->createQuery("SELECT d FROM Application_Model_Document d");
    $count = $this->_em->createQuery("SELECT COUNT(d.id) FROM Application_Model_Document d");

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

    $this->view->paginator = $paginator;

    Zend_Layout::getMvcInstance()->sidebar = null;
    Zend_Layout::getMvcInstance()->versionableId = null;
  }

  /**
   * Removes a single document by id. 
   */
  public function deleteAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $document = $this->_em->getRepository('Application_Model_Document')->findOneById($input->id);
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
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id) && $this->_defaultNamespace->userId){
      $document = $this->_em->getRepository('Application_Model_Document')->findOneById($input->id);
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
    $input = new Zend_Filter_Input(array('detector'=>'Alpha'), null, $this->_getAllParams());

    $detector = Unplagged_Detector::factory($$input->detector);
    $report = $detector->handleResult($input);

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
