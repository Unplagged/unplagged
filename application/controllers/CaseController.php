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
 * Handles action related to cases.
 */
class CaseController extends Unplagged_Controller_Action{

  public function indexAction(){
    $this->_helper->redirector('list', 'case');
  }

  public function createAction(){
    $modifyForm = new Application_Form_Case_Modify();

    if($this->_request->isPost()){
      $result = $this->handleModifyData($modifyForm);
      
      if($result){
        $this->_helper->flashMessenger->addMessage('The case was created successfully.');
        $this->_helper->redirector('list', 'case');
      }
    }

    $this->view->title = "Create case";
    $this->view->modifyForm = $modifyForm;
    $this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'case'));
  }

  public function editAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    $case = $this->_em->getRepository('Application_Model_Case')->findOneById($input->id);

    if($case){
      $modifyForm = new Application_Form_Case_Modify();
      $modifyForm->setAction("/case/edit/id/" . $input->id);

      $modifyForm->getElement("name")->setValue($case->getName());
      $modifyForm->getElement("alias")->setValue($case->getAlias());
      $modifyForm->getElement("abbreviation")->setValue($case->getAbbreviation());
      $modifyForm->getElement("submit")->setLabel("Save case");

      if($this->_request->isPost()){
        $result = $this->handleModifyData($modifyForm, $case);
        
        if($result){
          $this->_helper->flashMessenger->addMessage('The case was updated successfully.');
          $this->_helper->redirector('list', 'case');
        }
      }

      $this->view->title = "Edit case";
      $this->view->modifyForm = $modifyForm;
      $this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'case'));
    }else{
      $this->_helper->redirector('list', 'case');
    }
  }

  public function listAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());

    $query = $this->_em->createQuery("SELECT c FROM Application_Model_Case c");
    $count = $this->_em->createQuery("SELECT COUNT(c.id) FROM Application_Model_Case c");

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

    $this->view->paginator = $paginator;
  }

  public function autocompleteAliasAction(){
    $input = new Zend_Filter_Input(array('term'=>'Alpha'), null, $this->_getAllParams());

    $qb = $this->_em->createQueryBuilder();
    $qb->add('select', "c.id AS value, c.alias AS label")
        ->add('from', 'Application_Model_Case c')
        ->where("c.alias LIKE '%" . $input->term . "%'");
    $qb->setMaxResults(5);

    $dbresults = $qb->getQuery()->getResult();
    $results = array();
    foreach($dbresults as $key=>$value){
      $results[] = $value;
    }
    $this->_helper->json($results);
  }

  public function filesAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());
    
    $this->setTitle('Case Files');

    $userId = $this->_defaultNamespace->userId;
    $user = $this->_em->getRepository('Application_Model_User')->findOneById($userId);
    $case = $user->getCurrentCase();
    
    $caseFiles = $case->getFiles();

    $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($caseFiles->toArray()));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

    $this->view->paginator = $paginator;

    //change the view to the one from the file controller
    $this->_helper->viewRenderer->renderBySpec('list', array('controller'=>'file'));
  }

  public function addFileAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());
    
    $file = $this->_em->getRepository('Application_Model_File')->findOneById($input->id);
    $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);

    $case = $user->getCurrentCase();

    $case->addFile($file);
    $this->_em->persist($case);
    $this->_em->flush();
    
    $this->_helper->viewRenderer->setNoRender(true);
  }

  private function handleModifyData(Application_Form_Case_Modify $modifyForm, Application_Model_Case $case = null){
    if(!($case)){
      $case = new Application_Model_Case();
    }

    $formData = $this->_request->getPost();
    if($modifyForm->isValid($formData)){

      $case->setName($formData['name']);
      $case->setAlias($formData['alias']);
      $case->setAbbreviation($formData['abbreviation']);

      /*
        // add the collaborators
        $case->clearCollaborators();
        if(!empty($formData["collaborator"])){
        foreach($formData["collaborator"] as $key=>$value){
        $userId = preg_replace('/[^0-9]/', '', $value);
        $collaborator = $this->_em->find('Application_Model_User', $userId);
        $case->addReviewer($collaborator);
        }
        }

        // add the tags
        $case->clearTags();
        if(!empty($formData["tags"])){
        foreach($formData["tags"] as $key=>$value){
        $tagId = preg_replace('/[^0-9]/', '', $value);
        $tag = $this->_em->find('Application_Model_Tag', $tagId);
        if(!$tag){
        if(substr($value, 0, 4) == "true"){
        $value = substr($value, 4);
        }else{
        $value = substr($value, 5);
        }

        $tag = new Application_Model_Tag();
        $tag->setTitle($value);
        $this->_em->persist($tag);
        }
        $case->addTag($tag);
        }
        }
       */
      // write back to persistence manager and flush it
      $this->_em->persist($case);
      $this->_em->flush();

      // notification
      $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
      Unplagged_Helper::notify("case_created", $case, $user);

      return true;
    }
    
    return false;
  }

}

?>
