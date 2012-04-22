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
class Document_PageController extends Unplagged_Controller_Action{

  public function init(){
    parent::init();

    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    Zend_Layout::getMvcInstance()->sidebar = 'page-tools';
    Zend_Layout::getMvcInstance()->versionableId = $input->id;
  }

  public function indexAction(){
    $this->_helper->redirector('list', 'document_page');
  }

  public function listAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits', 'page'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $query = $this->_em->createQuery("SELECT p FROM Application_Model_Document_Page p WHERE p.document = '" . $input->id . "'");
      $count = $this->_em->createQuery("SELECT COUNT(p.id) FROM Application_Model_Document_Page p WHERE p.document = '" . $input->id . "'");

      $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
      $paginator->setItemCountPerPage(100);
      $paginator->setCurrentPageNumber($input->page);

      $this->view->paginator = $paginator;

      $document = $this->_em->getRepository('Application_Model_Document')->findOneById($input->id);
      if($document){
        $this->view->document = $document;
      }
    }

    Zend_Layout::getMvcInstance()->sidebar = null;
    Zend_Layout::getMvcInstance()->versionableId = null;
  }

  public function detectionReportsAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits', 'page'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $query = $this->_em->createQuery("SELECT p FROM Application_Model_Document_Page_DetectionReport p WHERE p.page = '" . $input->id . "'");
      $count = $this->_em->createQuery("SELECT COUNT(p.id) FROM Application_Model_Document_Page_DetectionReport p WHERE p.page = '" . $input->id . "'");

      $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
      $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
      $paginator->setCurrentPageNumber($input->page);

      $this->view->paginator = $paginator;
    }
  }

  public function showAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->id);
      if($page){
        $this->view->page = $page;
      }
    }
  }

  public function deHyphenAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->id);
      if($page){
        $this->view->page = $page;

        $lines = explode("<br />", $page->getContent());
        $pageLines = array();
        foreach($lines as $line){
          $line = htmlspecialchars($line);

          $pageLine["content"] = empty($line) ? " " : $line;
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

            $pageContent = implode("<br />", $lineContent);
            $page->setContent($pageContent);

            // write back to persistence manager and flush it
            $this->_em->persist($page);
            $this->_em->flush();

            $this->_helper->flashMessenger->addMessage('The de-hyphenation was processed successfully.');
            $params = array('id'=>$page->getDocument()->getId());
            $this->_helper->redirector('list', 'document_page', '', $params);
          }
        }
        $this->view->deHyphenForm = $deHyphenForm;
      }
    }
  }

  public function editAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());
    $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->id);

    $editForm = new Application_Form_Document_Page_Modify();
    $editForm->setAction("/document_page/edit/id/" . $input->id);

    $editForm->getElement("pageNumber")->setValue($page->getPageNumber());
    $editForm->getElement("content")->setValue(str_replace("<br />", "\n", $page->getContent()));

    if($this->_request->isPost()){
      $formData = $this->_request->getPost();

      if($editForm->isValid($formData)){
        $page->setPageNumber($formData["pageNumber"]);

        $formData["content"] = nl2br($formData["content"]);
        $formData["content"] = str_replace("\r\n", "", $formData["content"]);
        $formData["content"] = str_replace("\n", "", $formData["content"]);
        $page->setContent($formData["content"]);

        // write back to persistence manager and flush it
        $this->_em->persist($page);
        $this->_em->flush();

        $this->_helper->flashMessenger->addMessage('The document page was updated successfully.');
        $params = array('id'=>$page->getDocument()->getId());
        $this->_helper->redirector('list', 'document_page', '', $params);
      }
    }

    $this->view->editForm = $editForm;
    $this->view->page = $page;
  }

  public function deleteAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->id);
      if($page){
        $this->_em->remove($page);
        $this->_em->flush();
      }else{
        $this->_helper->flashMessenger->addMessage('Page does not exist.');
      }
    }

    $this->_helper->flashMessenger->addMessage('The document page was deleted successfully.');
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
        $words = array('hat', 'mit',
          'aber', 'als', 'am', 'an', 'auch', 'auf', 'aus', 'bei', 'bin',
          'bis', 'ist', 'da', 'dadurch', 'daher', 'darum', 'das', 'daß', 'dass', 'dein', 'deine',
          'dem', 'den', 'der', 'des', 'dessen', 'deshalb', 'die', 'die', 'dieser', 'dieses', 'doch', 'dort', 'du', 'durch',
          'ein', 'eine', 'einem', 'einen', 'einer', 'eines', 'er', 'es', 'euer', 'eure', 'für', 'hatte', 'hatten', 'hattest',
          'hattet', 'hier', 'hinter', 'ich', 'ihr', 'ihre', 'im', 'in', 'ist', 'ja', 'jede', 'jedem', 'jeden', 'jeder', 'jedes',
          'jener', 'jenes', 'jetzt', 'kann', 'kannst', 'können', 'könnt', 'machen', 'mein', 'meine', 'mit', 'muß', 'mußt',
          'musst', 'müssen', 'müßt', 'nach', 'nachdem', 'nein', 'nicht', 'nun', 'oder', 'seid', 'sein', 'seine', 'sich', 'sie',
          'sind', 'soll', 'sollen', 'sollst', 'sollt', 'sonst', 'soweit', 'sowie', 'und', 'unser', 'unsere', 'unter', 'vom', 'von',
          'vor', 'wann', 'warum', 'was', 'weiter', 'weitere', 'wenn', 'wer', 'werde', 'werden', 'werdet', 'weshalb', 'wie', 'wieder',
          'wieso', 'wir', 'wird', 'wirst', 'wo', 'woher', 'wohin', 'zu', 'zum', 'zur', 'über');
        $reg = '/(' . implode('|', $words) . ')/i';
        $lines = $page->getContent();
        $lines = preg_replace($reg, "<span class='stopword'>$1</span>", $lines);

        $this->view->stopWordContent = $lines;
        $this->view->page = $page;
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
        $query = $this->_em->createQuery("SELECT p FROM Application_Model_Document_Page_SimtextReport p WHERE p.page = '" . $input->id . "'");
        $count = $this->_em->createQuery("SELECT COUNT(p.id) FROM Application_Model_Document_Page_SimtextReport p WHERE p.page = '" . $input->id . "'");

        $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
        $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
        $paginator->setCurrentPageNumber($input->page);

        $this->view->paginator = $paginator;
        $this->render('simtext/list-reports');
      }
    }

    Zend_Layout::getMvcInstance()->sidebar = null;
    Zend_Layout::getMvcInstance()->versionableId = null;
  }

  /**
   * Does a simtext comparision with a page and multiple  
   */
  public function simtextAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    if(!empty($input->id)){
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($input->id);
      $this->view->page = $page;

      $simtextForm = new Application_Form_Document_Page_Simtext();
      $simtextForm->setAction("/document_page/simtext/id/" . $input->id);

      if($this->_request->isPost()){
        $result = $this->handleSimtextData($simtextForm, $page);

        if($result){
          $this->_helper->flashMessenger->addMessage('The simtext process was started, you will be notified, whenever it is finished.');
          //$this->_helper->redirector('list', 'case');
        }
      }

      $this->view->title = "Create case";
      $this->view->simtextForm = $simtextForm;
      $this->render('simtext/create');
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

      return true;
    }

    return false;
  }

}

?>
