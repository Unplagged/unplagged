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
 * This controller handles fragment related stuff.
 */
class Document_FragmentController extends Unplagged_Controller_Versionable{

  public function init(){
    parent::init();

    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    Zend_Layout::getMvcInstance()->sidebar = 'fragment-tools';
    Zend_Layout::getMvcInstance()->versionableId = $input->id;
  }

  public function indexAction(){
    
  }

  /**
   * Displays a single fragment by a given id. 
   */
  public function showAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    $fragment = $this->_em->getRepository('Application_Model_Document_Fragment')->findOneById($input->id);

    $this->view->fragment = $fragment;
    $this->view->plag = $fragment->getPlag();
    $this->view->source = $fragment->getSource();

    // @todo remove, jsut for now to have something, it should be changed to explode("\n",...  
    $plagText = $fragment->getPlag()->getText();
    $sourceText = $fragment->getSource()->getText();

    $this->view->plagLines = explode("\n", $plagText);
    $this->view->sourceLines = explode("\n", $sourceText);

    Zend_Layout::getMvcInstance()->sidebar = 'fragment-tools';
    Zend_Layout::getMvcInstance()->versionableId = $input->id;
  }

  /**
   * Handles the creation of a new fragment. 
   */
  public function createAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits', 'content'=>'StripTags'), null, $this->_getAllParams());
    $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->page);

    $modifyForm = new Application_Form_Document_Fragment_Modify();
    if($page){
      $modifyForm->getElement("candidateDocument")->setValue($page->getDocument()->getId());
      foreach($modifyForm->getElement("candidateBibTex")->getDecorators() as $decorator){
        $decorator->setOption('style', 'display: none');
      }
      // remove white spaces of content
      $contentLines = explode("\n", $input->content);
      foreach($contentLines as $i => $contentLine) {
        $contentLines[$i] = trim($contentLine);
      }
      $input->content = implode("\n", $contentLines);
      $modifyForm->getElement("candidateText")->setValue($input->content);
      $modifyForm->getElement("candidatePageFrom")->setValue($page->getPageNumber());
      $modifyForm->getElement("candidatePageTo")->setValue($page->getPageNumber());
    }

    if($this->_request->isPost() && empty($input->page)) {
      $result = $this->handleModifyData($modifyForm);

      if($result){
        // log fragment creation
        $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
        Unplagged_Helper::notify("fragment_created", $result, $user);

        $this->_helper->flashMessenger->addMessage('The fragment was created successfully.');
        $this->_helper->redirector('list', 'document_fragment');
      }
    }

    $this->view->title = "Create fragment";
    $this->view->modifyForm = $modifyForm;
    $this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'document_fragment'));
  }

  /**
   * Handles the edit of an already exisiting fragment. 
   */
  public function editAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

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
	  $htmlTagdeleteLeft = strip_tags($fragment->getPlag()->getText());
      $modifyForm->getElement("candidateText")->setValue($htmlTagdeleteLeft);//$fragment->getPlag()->getText());

      $modifyForm->getElement("sourceDocument")->setValue($fragment->getSource()->getPageFrom()->getDocument()->getId());
      foreach($modifyForm->getElement("sourceBibTex")->getDecorators() as $decorator){
        $decorator->setOption('style', 'display: none');
      }
      $modifyForm->getElement("sourcePageFrom")->setValue($fragment->getSource()->getPageFrom()->getPageNumber());
      $modifyForm->getElement("sourceLineFrom")->setValue($fragment->getSource()->getLineFrom());
      $modifyForm->getElement("sourcePageTo")->setValue($fragment->getSource()->getPageTo()->getPageNumber());
      $modifyForm->getElement("sourceLineTo")->setValue($fragment->getSource()->getLineTo());
	  $htmlTagdeleteRight = strip_tags($fragment->getSource()->getText());
      $modifyForm->getElement("sourceText")->setValue($htmlTagdeleteRight);//$fragment->getSource()->getText());

      $modifyForm->getElement("submit")->setLabel("Save fragment");

      if($this->_request->isPost()){
        $result = $this->handleModifyData($modifyForm, $fragment);

        if($result){
          // log fragment creation
          // log fragment creation
          $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
          Unplagged_Helper::notify("fragment_updated", $result, $user);

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
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());

    $query = $this->_em->createQuery("SELECT f FROM Application_Model_Document_Fragment f");
    $count = $this->_em->createQuery("SELECT COUNT(f.id) FROM Application_Model_Document_Fragment f");

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

    // generate the action dropdown for each fragment
    foreach($paginator as $fragment):
      $fragment->actions = array();

      $action['link'] = '/document_fragment/edit/id/' . $fragment->getId();
      $action['title'] = 'Edit fragment';
      $action['icon'] = 'images/icons/pencil.png';
      $fragment->actions[] = $action;

      $action['link'] = '/document_fragment/delete/id/' . $fragment->getId();
      $action['title'] = 'Remove fragment';
      $action['icon'] = 'images/icons/delete.png';
      $fragment->actions[] = $action;
    endforeach;

    $this->view->paginator = $paginator;

    Zend_Layout::getMvcInstance()->sidebar = null;
    Zend_Layout::getMvcInstance()->versionableId = null;
  }

  /**
   * Compares two version of a fragment. 
   */
  public function changelogAction(){
    parent::changelogAction();

    $this->setTitle("Changelog of fragments");
    Zend_Layout::getMvcInstance()->sidebar = 'fragment-tools';
  }

  public function deleteAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $document = $this->_em->getRepository('Application_Model_Document_Fragment')->findOneById($input->id);
      if($document){
        $this->_em->remove($document);
        $this->_em->flush();
      }else{
        $this->_helper->flashMessenger->addMessage('The fragment does not exist.');
      }
    }

    $this->_helper->flashMessenger->addMessage('The fragment was deleted successfully.');
    $this->_helper->redirector('list', 'document_fragment');

    // disable view
    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
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
      //$data["text"] = $formData['candidateText'];
	  $data["text"] = $formData['hiddenCandidate'];
      $fragment->setPlag(new Application_Model_Document_Fragment_Partial($data));

      // source partial
      unset($data);
      $data = $this->handleDocumentCreation($formData['sourceDocument'], $formData['sourcePageFrom'], $formData['sourcePageTo'], $formData['sourceBibTex']);
      $data["lineFrom"] = $formData['sourceLineFrom'];
      $data["lineTo"] = $formData['sourceLineTo'];
      //$data["text"] = $formData['sourceText'];
	  $data["text"] = $formData['hiddenSource'];
      $fragment->setSource(new Application_Model_Document_Fragment_Partial($data));

      // write back to persistence manager and flush it
      $this->_em->persist($fragment);
      $this->_em->flush();

      return $fragment;
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
      $state = $this->_em->getRepository('Application_Model_State')->findOneByName("parsed");
      $data["document"] = new Application_Model_Document(array("title"=>$title, "bibtex"=>$bibtex, 'state'=> $state));
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
