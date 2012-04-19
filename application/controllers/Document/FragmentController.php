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
 * Description of Document_FragmentController
 */
class Document_FragmentController extends Zend_Controller_Action{

  public function init(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
    $this->_defaultNamespace = new Zend_Session_Namespace('Default');
    $this->view->flashMessages = $this->_helper->flashMessenger->getMessages();
  }

  public function indexAction(){
    
  }

  public function showAction(){
    $input = new Zend_Filter_Input(array('id'=>'digits'), null, $this->_getAllParams());

    $fragment = $this->_em->getRepository('Application_Model_Document_Fragment')->findOneById($input->id);

    $this->view->fragment = $fragment;
    $this->view->plag = $fragment->getPlag();
    $this->view->source = $fragment->getSource();

    // @todo remove, jsut for now to have something, it should be changed to explode("\n",...          
    $this->view->plagLines = str_split(json_encode($fragment->getPlag()->getText()), 20);
    $this->view->sourceLines = str_split(json_encode($fragment->getSource()->getText()), 20);

    Zend_Layout::getMvcInstance()->sidebar = 'fragment-tools';
    Zend_Layout::getMvcInstance()->versionableId = $input->id;
  }

  public function createAction(){
    $modifyForm = new Application_Form_Document_Fragment_Modify();

    if($this->_request->isPost()){
      $result = $this->handleModifyData($modifyForm);

      if($result){
        // log fragment creation
        $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
        //Unplagged_Helper::notify("fragment_created", $fragment, $user);

        $this->_helper->flashMessenger->addMessage('The fragment was created successfully.');
        $this->_helper->redirector('list', 'document_fragment');
      }
    }

    $this->view->title = "Create fragment";
    $this->view->modifyForm = $modifyForm;
    $this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'document_fragment'));
  }

  public function editAction(){
    $input = new Zend_Filter_Input(array('id'=>'digits'), null, $this->_getAllParams());

    $fragment = $this->_em->getRepository('Application_Model_Document_Fragment')->findOneById($input->id);

    if($fragment){
      $modifyForm = new Application_Form_Document_Fragment_Modify();
      $modifyForm->setAction("/document_fragment/edit/id/" . $input->id);

      $modifyForm->getElement("type")->setValue($fragment->getType()->getId());
      $modifyForm->getElement("note")->setValue($fragment->getNote());

      $modifyForm->getElement("candidateDocument")->setValue($fragment->getPlag()->getPageFrom()->getDocument()->getId());
      foreach($modifyForm->getElement("candidateBibTex")->getDecorators() as $decorator){
        $decorator->setOption('style', 'display: none');
      }
      $modifyForm->getElement("candidatePageFrom")->setValue($fragment->getPlag()->getPageFrom()->getPageNumber());
      $modifyForm->getElement("candidateLineFrom")->setValue($fragment->getPlag()->getLineFrom());
      $modifyForm->getElement("candidatePageTo")->setValue($fragment->getPlag()->getPageTo()->getPageNumber());
      $modifyForm->getElement("candidateLineTo")->setValue($fragment->getPlag()->getLineTo());
      $modifyForm->getElement("candidateText")->setValue($fragment->getPlag()->getText());

      $modifyForm->getElement("sourceDocument")->setValue($fragment->getSource()->getPageFrom()->getDocument()->getId());
      foreach($modifyForm->getElement("sourceBibTex")->getDecorators() as $decorator){
        $decorator->setOption('style', 'display: none');
      }
      $modifyForm->getElement("sourcePageFrom")->setValue($fragment->getSource()->getPageFrom()->getPageNumber());
      $modifyForm->getElement("sourceLineFrom")->setValue($fragment->getSource()->getLineFrom());
      $modifyForm->getElement("sourcePageTo")->setValue($fragment->getSource()->getPageTo()->getPageNumber());
      $modifyForm->getElement("sourceLineTo")->setValue($fragment->getSource()->getLineTo());
      $modifyForm->getElement("sourceText")->setValue($fragment->getSource()->getText());

      $modifyForm->getElement("submit")->setLabel("Save fragment");

      if($this->_request->isPost()){
        $result = $this->handleModifyData($modifyForm, $fragment);

        if($result){
          // log fragment creation
          $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
          //Unplagged_Helper::notify("fragment_created", $fragment, $user);

          $this->_helper->flashMessenger->addMessage('The fragment was updated successfully.');
          $this->_helper->redirector('list', 'document_fragment');
        }
      }

      $this->view->title = "Edit fragment";
      $this->view->modifyForm = $modifyForm;
      $this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'document_fragment'));
    }else{
      $this->_helper->redirector('list', 'document_fragment');
    }
  }

  public function listAction(){
    // @todo: clean input
    $page = $this->_getParam('page');

    $query = $this->_em->createQuery("SELECT f FROM Application_Model_Document_Fragment f");
    $count = $this->_em->createQuery("SELECT COUNT(f.id) FROM Application_Model_Document_Fragment f");

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($page);

    $this->view->paginator = $paginator;
  }

  public function diffAction(){
    // @todo: clean input
    $versionableId = $this->_getParam('id');

    $query = $this->_em->createQuery("SELECT v FROM Application_Model_Versionable_Version v WHERE v.versionable = :versionable");
    $query->setParameter("versionable", $versionableId);
    $versions = $query->getResult();

    $params["versions"] = array();
    foreach($versions as $version){
      $params["versions"][$version->getId()] = "Version " . $version->getVersion();
    }
    $params["action"] = "/document_fragment/diff/id/" . $versionableId;

    // create the form
    $diffVersionsForm = new Application_Form_Versionable_Diff($params);

    // form has been submitted through post request
    if($this->_request->isPost()){
      $formData = $this->_request->getPost();

      // if the form doesn't validate, pass to view and return
      if($diffVersionsForm->isValid($formData)){
        $firstVersionId = $this->getRequest()->getParam('firstVersion');
        $secondVersionId = $this->getRequest()->getParam('secondVersion');

        $firstVersion = $this->_em->getRepository('Application_Model_Versionable_Version')->findOneById($firstVersionId);
        $secondVersion = $this->_em->getRepository('Application_Model_Versionable_Version')->findOneById($secondVersionId);

        // @todo, just to have some data for now
        $firstData = $firstVersion->getData();
        $secondData = $secondVersion->getData();

        if(!empty($firstData) && !empty($secondData)){
          // @todo remove, jsut for now to have something
          $a = str_split(json_encode($firstData), 20);
          $b = str_split(json_encode($secondData), 20);

          // options for generating the diff
          $options = array(
            'ignoreWhitespace'=>true,
            'ignoreCase'=>true,
            'context'=>1000
          );

          $diff = new Diff($a, $b, $options);
          $renderer = new Diff_Renderer_Html_Array();
        }

        if(!empty($diff)){
          $this->view->diff = Unplagged_Helper::formatDiff($diff->Render($renderer), $firstVersion->getVersion(), $secondVersion->getVersion());
        }
      }
    }

    $this->view->diffVersionsForm = $diffVersionsForm;
    Zend_Layout::getMvcInstance()->sidebar = 'fragment-tools';
  }

  private function handleModifyData(Application_Form_Document_Fragment_Modify $modifyForm, Application_Model_Document_Fragment $fragment = null){
    if(!($fragment)){
      $fragment = new Application_Model_Document_Fragment();
    }

    $formData = $this->_request->getPost();
    if($modifyForm->isValid($formData)){

      $fragment->setNote($formData['note']);
      $fragment->setType($this->_em->getRepository('Application_Model_Document_Fragment_Type')->findOneById($formData['type']));

      // candidate partial
      $data = $this->handleDocumentCreation($formData['candidateDocument'], $formData['candidatePageFrom'], $formData['candidatePageTo'], $formData['candidateBibTex']);
      $data["lineFrom"] = $formData['candidateLineFrom'];
      $data["lineTo"] = $formData['candidateLineTo'];
      $data["text"] = $formData['candidateText'];
      $fragment->setPlag(new Application_Model_Document_Fragment_Partial($data));

      // source partial
      unset($data);
      $data = $this->handleDocumentCreation($formData['sourceDocument'], $formData['sourcePageFrom'], $formData['sourcePageTo'], $formData['sourceBibTex']);
      $data["lineFrom"] = $formData['sourceLineFrom'];
      $data["lineTo"] = $formData['sourceLineTo'];
      $data["text"] = $formData['sourceText'];
      $fragment->setSource(new Application_Model_Document_Fragment_Partial($data));

      // write back to persistence manager and flush it
      $this->_em->persist($fragment);
      $this->_em->flush();

      return true;
    }else{
      foreach($modifyForm->getElement("candidateBibTex")->getDecorators() as $decorator){
        $display = $formData['candidateDocument'] == "new" ? "block" : "none";
        $decorator->setOption('style', 'display: ' . $display);
      }
      foreach($modifyForm->getElement("sourceBibTex")->getDecorators() as $decorator){
        $display = $formData['sourceDocument'] == "new" ? "block" : "none";
        $decorator->setOption('style', 'display: ' . $display);
      }
    }

    return false;
  }

  private function handleDocumentCreation($documentId, $pageFrom, $pageTo, $bibtex){
    $data = array();

    if($documentId == "new"){
      $title = "Document " . time();
      $data["document"] = new Application_Model_Document(array("title"=>$title, "bibtex"=>$bibtex));
      $this->_em->persist($data["document"]);

      $data["pageFrom"] = new Application_Model_Document_Page(array("pageNumber"=>$pageFrom));
      $data["pageTo"] = new Application_Model_Document_Page(array("pageNumber"=>$pageTo));
      $this->_em->persist($data["pageFrom"]);
      $this->_em->persist($data["pageTo"]);

      $data["document"]->addPage($data["pageFrom"]);
      $data["document"]->addPage($data["pageTo"]);
    }else{
      $data["document"] = $this->_em->getRepository('Application_Model_Document')->findOneById($documentId);
      $data["pageFrom"] = $this->_em->getRepository('Application_Model_Document_Page')->findOneBy(array("document"=>$documentId, "pageNumber"=>$pageFrom));
      $data["pageTo"] = $this->_em->getRepository('Application_Model_Document_Page')->findOneBy(array("document"=>$documentId, "pageNumber"=>$pageFrom));
    }

    return $data;
  }

}

?>
