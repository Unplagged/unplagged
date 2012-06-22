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
 * This class handles actions related to pages within a document
 */
class Document_PageController extends Unplagged_Controller_Versionable{

  public function indexAction(){
    $this->_helper->redirector('list', 'document_page');
  }

  /**
   * Creates a single document page. 
   */
  public function createAction(){
    $input = new Zend_Filter_Input(array('document'=>'Digits'), null, $this->_getAllParams());
    $document = $this->_em->getRepository('Application_Model_Document')->findOneById($input->document);

    if($document){
      $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$document));
      if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
        $this->redirectToLastPage(true);
      }

      Zend_Layout::getMvcInstance()->menu = $document->getSidebarActions();

      $modifyForm = new Application_Form_Document_Page_Modify();

      if($this->_request->isPost()){
        $result = $this->handleModifyData($modifyForm);

        if($result){
          $result->setDocument($document);
          $this->_em->persist($result);
          $this->_em->flush();

// notification
//$user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
//Unplagged_Helper::notify('case_created', $result, $user);

          $this->_helper->FlashMessenger(array('success'=>'The document page was created successfully.'));
          $params = array('id'=>$result->getId());
          $this->_helper->redirector('show', 'document_page', '', $params);
        }
      }

      $this->view->modifyForm = $modifyForm;
      $this->view->title = 'Document: ' . $document->getTitle();
      $this->view->tooltitle = 'Create';
      $this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'document_page'));
    }
  }

  /**
   * Edits a single document page. 
   */
  public function editAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    $this->_em->clear();
    
    $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->id);
    if($page){
       //@todo: need to fix that, since em needs to be cleared, the user is gone at this point
/*     $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$page->getDocument()));
      if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
        $this->redirectToLastPage(true);
      }*/

      Zend_Layout::getMvcInstance()->menu = $page->getSidebarActions();

      $modifyForm = new Application_Form_Document_Page_Modify();
      $modifyForm->setAction("/document_page/edit/id/" . $input->id);

      $modifyForm->getElement("pageNumber")->setValue($page->getPageNumber());
      $modifyForm->getElement("disabled")->setValue($page->getDisabled());
      $modifyForm->getElement("content")->setValue($page->getContent("text"));
      $modifyForm->getElement("submit")->setLabel("Save page");

      if($this->_request->isPost()){
        $result = $this->handleModifyData($modifyForm, $page);

        if($result){
// notification
//$user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
//Unplagged_Helper::notify("case_updated", $result, $user);

          $this->_helper->FlashMessenger(array('success'=>'The document page was updated successfully.'));
          $params = array('id'=>$page->getId());
          $this->_helper->redirector('show', 'document_page', '', $params);
        }
      }

// $this->view->title = "Edit page";
      $this->view->modifyForm = $modifyForm;
      $this->initPageView($page, '/document_page/edit/id');
      $this->view->tooltitle = 'Edit';
      $this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'document_page'));
    }else{
      $this->_helper->FlashMessenger(array('error'=>'The specified page does not exist.'));
      $this->_helper->redirector('list', 'case');
    }
  }

  public function listAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $document = $this->_em->getRepository('Application_Model_Document')->findOneById($input->id);
      if($document){
        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$document));
        if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
          $this->redirectToLastPage(true);
        }

        Zend_Layout::getMvcInstance()->menu = $document->getSidebarActions();

        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'read', 'base'=>null));
        $query = 'SELECT p FROM Application_Model_Document_Page p JOIN p.document b';
        $count = 'SELECT COUNT(p.id) FROM Application_Model_Document_Page p JOIN p.document b';

        $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count, array('p.document'=>$input->id), 'p.pageNumber ASC', $permission));
        $paginator->setItemCountPerPage(100);
        $paginator->setCurrentPageNumber($input->page);

        $this->view->paginator = $paginator;
        $this->view->title = 'Document: ' . $document->getTitle();
      }
    }
  }

  public function detectionReportsAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits', 'page'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->id);
      if($page){
        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$page->getDocument()));
        if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
          $this->redirectToLastPage(true);
        }

        Zend_Layout::getMvcInstance()->menu = $page->getSidebarActions();

        $query = $this->_em->createQuery("SELECT p FROM Application_Model_Document_Page_DetectionReport p WHERE p.page = '" . $input->id . "'");
        $count = $this->_em->createQuery("SELECT COUNT(p.id) FROM Application_Model_Document_Page_DetectionReport p WHERE p.page = '" . $input->id . "'");

        $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
        $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
        $paginator->setCurrentPageNumber($input->page);

        $this->view->paginator = $paginator;
      }else{
        $this->redirectToLastPage();
      }
    }else{
      $this->redirectToLastPage();
    }
  }

  public function showAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->id);
      if($page){
        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$page->getDocument()));
        if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
          $this->redirectToLastPage(true);
        }

        Zend_Layout::getMvcInstance()->menu = $page->getSidebarActions();

        $this->view->case = Zend_Registry::getInstance()->user->getCurrentCase();
        $this->view->page = $page;
        $this->initPageView($page);
      }
    }
  }

  public function deHyphenAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->id);
      if($page){
        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$page->getDocument()));
        if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
          $this->redirectToLastPage(true);
        }

        Zend_Layout::getMvcInstance()->menu = $page->getSidebarActions();

        $lines = $page->getContent("array");

        $pageLines = array();
        foreach($lines as $lineNumber=>$content){
          $pageLine["content"] = !empty($content) ? trim($content) : ' ';
          $pageLine["hasHyphen"] = (substr($pageLine["content"], -1) == "-");
          $pageLines[] = $pageLine;
        }
// create form
        $deHyphenForm = new Application_Form_Document_Page_Dehyphen(array('pageLines'=>$pageLines));

        if($this->_request->isPost()){
          $formData = $this->_request->getPost();

          $lineContent = array();
          if($deHyphenForm->isValid($formData)){
            foreach($formData["pageLine"] as $lineNumber=>$doDehyphenation){
              if($doDehyphenation == 1){
// remove last character (the hyphen)
                $pageLines[$lineNumber]["content"] = substr($pageLines[$lineNumber]["content"], 0, -1);

// move first word of following line to the current line to merge it with the last word
                if($pageLines[$lineNumber + 1]){
                  $nextLine = $pageLines[$lineNumber + 1];
                  if(!empty($nextLine["content"])){
                    $wordsNextLine = explode(" ", $nextLine["content"]);
                    $wordToMerge = array_shift($wordsNextLine);

                    $pageLines[$lineNumber]["content"] .= $wordToMerge;
                    $pageLines[$lineNumber + 1]["content"] = implode(" ", $wordsNextLine);
                  }
                }
              }
              $lineContent[] = $pageLines[$lineNumber]["content"];
            }

            $page->setContent($lineContent, "array");

// write back to persistence manager and flush it
            $this->_em->persist($page);
            $this->_em->flush();

            $this->_helper->FlashMessenger(array('success'=>'The de-hyphenation was processed successfully.'));
            $params = array('id'=>$page->getId());
            $this->_helper->redirector('show', 'document_page', '', $params);
          }
        }
        $this->view->deHyphenForm = $deHyphenForm;
      }
      $this->initPageView($page, '/document_page/de-hyphen/id');
    }
  }

  private function handleModifyData(Application_Form_Document_Page_Modify $modifyForm, Application_Model_Document_Page $page = null){
    if(!($page)){
      $page = new Application_Model_Document_Page();
    }

    $formData = $this->_request->getPost();
    if($modifyForm->isValid($formData)){

      $page->setPageNumber($formData['pageNumber']);
      $page->setDisabled($formData['disabled']);
      $page->setContent($formData["content"], "text");

// write back to persistence manager and flush it
      $this->_em->persist($page);
      $this->_em->flush();
      /*
        // updates the barcode data
        $case = Zend_Registry::getInstance()->user->getCurrentCase();
        $case->updateBarcodeData();
        $this->_em->persist($case);
        $this->_em->flush();
       */
      return $page;
    }

    return false;
  }

  public function deleteAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->id);
      if($page){
        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$page->getDocument()));
        if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
          $this->redirectToLastPage(true);
        }

        $this->_em->remove($page);
        $this->_em->flush();
      }else{
        $this->_helper->FlashMessenger('Page does not exist.');
      }
    }

    $this->_helper->FlashMessenger(array('info'=>'The document page was deleted successfully.'));
    $params = array('id'=>$page->getDocument()->getId());
    $this->_helper->redirector('list', 'document_page', '', $params);

// disable view
    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

  public function stopwordsAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());
    if(!empty($input->id)){
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->id);

      if($page){
        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$page->getDocument()));
        if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
          $this->redirectToLastPage(true);
        }

        Zend_Layout::getMvcInstance()->menu = $page->getSidebarActions();

        $words = array('hat', 'mit',
          'aber', 'als', 'am', 'an', 'auch', 'auf', 'aus', 'bei', 'bin',
          'bis', 'ist', 'da', 'dadurch', 'daher', 'darum', 'das', 'da√ü', 'dass', 'dein', 'deine',
          'dem', 'den', 'der', 'des', 'dessen', 'deshalb', 'die', 'die', 'dieser', 'dieses', 'doch', 'dort', 'du', 'durch',
          'ein', 'eine', 'einem', 'einen', 'einer', 'eines', 'er', 'es', 'euer', 'eure', 'f√ºr', 'hatte', 'hatten', 'hattest',
          'hattet', 'hier', 'hinter', 'ich', 'ihr', 'ihre', 'im', 'in', 'ist', 'ja', 'jede', 'jedem', 'jeden', 'jeder', 'jedes',
          'jener', 'jenes', 'jetzt', 'kann', 'kannst', 'k√∂nnen', 'k√∂nnt', 'machen', 'mein', 'meine', 'mit', 'mu√ü', 'mu√üt',
          'musst', 'm√ºssen', 'm√º√üt', 'nach', 'nachdem', 'nein', 'nicht', 'nun', 'oder', 'seid', 'sein', 'seine', 'sich', 'sie',
          'sind', 'soll', 'sollen', 'sollst', 'sollt', 'sonst', 'soweit', 'sowie', 'und', 'unser', 'unsere', 'unter', 'vom', 'von',
          'vor', 'wann', 'warum', 'was', 'weiter', 'weitere', 'wenn', 'wer', 'werde', 'werden', 'werdet', 'weshalb', 'wie', 'wieder',
          'wieso', 'wir', 'wird', 'wirst', 'wo', 'woher', 'wohin', 'zu', 'zum', 'zur', '√ºber');
        $reg = '/(?<=\s)(' . implode('|', $words) . ')(?=\s)/';
        $lines = $page->getContent("list");
        $lines = preg_replace($reg, "<span class='stopword'>$1</span>", $lines);

        $this->view->stopWordContent = $lines;
        $this->initPageView($page, '/document_page/stopwords/id');
      }
    }
  }

  public function simtextReportsAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits', 'page'=>'Digits', 'show'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->show)){
      $report = $this->_em->getRepository('Application_Model_Document_Page_SimtextReport')->findOneById($input->show);
      $this->view->report = $report;

      $this->render('simtext/show');
    }else{
      if(!empty($input->id)){
<<<<<<< HEAD
        $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->id);
        if($page){
          $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$document));
          if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
            $this->redirectToLastPage(true);
          }
          Zend_Layout::getMvcInstance()->menu = $page->getSidebarActions();

          $query = $this->_em->createQuery("SELECT p FROM Application_Model_Document_Page_SimtextReport p WHERE p.page = '" . $input->id . "'");
          $count = $this->_em->createQuery("SELECT COUNT(p.id) FROM Application_Model_Document_Page_SimtextReport p WHERE p.page = '" . $input->id . "'");

          $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
          $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
          $paginator->setCurrentPageNumber($input->page);

          foreach($paginator as $report):
            if($report->getState()->getName() == 'task_scheduled'){
// find the associated task and get percentage
              $state = $this->_em->getRepository('Application_Model_State')->findOneByName('task_running');
              $task = $this->_em->getRepository('Application_Model_Task')->findOneBy(array('ressource'=>$report->getId(), 'state'=>$state));
              if(!$task){
                $percentage = 0;
              }else{
                $percentage = $task->getProgressPercentage();
              }
              $report->outputState = '<div class="progress"><div class="bar" style="width: ' . $percentage . '%;"></div></div>';
=======
        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'read', 'base'=>null));
        $query = 'SELECT p FROM Application_Model_Document_Page_SimtextReport p JOIN p.page c JOIN c.document b';
        $count = 'SELECT count(p.id) FROM Application_Model_Document_Page_SimtextReport p JOIN p.page c JOIN c.document b';

        $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count, array('p.page'=>$input->id), null, $permission));
        $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
        $paginator->setCurrentPageNumber($input->page);

        foreach($paginator as $report):
          if($report->getState()->getName() == 'task_scheduled'){
            // find the associated task and get percentage
            $state = $this->_em->getRepository('Application_Model_State')->findOneByName('task_running');
            $task = $this->_em->getRepository('Application_Model_Task')->findOneBy(array('ressource'=>$report->getId(), 'state'=>$state));
            if(!$task){
              $percentage = 0;
>>>>>>> 5d8376472d32b3c9eb0b0ddd34e47071408cb9bf
            }else{
              $report->outputState = $report->getState()->getTitle();
            }
          endforeach;

          $this->view->paginator = $paginator;
          $this->render('simtext/list-reports');
        }
      }
    }
  }

  /**
   * Does a simtext comparision with a page and multiple sources.
   */
  public function simtextAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->id);
      if($page){
        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$page->getDocument()));
        if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
          $this->redirectToLastPage(true);
        }
        Zend_Layout::getMvcInstance()->menu = $page->getSidebarActions();

        $this->view->page = $page;

        $simtextForm = new Application_Form_Document_Page_Simtext();
        $simtextForm->setAction("/document_page/simtext/id/" . $input->id);

        if($this->_request->isPost()){
          $result = $this->handleSimtextData($simtextForm, $page);

          if($result){
            $this->_helper->FlashMessenger('The simtext process was started, you will be notified, when it finished.');
            $this->_helper->redirector('simtext-reports', 'document_page', '', array('id'=>$input->id));
          }
        }

        $this->initPageView($page, '/document_page/simtext/id');
        $this->view->simtextForm = $simtextForm;
        $this->render('simtext/create');
      }
    }
  }

  private function handleSimtextData(Application_Form_Document_Page_Simtext $simtextForm, Application_Model_Document_Page $page){
    if(!($page)){
      $page = new Application_Model_Document_Page();
    }

    $formData = $this->_request->getPost();
    if($simtextForm->isValid($formData)){

      $data["page"] = $page;
      $data["title"] = $formData["title"];
      $data["documents"] = $formData["documents"];
      $data["state"] = $this->_em->getRepository('Application_Model_State')->findOneByName('task_scheduled');
      $report = new Application_Model_Document_Page_SimtextReport($data);

// start task
      $data = array();
      $data["initiator"] = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
      $data["ressource"] = $report;
      $data["action"] = $this->_em->getRepository('Application_Model_Action')->findOneByName('page_simtext');
      $data["state"] = $this->_em->getRepository('Application_Model_State')->findOneByName('task_scheduled');
      $task = new Application_Model_Task($data);

      $this->_em->persist($task);
      $this->_em->flush();

      /*
        //$formData["documents"]
        // notification @todo: add notification
        $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
        Unplagged_Helper::notify("case_created", $case, $user);
       */

      return $task;
    }

    return false;
  }

  /**
   * Compares two version of a fragment. 
   */
  public function changelogAction(){
    parent::changelogAction();

    $this->setTitle("Changelog of page");
  }

  /**
   * Returns all lines in the document page. 
   */
  public function readAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->id);
      if($page){
        $response["statuscode"] = 200;
        $response["data"] = $page->toArray(true);
      }else{
        $response["statuscode"] = 404;
        $response["statusmessage"] = "No page by that id found.";
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

  public function fragmentAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits', 'source'=>'Digits', 'sourceDocument'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->id);

      if($page){
        $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'document', 'action'=>'update', 'base'=>$page->getDocument()));
        if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
          $this->redirectToLastPage(true);
        }
        Zend_Layout::getMvcInstance()->menu = $page->getSidebarActions();


        if(!empty($input->sourceDocument)){
          $sourceDocument = $this->_em->getRepository('Application_Model_Document')->findOneById($input->sourceDocument);
          $source = $sourceDocument->getPages()->first();
        }elseif(!empty($input->source)){
          $source = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->source);
        }
        if(isset($source)){
          $this->view->source = $source;
          $pageLink = '/document_page/fragment/id/' . $input->id . '/source';
          $this->initPageView($source, $pageLink, 'prevSourcePageLink', 'nextSourcePageLink');
        }

        $pageLink = '/document_page/fragment/id';
        if(!empty($source)){
          $pageLink = '/document_page/fragment/source/' . $input->source . '/id';
        }

        $this->view->documents = Zend_Registry::getInstance()->user->getCurrentCase()->getDocuments();
        $this->view->page = $page;
        $this->initPageView($page, $pageLink);

        if(!empty($source)){
// do simtext
          $left = $page->getContent('array');
          $right = $source->getContent('array');

          foreach($left as $lineNumber=>$lineContent){
            $left[$lineNumber] = htmlentities($lineContent, ENT_COMPAT, 'UTF-8');
          }

          foreach($right as $lineNumber=>$lineContent){
            $right[$lineNumber] = htmlentities($lineContent, ENT_COMPAT, 'UTF-8');
          }

          $simtextResult = Unplagged_CompareText::compare($left, $right, 4); // do simtext with left and right

          $left = $simtextResult['left'];
          $right = $simtextResult['right'];

          foreach($left as $lineNumber=>$lineContent){
            $left[$lineNumber] = '<li value="' . $lineNumber . '">' . $lineContent . '</li>';
          }

          foreach($right as $lineNumber=>$lineContent){
            $right[$lineNumber] = '<li value="' . $lineNumber . '">' . $lineContent . '</li>';
          }

          $this->view->pageContent = '<ol>' . implode("\n", $left) . '</ol>';
          $this->view->sourceContent = '<ol>' . implode("\n", $right) . '</ol>';
        }else{
          $this->view->pageContent = $page->getContent('list');
          $this->view->sourceContent = '';
        }
      }
    }
  }

  public function compareAction(){
    $input = new Zend_Filter_Input(array('fragment'=>'Digits', 'highlight'=>'Alpha', 'candidateLineFrom'=>'Digits', 'candidateLineTo'=>'Digits', 'sourceLineFrom'=>'Digits', 'sourceLineTo'=>'Digits'), null, $this->_getAllParams());
    if($input->fragment){
      $fragment = $this->_em->getRepository('Application_Model_Document_Fragment')->findOneById($input->fragment);
    }else{
      $fragment = new Application_Model_Document_Fragment();

      if($input->candidateLineFrom && $input->candidateLineTo){
        $partial = new Application_Model_Document_Fragment_Partial();
        $partial->setLineFrom($this->_em->getRepository('Application_Model_Document_Page_Line')->findOneById($input->candidateLineFrom));
        $partial->setLineTo($this->_em->getRepository('Application_Model_Document_Page_Line')->findOneById($input->candidateLineTo));
        $fragment->setPlag($partial);
      }

      if($input->sourceLineFrom && $input->sourceLineTo){
        $partial = new Application_Model_Document_Fragment_Partial();
        $partial->setLineFrom($this->_em->getRepository('Application_Model_Document_Page_Line')->findOneById($input->sourceLineFrom));
        $partial->setLineTo($this->_em->getRepository('Application_Model_Document_Page_Line')->findOneById($input->sourceLineTo));
        $fragment->setSource($partial);
      }
    }

    $content = $fragment->getContent('list', $input->highlight == 'true');

    $response['statuscode'] = 200;
    $response['data']['plag'] = $content['plag'];
    $response['data']['source'] = $content['source'];

    $this->getResponse()->appendBody(json_encode($response));

// disable view
    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
  }

  private function initPageView(Application_Model_Document_Page $page, $pageLink = '/document_page/show/id', $prevLinkParam = 'prevPageLink', $nextLinkParam = 'nextPageLink'){
// next page
    $query = $this->_em->createQuery('SELECT p FROM Application_Model_Document_Page p WHERE p.document = :document AND p.pageNumber > :pageNumber ORDER BY p.pageNumber ASC');
    $query->setParameter("document", $page->getDocument()->getId());
    $query->setParameter("pageNumber", $page->getPageNumber());
    $query->setMaxResults(1);

    $nextPage = $query->getResult();
    if($nextPage){
      $nextPage = $nextPage[0];
      $this->view->$nextLinkParam = $pageLink . '/' . $nextPage->getId();
    }

// previous page
    $query = $this->_em->createQuery('SELECT p FROM Application_Model_Document_Page p WHERE p.document = :document AND p.pageNumber < :pageNumber ORDER BY p.pageNumber DESC');
    $query->setParameter("document", $page->getDocument()->getId());
    $query->setParameter("pageNumber", $page->getPageNumber());
    $query->setMaxResults(1);

    $prevPage = $query->getResult();
    if($prevPage){
      $prevPage = $prevPage[0];
      $this->view->$prevLinkParam = $pageLink . '/' . $prevPage->getId();
    }

    $lastPage = $page->getDocument()->getPages()->last();

    $this->view->title = 'Document: ' . $page->getDocument()->getTitle();
    $this->view->subtitle = 'Page ' . $page->getPageNumber() . ' of ' . $lastPage->getPageNumber();
  }

}
<<<<<<< HEAD

?>
=======
?>
>>>>>>> 5d8376472d32b3c9eb0b0ddd34e47071408cb9bf
