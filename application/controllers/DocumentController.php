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

    $case = Zend_Registry::getInstance()->user->getCurrentCase();
    if(!$case){
      $errorText = 'You have to select a case, before you can access documents.';
      $this->_helper->FlashMessenger(array('error'=>$errorText));
      $this->redirectToLastPage();
    }
  }

  public function indexAction(){
    $this->_helper->redirector('list', 'document');
  }

  public function createAction(){
    //$permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'create', 'base'=>$document));
    //if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
    //  $this->redirectToLastPage(true);
    //}

    $modifyForm = new Application_Form_Document_Modify();

    if($this->_request->isPost()){
      $result = $this->handleModifyData($modifyForm);

      if($result){
        $state = $this->_em->getRepository('Application_Model_State')->findOneByName('parsed');
        $result->setState($state);

        $case = Zend_Registry::getInstance()->user->getCurrentCase();
        $result->setCase($case);

        $this->_em->persist($result);
        $this->_em->flush();

        // notification
        Unplagged_Helper::notify('document_created', $result, Zend_Registry::getInstance()->user);

        $this->_helper->FlashMessenger(array('success'=>'The document was created successfully.'));
        $this->_helper->redirector('list', 'document');
      }
    }

    $this->view->title = "Create document";
    $this->view->modifyForm = $modifyForm;
    $this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'document'));
  }

  public function editAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    $document = $this->_em->getRepository('Application_Model_Document')->findOneById($input->id);

    if($document){
      $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$document));
      if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
        $this->redirectToLastPage(true);
      }

      Zend_Layout::getMvcInstance()->menu = $document->getSidebarActions();

      $modifyForm = new Application_Form_Document_Modify();
      $modifyForm->setAction("/document/edit/id/" . $input->id);

      $modifyForm->getElement("title")->setValue($document->getTitle());

      // set bibTex information
      $bibTex = $document->getBibTex();
      if(!$bibTex){
        $bibTex = new Application_Model_BibTex();
        $document->setBibTex($bibTex);
      }
      $modifyForm->getElement("bibSourceType")->setValue($bibTex->getSourceType());

      foreach(Application_Model_BibTex::$accessibleFields as $fieldName=>$field){
        $modifyForm->getElement('bib' . ucfirst($fieldName))->setValue($bibTex->getContent($fieldName));
      }

      $modifyForm->getElement("submit")->setLabel("Save document");

      if($this->_request->isPost()){
        $result = $this->handleModifyData($modifyForm, $document);

        if($result){
          // log notification
          Unplagged_Helper::notify("document_updated", $result, Zend_Registry::getInstance()->user);

          $this->_helper->FlashMessenger(array('success'=>'The document was updated successfully.'));
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
    $case = Zend_Registry::getInstance()->user->getCurrentCase();

    $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'read', 'base'=>null));
    $query = 'SELECT b FROM Application_Model_Document b';
    $count = 'SELECT COUNT(b.id) FROM Application_Model_Document b';

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count, array('b.case'=>$case->getId()), null, $permission));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

// generate the action dropdown for each file
    foreach($paginator as $document):
      if($document->getState()->getName() == 'scheduled'){
// find the associated task and get percentage
        $state = $this->_em->getRepository('Application_Model_State')->findOneByName('running');
        $task = $this->_em->getRepository('Application_Model_Task')->findOneBy(array('ressource'=>$document->getId(), 'state'=>$state));
        if(!$task){
          $percentage = 0;
        }else{
          $percentage = $task->getProgressPercentage();
        }
        $document->outputState = '<div class="progress"><div class="bar" style="width: ' . $percentage . '%;"></div></div>';
      }else{
        $document->outputState = $document->getState()->getTitle();
      }

      $document->actions = array();
      $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$document));
      if(Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
        $action['link'] = '/document/edit/id/' . $document->getId();
        $action['label'] = 'Edit document';
        $action['icon'] = 'images/icons/pencil.png';
        $document->actions[] = $action;
      }

      $action['link'] = '/document/detect-plagiarism/id/' . $document->getId();
      $action['label'] = 'Detect plagiarism';
      $action['icon'] = 'images/icons/eye.png';
      $document->actions[] = $action;
      $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'delete', 'base'=>$document));

      if(Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
        $action['link'] = '/document/delete/id/' . $document->getId();
        $action['label'] = 'Delete document';
        $action['icon'] = 'images/icons/delete.png';
        $document->actions[] = $action;
      }
      $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'case', 'action'=>'update', 'base'=>$case));
      if(Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
        if($case->getTarget() && $case->getTarget()->getId() == $document->getId()){
          $action['link'] = '/document/unset-target/id/' . $document->getId();
          $action['label'] = 'Unset target';
          $action['icon'] = 'images/icons/page_find.png';
          $document->actions[] = $action;
          $document->isTarget = true;
        }else{
          $action['link'] = '/document/set-target/id/' . $document->getId();
          $action['label'] = 'Set target';
          $action['icon'] = 'images/icons/page.png';
          $document->actions[] = $action;
          $document->isTarget = false;
        }
      }
      $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'authorize', 'base'=>$document));
      if(Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
        $action['link'] = '/permission/edit/id/' . $document->getId();
        $action['label'] = 'Set permissions';
        $action['icon'] = 'images/icons/shield.png';
        $document->actions[] = $action;
      }
    endforeach;

    $this->view->paginator = $paginator;
  }

  /**
   * Removes a single document by id.
   */
  public function deleteAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $document = $this->_em->getRepository('Application_Model_Document')->findOneById($input->id);
      if($document){
        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'delete', 'base'=>$document));
        if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
          $this->redirectToLastPage(true);
        }

        Unplagged_Helper::notify('document_removed', $document, Zend_Registry::getInstance()->user);

        $document->remove();
        $this->_em->persist($document);
        $this->_em->flush();
      }else{
        $this->_helper->FlashMessenger('The document does not exist.');
      }
    }

    $this->_helper->FlashMessenger(array('success'=>'The document was deleted successfully.'));
    $this->_helper->redirector('list', 'document');

// disable view
    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

  /**
   * Initialises an automated plagiarism detection.
   */
  public function detectPlagiarismAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id) && $this->_defaultNamespace->userId){
      $document = $this->_em->getRepository('Application_Model_Document')->findOneById($input->id);
      if($document){
        Zend_Layout::getMvcInstance()->menu = $document->getSidebarActions();

        $pages = $document->getPages();

        $successPages = array();
        $errorPages = array();
        foreach($pages as $page){
          $detector = Unplagged_Detector::factory();

          $data["user"] = Zend_Registry::getInstance()->user;

          $data["page"] = $page;
          $data["state"] = $this->_em->getRepository('Application_Model_State')->findOneByName("running");
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
          $this->_helper->FlashMessenger(array('success'=>sprintf($successText, $successPagesStr)));
        }
        if(!empty($errorPagesStr)){
          $this->_helper->FlashMessenger(array('error'=>sprintf($errorText, $errorPagesStr)));
        }
      }else{
        $this->_helper->FlashMessenger('Dcument does not exist.');
      }
    }else{
      $this->_helper->FlashMessenger('No document selected.');
    }
    $this->_helper->redirector('list', 'document');
  }

  /**
   * Initializes an automated plagiarism detection.
   */
  public function responsePlagiarismAction(){
    $input = new Zend_Filter_Input(array('detector'=>'Alnum', 'report'=>'Alnum', 'result'=>'Alnum', 'status'=>'Alnum'), null, $this->_getAllParams());

    $detector = Unplagged_Detector::factory($input->detector);
    $report = $detector->handleResult(array('report'=>$input->report, 'result'=>$input->result, 'status'=>$input->status));
    if($report){
      $this->_em->persist($report);
      $this->_em->flush();

      // create notification
      Unplagged_Helper::notify("detection_report_created", $report, $report->getUser());
    }
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
      $bibTex = $document->getBibTex();
      if(!$bibTex){
        $bibTex = new Application_Model_BibTex();
      }

      $bibTex->setSourceType($formData['bibSourceType']);
      foreach(Application_Model_BibTex::$accessibleFields as $fieldName=>$field){
        $fieldId = 'bib' . ucfirst($fieldName);
        $bibTex->setContent($formData[$fieldId], $fieldName);
      }

      $document->setBibTex($bibTex);

      // write back to persistence manager and flush it
      $this->_em->persist($document);
      $this->_em->flush();

      return $document;
    }

    return false;
  }

  /**
   * Returns all pages in the document .
   */
  public function readAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $document = $this->_em->getRepository('Application_Model_Document')->findOneById($input->id);
      if($document){
        $response["statuscode"] = 200;
        $response["data"] = $document->toArray();
      }else{
        $response["statuscode"] = 404;
        $response["statusmessage"] = "No document by that id found.";
      }
    }else{
      $response["statuscode"] = 405;
      $response["statusmessage"] = "Required parameter id is missing.";
    }

    $this->getResponse()->appendBody(json_encode($response));

// disable view
    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

  public function setTargetAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    $this->targetAction($input->id, true);
  }

  public function unsetTargetAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    $this->targetAction($input->id, false);
  }

  /**
   * Handles setting and unsetting the target of the current case.
   * @param Integer $fileId
   * @param Boolean $isTarget
   */
  private function targetAction($documentId, $isTarget){
    $case = Zend_Registry::getInstance()->user->getCurrentCase();

    if(!empty($documentId)){
      $document = null;
      if($isTarget){
        $document = $this->_em->getRepository('Application_Model_Document')->findOneById($documentId);
      }
      $case->setTarget($document);

      $this->_em->persist($case);
      $this->_em->flush();
    }else{
      $this->_helper->FlashMessenger('No document found.');
    }


    $this->_helper->redirector('list', 'document');

// disable view
    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

}

?>
