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
    $modifyForm->getElement("collaborators")->setValue(array($this->_defaultNamespace->userId));

    if($this->_request->isPost()){
      $result = $this->handleModifyData($modifyForm);

      if($result){
        // notification
        $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
        Unplagged_Helper::notify('case_created', $result, $user);

        $this->_helper->FlashMessenger(array('success'=>'The case was created successfully.'));
        $this->_helper->redirector('list', 'case');
      }
    }

    $this->view->title = 'Create case';
    $this->view->modifyForm = $modifyForm;
    $this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'case'));
  }

  public function editAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    $case = $this->_em->getRepository('Application_Model_Case')->findOneById($input->id);

    if($case){
      $permission = $this->_em->getRepository('Application_Model_Permission')->findOneBy(array('type'=>'case', 'action'=>'update', 'base'=>$case));
      if(!Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
        $this->redirectToLastPage(true);
      }

      $modifyForm = new Application_Form_Case_Modify();
      $modifyForm->setAction("/case/edit/id/" . $input->id);

      $modifyForm->getElement("name")->setValue($case->getName());
      $modifyForm->getElement("alias")->setValue($case->getAlias());
      $modifyForm->getElement("tags")->setValue($case->getTagIds());
      $modifyForm->getElement("collaborators")->setValue($case->getCollaboratorIds());
      $modifyForm->getElement("submit")->setLabel("Save case");

      if($this->_request->isPost()){
        $result = $this->handleModifyData($modifyForm, $case);

        if($result){
          // notification
          $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
          Unplagged_Helper::notify("case_updated", $result, $user);

          $this->_helper->FlashMessenger(array('success'=>'The case was updated successfully.'));
          $this->_helper->redirector('list', 'case');
        }
      }

      $this->view->title = "Edit case";
      $this->view->modifyForm = $modifyForm;
      $this->_helper->viewRenderer->renderBySpec('modify', array('controller'=>'case'));
    }else{
      $this->_helper->FlashMessenger(array('error'=>'The specified case does not exist.'));
      $this->_helper->redirector('list', 'case');
    }
  }

  public function listAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());

    $permission = $this->_em->getRepository('Application_Model_Permission')->findOneBy(array('type'=>'case', 'action'=>'read', 'base'=>null));
    $query = 'SELECT b FROM Application_Model_Case b';
    $count = 'SELECT COUNT(b.id) FROM Application_Model_Case b';

    $paginator = new Zend_Paginator(new Unplagged_Paginator_Adapter_DoctrineQuery($query, $count, null, null, $permission));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

    // generate the action dropdown for each fragment
    foreach($paginator as $case):
      $case->actions = array();
      $permission = $this->_em->getRepository('Application_Model_Permission')->findOneBy(array('type'=>'case', 'action'=>'update', 'base'=>$case));
      if(Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
        $action['link'] = '/case/edit/id/' . $case->getId();
        $action['label'] = 'Edit case';
        $action['icon'] = 'images/icons/pencil.png';
        $case->actions[] = $action;
      }
    endforeach;

    $this->view->paginator = $paginator;
  }

  public function filesAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());

    $this->setTitle('Case Files');

    $user = Zend_Registry::getInstance()->user;
    $case = $user->getCurrentCase();
    if(!empty($case)){
      $caseFiles = $case->getFiles();

      $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($caseFiles->toArray()));
      $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
      $paginator->setCurrentPageNumber($input->page);

      // generate the action dropdown for each file
      // @todo: use centralised method for all three file lists
      foreach($paginator as $file):
        $file->actions = array();

        $action['link'] = '/file/parse/id/' . $file->getId();
        $action['label'] = 'OCR';
        $action['icon'] = 'images/icons/page_gear.png';
        $file->actions[] = $action;

        $action['link'] = '/file/download/id/' . $file->getId();
        $action['label'] = 'Download';
        $action['icon'] = 'images/icons/disk.png';
        $file->actions[] = $action;

        $action['link'] = '/file/delete/id/' . $file->getId();
        $action['label'] = 'Delete';
        $action['icon'] = 'images/icons/delete.png';
        $file->actions[] = $action;

        $action['link'] = '/user/add-file/id/' . $file->getId();
        $action['label'] = 'Add to personal files';
        $action['icon'] = 'images/icons/basket_put.png';
        $file->actions[] = $action;

      endforeach;

      $this->view->paginator = $paginator;
      $this->view->uploadLink = '/file/upload?area=case';

      //change the view to the one from the file controller
      $this->_helper->viewRenderer->renderBySpec('list', array('controller'=>'file'));
    }else{
      $this->_helper->FlashMessenger(array('error'=>'You need to select a case first, before you can view files of it.'));
      $this->redirectToLastPage();
    }
  }

  public function addFileAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    $file = $this->_em->getRepository('Application_Model_File')->findOneById($input->id);
    if($file){
      $user = Zend_Registry::getInstance()->user;

      $case = $user->getCurrentCase();
      if(!$case->hasFile($file)){
        $case->addFile($file);
        $this->_em->persist($case);
        $this->_em->flush();

        $this->_helper->FlashMessenger(array('success'=>'The file was added to your current case.'));
      }else{
        $this->_helper->FlashMessenger(array('error'=>'The file already belongs to the current case.'));
      }
    }else{
      $this->_helper->FlashMessenger(array('error'=>'The specified file does not exist.'));
    }

    $this->_helper->viewRenderer->setNoRender(true);
    $this->redirectToLastPage();
  }

  private function handleModifyData(Application_Form_Case_Modify $modifyForm, Application_Model_Case $case = null){
    $formData = $this->_request->getPost();
    if($modifyForm->isValid($formData)){
      if(!($case)){
        $case = new Application_Model_Case();
        $case->setName($formData['name']);
        $case->setAlias($formData['alias']);

        //flush here, so that we can use the id
        $this->_em->persist($case);
        $this->_em->flush();

        $this->initBasicRolesForCase($case);
      }else{
        $case->setAlias($formData['alias']);
        $case->setName($formData['name']);
      }

      $case->setCollaborators($formData['collaborators']);
      $case->setTags($formData['tags']);

      // write back to persistence manager and flush it
      $this->_em->persist($case);
      $this->_em->flush();

      return $case;
    }

    return false;
  }

  private function initBasicRolesForCase(Application_Model_Case $case){
    $adminRole = new Application_Model_User_InheritableRole();
    $adminRole->setRoleId('admin_case-' . $case->getId());

    $case->addDefaultRole($adminRole);
  }

}

?>