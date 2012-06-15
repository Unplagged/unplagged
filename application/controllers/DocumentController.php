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

    Zend_Layout::getMvcInstance()->menu = 'document-tools';
    Zend_Layout::getMvcInstance()->versionableId = $input->id;
  }

  public function indexAction(){
    $this->_helper->redirector('list', 'document');
  }

  public function editAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    $document = $this->_em->getRepository('Application_Model_Document')->findOneById($input->id);

    if($document){
      if(!Zend_Registry::getInstance()->user->hasPermission(new Application_Model_Permission('document', 'update', $input->id))){
        $this->redirectToLastPage(true);
      }

      $modifyForm = new Application_Form_Document_Modify();
      $modifyForm->setAction("/document/edit/id/" . $input->id);

      $modifyForm->getElement("title")->setValue($document->getTitle());

      // set bibTex information
      $bibTex = $document->getBibTex();
      //var_dump($bibTex);
      $modifyForm->getElement("type")->setValue($bibTex['form']);
      $modifyForm->getElement("kuerzel")->setValue($bibTex['kuerzel']);
      $modifyForm->getElement("autor")->setValue($bibTex['autor']);
      $modifyForm->getElement("titel")->setValue($bibTex['titel']);
      //$modifyForm->getElement("titel")->setAttrib('disabled','disabled');
      $modifyForm->getElement("zeitschrift")->setValue($bibTex['zeitschrift']);
      $modifyForm->getElement("sammlung")->setValue($bibTex['sammlung']);
      $modifyForm->getElement("hrsg")->setValue($bibTex['hrsg']);
      $modifyForm->getElement("beteiligte")->setValue($bibTex['beteiligte']);
      $modifyForm->getElement("ort")->setValue($bibTex['ort']);
      $modifyForm->getElement("verlag")->setValue($bibTex['verlag']);
      $modifyForm->getElement("ausgabe")->setValue($bibTex['ausgabe']);
      $modifyForm->getElement("jahr")->setValue($bibTex['jahr']);
      $modifyForm->getElement("monat")->setValue($bibTex['monat']);
      $modifyForm->getElement("tag")->setValue($bibTex['tag']);
      $modifyForm->getElement("nummer")->setValue($bibTex['nummer']);
      $modifyForm->getElement("seiten")->setValue($bibTex['seiten']);
      $modifyForm->getElement("umfang")->setValue($bibTex['umfang']);
      $modifyForm->getElement("reihe")->setValue($bibTex['reihe']);
      $modifyForm->getElement("anmerkung")->setValue($bibTex['anmerkung']);
      $modifyForm->getElement("isbn")->setValue($bibTex['isbn']);
      $modifyForm->getElement("issn")->setValue($bibTex['issn']);
      $modifyForm->getElement("doi")->setValue($bibTex['doi']);
      $modifyForm->getElement("url")->setValue($bibTex['url']);
      $modifyForm->getElement("urn")->setValue($bibTex['urn']);
      $modifyForm->getElement("wp")->setValue($bibTex['wp']);
      $modifyForm->getElement("inlit")->setValue($bibTex['inlit']);
      $modifyForm->getElement("infn")->setValue($bibTex['infn']);
      $modifyForm->getElement("schluessel")->setValue($bibTex['schluessel']);

      $modifyForm->getElement("submit")->setLabel("Save document");

      if($this->_request->isPost()){
        $result = $this->handleModifyData($modifyForm, $document);

        if($result){
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

    if($case){
      $permissionAction = 'read';
      $query = 'SELECT b FROM Application_Model_Document b';
      $count = 'SELECT COUNT(b.id) FROM Application_Model_Document b';

      $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count, array('b.case'=>$case->getId()), null, $permissionAction));
      $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
      $paginator->setCurrentPageNumber($input->page);

      // generate the action dropdown for each file
      foreach($paginator as $document):
        if($document->getState()->getName() == 'task_scheduled'){
          // find the associated task and get percentage
          $state = $this->_em->getRepository('Application_Model_State')->findOneByName('task_running');
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
        if(Zend_Registry::getInstance()->user->hasPermission(new Application_Model_Permission('document', 'update', $document->getId()))){
          $action['link'] = '/document/edit/id/' . $document->getId();
          $action['label'] = 'Edit document';
          $action['icon'] = 'images/icons/pencil.png';
          $document->actions[] = $action;
        }
        
        $action['link'] = '/document/detect-plagiarism/id/' . $document->getId();
        $action['label'] = 'Detect plagiarism';
        $action['icon'] = 'images/icons/eye.png';
        $document->actions[] = $action;

        if(Zend_Registry::getInstance()->user->hasPermission(new Application_Model_Permission('document', 'delete', $document->getId()))){
          $action['link'] = '/document/delete/id/' . $document->getId();
          $action['label'] = 'Delete document';
          $action['icon'] = 'images/icons/delete.png';
          $document->actions[] = $action;
        }
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
        if(Zend_Registry::getInstance()->user->hasPermission(new Application_Model_Permission('document', 'authorize', $document->getId()))){
          $action['link'] = '/permission/edit/id/' . $document->getId();
          $action['label'] = 'Set permissions';
          $action['icon'] = 'images/icons/shield.png';
          $document->actions[] = $action;
        }
      endforeach;

      $this->view->paginator = $paginator;

      Zend_Layout::getMvcInstance()->sidebar = null;
      Zend_Layout::getMvcInstance()->versionableId = null;
    }else{
      $this->_helper->FlashMessenger('You need to select a case first.');
    }
  }

  /**
   * Removes a single document by id.
   */
  public function deleteAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $document = $this->_em->getRepository('Application_Model_Document')->findOneById($input->id);
      if($document){
        if(!Zend_Registry::getInstance()->user->hasPermission(new Application_Model_Permission('document', 'delete', $input->id))){
          $this->redirectToLastPage(true);
        }
        $this->_em->remove($document);
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
        $pages = $document->getPages();

        $successPages = array();
        $errorPages = array();
        foreach($pages as $page){
          $detector = Unplagged_Detector::factory();

          $data["user"] = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);

          $data["page"] = $page;
          $data["state"] = $this->_em->getRepository('Application_Model_State')->findOneByName("report_running");
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
      $document->setBibTexForm($formData['type']);

      // save bibtex information
      $document->setBibTexKuerzel($formData['kuerzel']);
      $document->setBibTexAutor($formData['autor']);
      $document->setBibTexTitel($formData['titel']);
      $document->setBibTexZeitschrift($formData['zeitschrift']);
      $document->setBibTexSammlung($formData['sammlung']);
      $document->setBibTexHrsg($formData['hrsg']);
      $document->setBibTexBeteiligte($formData['beteiligte']);
      $document->setBibTexOrt($formData['ort']);
      $document->setBibTexVerlag($formData['verlag']);
      $document->setBibTexAusgabe($formData['ausgabe']);
      $document->setBibTexJahr($formData['jahr']);
      $document->setBibTexMonat($formData['monat']);
      $document->setBibTexTag($formData['tag']);
      $document->setBibTexNummer($formData['nummer']);
      $document->setBibTexSeiten($formData['seiten']);
      $document->setBibTexUmfang($formData['umfang']);
      $document->setBibTexReihe($formData['reihe']);
      $document->setBibTexAnmerkung($formData['anmerkung']);
      $document->setBibTexIsbn($formData['isbn']);
      $document->setBibTexIssn($formData['issn']);
      $document->setBibTexDoi($formData['doi']);
      $document->setBibTexUrl($formData['url']);
      $document->setBibTexUrn($formData['urn']);
      $document->setBibTexWp($formData['wp']);
      $document->setBibTexInlit($formData['inlit']);
      $document->setBibTexInfn($formData['infn']);
      $document->setBibTexSchluessel($formData['schluessel']);

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
