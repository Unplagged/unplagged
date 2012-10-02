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
class SimtextController extends Unplagged_Controller_Action{

  public function ajaxAction(){
    $input = new Zend_Filter_Input(array('left'=>'StripTags', 'right'=>'StripTags'), null, $this->_getAllParams());

    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender();

    echo Unplagged_CompareText::compare($input->left, $input->right, 4);
  }

  public function showReportAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $report = $this->_em->getRepository('Application_Model_Simtext_Report')->findOneById($input->id);
      if($report) {
      $this->view->report = $report;
      $this->setTitle("Simtext report: %s", array($report->getTitle()));
      } else {
        
      }
    }
  }

  public function listReportsAction(){
    $input = new Zend_Filter_Input(array('source'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->source)){
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->source);
      if($page){
        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$page->getDocument()));
        if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
          $this->redirectToLastPage(true);
        }
        Zend_Layout::getMvcInstance()->menu = $page->getSidebarActions();

        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'read', 'base'=>$page->getDocument()));
        $query = 'SELECT p FROM Application_Model_Simtext_Report p JOIN p.state ps JOIN p.page c JOIN c.document b';
        $count = 'SELECT count(p.id) FROM Application_Model_Simtext_Report p JOIN p.state ps JOIN p.page c JOIN c.document b';

        $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count, array('p.page'=>$input->source, 'ps.name'=>array('!=', 'deleted')), null, $permission));
        $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
        $paginator->setCurrentPageNumber($input->source);

        foreach($paginator as $report):
          if($report->getState()->getName() == 'scheduled'){
            // find the associated task and get percentage
            $state = $this->_em->getRepository('Application_Model_State')->findOneByName('running');
            $task = $this->_em->getRepository('Application_Model_Task')->findOneBy(array('resource'=>$report->getId(), 'state'=>$state));
            if(!$task){
              $percentage = 0;
            }else{
              $percentage = $task->getProgressPercentage();
            }
            $report->outputState = '<div class="progress"><div class="bar" style="width: ' . $percentage . '%;"></div></div>';
            $report->actions = null;

          }else{
            $report->outputState = $report->getState()->getTitle();
            $report->actions = array();
            $action['link'] = '/simtext/delete-report/id/' . $report->getId();
            $action['label'] = 'Delete report';
            $action['icon'] = 'images/icons/delete.png';
            $report->actions[] = $action;          }
        endforeach;

        $this->setTitle("List of simtext reports");
        $this->view->paginator = $paginator;
      }
    }
  }

  public function deleteReportAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $report = $this->_em->getRepository('Application_Model_Simtext_Report')->findOneById($input->id);
      if($report){
        $report->remove();
        $this->_em->persist($report);
        $this->_em->flush();
        $this->_helper->FlashMessenger(array('success'=>'The report was deleted successfully.'));
      }else{
        $this->_helper->FlashMessenger(array('error'=>'Report does not exist.'));
      }
    }

    $this->redirectToLastPage();
  }

  /**
   * Does a simtext comparision with a page and multiple sources.
   */
  public function createReportAction(){
    $input = new Zend_Filter_Input(array('source'=>'Digits', 'redirect'=>'StringTrim'), null, $this->_getAllParams());

    if(!empty($input->source)){
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->source);
      if($page){
        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$page->getDocument()));
        if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
          $this->redirectToLastPage(true);
        }
        Zend_Layout::getMvcInstance()->menu = $page->getSidebarActions();

        $this->view->page = $page;

        $simtextForm = new Application_Form_Simtext_Modify();
        
        if($this->_request->isPost()){
          $result = $this->handleSimtextData($simtextForm, $page);

          if($result){
            $this->_helper->FlashMessenger(array('success' => 'The simtext process was started, you will be notified, when it finished.'));
            $this->_helper->redirector('list-reports', 'simtext', '', array('source'=>$input->source));
            if($input->redirect) {
          $simtextForm->setAction($input->redirect);
        }
          }
        }

       // $this->initPageView($page, '/document_page/simtext/id');
        $this->view->simtextForm = $simtextForm;
        $this->setTitle("Create simtext report");
      }
    }
  }

  private function handleSimtextData(Application_Form_Simtext_Modify $simtextForm, Application_Model_Document_Page $page){
    if(!($page)){
      $page = new Application_Model_Document_Page();
    }

    $formData = $this->_request->getPost();
    if($simtextForm->isValid($formData)){

      $data["page"] = $page;
      $data["title"] = $formData["title"];
      $data["documents"] = $formData["documents"];
      $data["state"] = $this->_em->getRepository('Application_Model_State')->findOneByName('scheduled');
      $report = new Application_Model_Simtext_Report($data);

      // start task
      $data = array();
      $data["initiator"] = Zend_Registry::getInstance()->user;
      $data["resource"] = $report;
      $data["action"] = $this->_em->getRepository('Application_Model_Action')->findOneByName('page_simtext');
      $data["state"] = $this->_em->getRepository('Application_Model_State')->findOneByName('scheduled');
      $task = new Application_Model_Task($data);

      $this->_em->persist($task);
      $this->_em->flush();

      return $task;
    }

    return false;
  }

}

?>
