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
    $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);

    $this->view->fragment = $fragment;
    $this->view->plag = $fragment->getPlag();
    $this->view->source = $fragment->getSource();

    $this->view->content = $fragment->getContent('list', true);

    $this->view->ratings = $this->_em->getRepository("Application_Model_Rating")->findBySource($input->id);

    $this->view->meId = $this->_defaultNamespace->userId;
    
    // check if the current user already rated this fragment
    $this->view->fragmentIsRated = $fragment->isRatedByUser($user);
        
    
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
      foreach($contentLines as $i=>$contentLine){
        $contentLines[$i] = trim($contentLine);
      }
      $input->content = implode("\n", $contentLines);
      $modifyForm->getElement("candidateText")->setValue($input->content);
      $modifyForm->getElement("candidatePageFrom")->setValue($page->getPageNumber());
      $modifyForm->getElement("candidatePageTo")->setValue($page->getPageNumber());
    }

    if($this->_request->isPost() && empty($input->page)){
      $result = $this->handleModifyData($modifyForm);

      if($result){
        // log fragment creation
        $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
        Unplagged_Helper::notify("fragment_created", $result, $user);

        $this->_helper->FlashMessenger('The fragment was created successfully.');
        $params = array('id'=>$result->getId());
        $this->_helper->redirector('show', 'document_fragment', '', $params);
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

      $modifyForm->getElement("submit")->setLabel("Save fragment");

      if($this->_request->isPost()){
        $result = $this->handleModifyData($modifyForm, $fragment);

        if($result){
          // log fragment creation
          $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
          Unplagged_Helper::notify("fragment_updated", $result, $user);

          $this->_helper->FlashMessenger('The fragment was updated successfully.');
          $params = array('id'=>$fragment->getId());
          $this->_helper->redirector('show', 'document_fragment', '', $params);
        }
      }else{
        $formData['candidateDocument'] = $fragment->getPlag()->getLineFrom()->getPage()->getDocument()->getId();
        $formData['candidatePageFrom'] = $fragment->getPlag()->getLineFrom()->getPage()->getId();
        $formData['candidatePageTo'] = $fragment->getPlag()->getLineTo()->getPage()->getId();
        $formData['candidateLineFrom'] = $fragment->getPlag()->getLineFrom()->getId();
        $formData['candidateLineTo'] = $fragment->getPlag()->getLineTo()->getId();
        $this->initalisePartial($modifyForm, 'candidate', $formData);

        $formData['sourceDocument'] = $fragment->getSource()->getLineFrom()->getPage()->getDocument()->getId();
        $formData['sourcePageFrom'] = $fragment->getSource()->getLineFrom()->getPage()->getId();
        $formData['sourcePageTo'] = $fragment->getSource()->getLineTo()->getPage()->getId();
        $formData['sourceLineFrom'] = $fragment->getSource()->getLineFrom()->getId();
        $formData['sourceLineTo'] = $fragment->getSource()->getLineTo()->getId();
        $this->initalisePartial($modifyForm, 'source', $formData);
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
      $action['label'] = 'Edit fragment';
      $action['icon'] = 'images/icons/pencil.png';
      $fragment->actions[] = $action;

      $action['link'] = '/document_fragment/delete/id/' . $fragment->getId();
      $action['label'] = 'Remove fragment';
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
        $this->_helper->FlashMessenger('The fragment does not exist.');
      }
    }

    $this->_helper->FlashMessenger('The fragment was deleted successfully.');
    $this->_helper->redirector('list', 'document_fragment');

    // disable view
    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

  public function rateAction(){
    $input = new Zend_Filter_Input(array('source'=>'Digits', 'id'=>'Digits'), null, $this->_getAllParams());

    $params = array('redirect'=>'document_fragment/show/id/' . $input->source);

    if($input->id){
      $this->view->title = "Edit fragment rating";
      $params['id'] = $input->id;
      $this->_forward('edit', 'rating', '', $params);
    }else{
      $this->view->title = "Rate fragment";
      $this->_forward('create', 'rating', '', $params);
    }
  }

  private function handleModifyData(Application_Form_Document_Fragment_Modify $modifyForm, Application_Model_Document_Fragment $fragment = null){
    if(!($fragment)){
      $fragment = new Application_Model_Document_Fragment();
    }

    $formData = $this->_request->getPost();

    $this->initalisePartial($modifyForm, 'candidate', $formData);
    $this->initalisePartial($modifyForm, 'source', $formData);

    if($modifyForm->isValid($formData)){

      $fragment->setNote($formData['note']);
      $fragment->setType($this->_em->getRepository('Application_Model_Document_Fragment_Type')->findOneById($formData['type']));

      // partials
      if($fragment && $fragment->getPlag()){
        $partial = $this->_em->getRepository('Application_Model_Document_Fragment_Partial')->findOneById($fragment->getPlag()->getId());
      }else{
        $partial = new Application_Model_Document_Fragment_Partial();
      }
      $fragment->setPlag($this->handlelPartialCreation($partial, $formData['candidateLineFrom'], $formData['candidateLineTo']));

      if($fragment && $fragment->getSource()){
        $partial = $this->_em->getRepository('Application_Model_Document_Fragment_Partial')->findOneById($fragment->getSource()->getId());
      }else{
        $partial = new Application_Model_Document_Fragment_Partial();
      }
      $fragment->setSource($this->handlelPartialCreation($partial, $formData['sourceLineFrom'], $formData['sourceLineTo']));

      // write back to persistence manager and flush it
      $this->_em->persist($fragment);
      $this->_em->flush();

      return $fragment;
    }

    return false;
  }

  /**
   * Creates a partial of a fragment (candidate or source part).
   * 
   * @param Application_Model_Document_Fragment_Partial $partial
   * @param type $lineFromId
   * @param type $lineToId
   * @param type $characterFrom
   * @param type $characterTo
   * @return \Application_Model_Document_Fragment_Partial 
   */
  private function handlelPartialCreation($partial = null, $lineFromId, $lineToId, $characterFrom = 1, $characterTo = 1){
    if(!($partial)){
      $partial = new Application_Model_Document_Fragment_Partial();
    }

    $partial->setLineFrom($this->_em->getRepository('Application_Model_Document_Page_Line')->findOneById($lineFromId));
    $partial->setCharacterFrom($characterFrom);
    $partial->setLineTo($this->_em->getRepository('Application_Model_Document_Page_Line')->findOneById($lineToId));
    $partial->setCharacterTo($characterTo);

    return $partial;
  }

  /**
   * Adds select options to the form in order to show the same dropdown options after page reload
   * which were added through ajax only. Also needed for Zend Haystack validator on select elements.
   * 
   * @param type $modifyForm
   * @param type $prefix
   * @param type $formData 
   */
  private function initalisePartial(&$modifyForm, $prefix, &$formData){
    $modifyForm->getElement($prefix . 'Document')->setValue($formData[$prefix . 'Document']);

    // initialise page select
    $document = $this->_em->getRepository('Application_Model_Document')->findOneById($formData[$prefix . 'Document']);
    if($document){
      foreach($document->getPages() as $page){
        $modifyForm->getElement($prefix . 'PageFrom')->addMultioption($page->getId(), $page->getPageNumber());
        $modifyForm->getElement($prefix . 'PageTo')->addMultioption($page->getId(), $page->getPageNumber());

        $modifyForm->getElement($prefix . 'PageFrom')->setAttrib('disabled', null);
        $modifyForm->getElement($prefix . 'PageTo')->setAttrib('disabled', null);
      }

      if(!empty($formData[$prefix . 'PageFrom'])){
        $modifyForm->getElement($prefix . 'PageFrom')->setValue($formData[$prefix . 'PageFrom']);

        // initialise line from select
        $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($formData[$prefix . 'PageFrom']);
        foreach($page->getLines() as $line){
          $modifyForm->getElement($prefix . 'LineFrom')->addMultioption($line->getId(), $line->getLineNumber());
        }
        $modifyForm->getElement($prefix . 'LineFrom')->setValue($formData[$prefix . 'LineFrom']);
        $modifyForm->getElement($prefix . 'LineFrom')->setAttrib('disabled', null);
      }

      if(!empty($formData[$prefix . 'PageTo'])){
        $modifyForm->getElement($prefix . 'PageTo')->setValue($formData[$prefix . 'PageTo']);

        // initialise line to select
        $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($formData[$prefix . 'PageTo']);
        foreach($page->getLines() as $line){
          $modifyForm->getElement($prefix . 'LineTo')->addMultioption($line->getId(), $line->getLineNumber());
        }

        $modifyForm->getElement($prefix . 'LineTo')->setValue($formData[$prefix . 'LineTo']);
        $modifyForm->getElement($prefix . 'LineTo')->setAttrib('disabled', null);
      }
    }
  }

}

?>
