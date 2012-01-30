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
    // @todo: clean input
    $documentId = $this->_getParam('id');
    $page = $this->_getParam('page');

    if(!empty($documentId)){
      $query = $this->_em->createQuery("SELECT p FROM Application_Model_Document_Page p WHERE p.document = '" . $documentId . "'");
      $count = $this->_em->createQuery("SELECT COUNT(p.id) FROM Application_Model_Document_Page p WHERE p.document = '" . $documentId . "'");

      $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count));
      $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
      $paginator->setCurrentPageNumber($page);

      $this->view->paginator = $paginator;

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

  public function stopwordsAction(){
    $pageId = $this->_getParam('id');
    if(!empty($pageId)){
      $pageId = preg_replace('/[^0-9]/', '', $pageId);
      $page = $this->_em->getRepository('Application_Model_Document_Page')->findOneById($pageId);


      if($page){

        $this->view->page = $page;
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
      }
    }
  }

}

?>
