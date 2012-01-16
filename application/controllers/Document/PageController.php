<?php

/**
 * Description of DocumentController
 *
 * @author benjamin
 */
class Document_PageController extends Zend_Controller_Action{

  public function init(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
    $this->_defaultNamespace = new Zend_Session_Namespace('Default');
    $this->view->flashMessages = $this->_helper->flashMessenger->getMessages();
  }

  public function indexAction(){
    $this->_helper->redirector('list', 'document_page');
  }

  public function listAction(){
    $documentId = $this->_getParam('id');

    if(!empty($documentId)){
      $documentId = preg_replace('/[^0-9]/', '', $documentId);
      $document = $this->_em->getRepository('Application_Model_Document')->findOneById($documentId);
      if($document){
        $this->view->document = $document;
      }
    }
  }

  public function showAction(){
    $pageId = $this->_getParam('id');

    if(!empty($pageId)){
      $pageId = preg_replace('/[^0-9]/', '', $pageId);
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($pageId);
      if($page){
        $this->view->page = $page;
      }
    }
  }

  public function deHyphenAction(){
    $pageId = $this->_getParam('id');

    if(!empty($pageId)){
      $pageId = preg_replace('/[^0-9]/', '', $pageId);
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($pageId);
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
    $pageId = $this->getRequest()->getParam('id');
    $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($pageId);

    $editForm = new Application_Form_Document_Page_Modify();
    $editForm->setAction("/document_page/edit/id/" . $pageId);

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
    $pageId = $this->_getParam('id');

    if(!empty($pageId)){
      $pageId = preg_replace('/[^0-9]/', '', $pageId);
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($pageId);
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

}

?>
