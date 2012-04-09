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
 *
 */
class CaseController extends Zend_Controller_Action{

  /**
   * Initalizes registry and namespace instance in the controller and allows to display flash messages in the view.
   * @see Zend_Controller_Action::init()
   */
  public function init(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
    $this->_defaultNamespace = new Zend_Session_Namespace('Default');
    $this->view->flashMessages = $this->_helper->flashMessenger->getMessages();
  }

  public function indexAction(){
    $this->_helper->redirector('list', 'case');
  }

  public function createAction(){
    $createForm = new Application_Form_Case_Modify();

    if($this->_request->isPost()){
      $this->handleCreationData($createForm);
    }
    $this->view->createForm = $createForm;
  }

  public function listAction(){
    // @todo: clean input
    $page = $this->_getParam('page');
    
    $query = $this->_em->createQuery("SELECT c FROM Application_Model_Case c");
		$count = $this->_em->createQuery("SELECT COUNT(c.id) FROM Application_Model_Case c");
		
		$paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
		$paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
		$paginator->setCurrentPageNumber($page);
    
    $this->view->paginator = $paginator;
  }
  
  public function autocompleteAliasAction() {
    // @todo: clean input
    $search_string = $this->_getParam('term');
    
    $qb = $this->_em->createQueryBuilder();
    $qb->add('select', "c.id AS value, c.alias AS label")
        ->add('from', 'Application_Model_Case c')
        ->where("c.alias LIKE '%" . $search_string . "%'");
    $qb->setMaxResults(5);

    $dbresults = $qb->getQuery()->getResult();
    $results = array();
    foreach($dbresults as $key=>$value){
      $results[] = $value;
    }
    $this->_helper->json($results);
  }

  private function handleCreationData(Application_Form_Case_Modify $createForm){
    $formData = $this->_request->getPost();

    if($createForm->isValid($formData)){
      $case = new Application_Model_Case($formData['name'], $formData['alias']);

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
      // write back to persistence manager and flush it
      $this->_em->persist($case);
      $this->_em->flush();
      
      // notification
      $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
      Unplagged_Helper::notify("case_created", $case, $user);

      $this->_helper->flashMessenger->addMessage('The case was successfully created.');
      $this->_helper->redirector('index', 'case');
    }else{
      //@todo error message here
    }
  }

}

?>
