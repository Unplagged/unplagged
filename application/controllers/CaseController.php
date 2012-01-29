<?php

/**
 * File for class {@link CaseController}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
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
    $query = $this->_em->createQuery('SELECT c FROM Application_Model_Case c');
    $cases = $query->getResult();

    $this->view->listCases = $cases;
  }
  
  public function autocompleteAliasAction() {
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

      $this->_helper->flashMessenger->addMessage('The case was successfully created.');
      $this->_helper->redirector('index', 'case');
    }else{
      //@todo error message here
    }
  }

}

?>
