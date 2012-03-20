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
 * The controller class handles all the user transactions as rights requests and user management.
 *
 */
class UserController extends Zend_Controller_Action{

  /**
   * Initalizes registry and namespace instance in the controller and allows to display flash messages in the view.
   * 
   * @see Zend_Controller_Action::init()
   */
  public function init(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
    $this->_defaultNamespace = new Zend_Session_Namespace('Default');
    
    $this->view->flashMessages = $this->_helper->flashMessenger->getMessages();
  }

  public function indexAction(){
    
  }

  /**
   * Displays a form for registering a user.
   */
  public function registerAction(){
    // create the form
    $registerForm = new Application_Form_User_Register();

    // form has been submitted through post request
    if($this->_request->isPost()){
      $formData = $this->_request->getPost();

      // if the form doesn't validate, pass to view and return
      if($registerForm->isValid($formData)){
        // create new user object
        $data = array();
        $data["username"] = $this->getRequest()->getParam('username');
        $data["password"] = Unplagged_Helper::hashString($this->getRequest()->getParam('password'));
        $data["email"] = $this->getRequest()->getParam('email');
        $data["verificationHash"] = Unplagged_Helper::generateRandomHash();
        $data["state"] = $this->_em->getRepository('Application_Model_User_State')->findOneByTitle('registered');
        $user = new Application_Model_User($data);

        // write back to persistence manager and flush it
        $this->_em->persist($user);
        $this->_em->flush();

        // log registration
        Unplagged_Dao_Log::log("user", "registration", $user);

        // send registration mail
        Unplagged_Mailer::sendRegistrationMail($user);
        Unplagged_Dao_Log::log("mailer", "registration", $user);

        $this->_helper->flashMessenger->addMessage('Registration done.');
        $this->_helper->redirector('index', 'index');
      }
    }

    // send form to view
    $this->view->registerForm = $registerForm;
  }

  /**
   * Verifies a user by a given hash in database.
   */
  public function verifyAction(){
    $verificationHash = preg_replace('/[^0-9a-z]/i', '', $this->getRequest()->getParam('hash'));

    // if no valid verification hash is set
    if(empty($verificationHash)){
      $this->_helper->redirector('index', 'index');
    }

    $user = $this->_em->getRepository('Application_Model_User')->findOneByVerificationHash($verificationHash);
    if(empty($user) || $user->getState()->getTitle() != 'registered'){
      $this->_helper->flashMessenger->addMessage('Verification failed.');
      $this->_helper->redirector('index', 'index');
    }else{
      $user->setState($this->_em->getRepository('Application_Model_User_State')->findOneByTitle('activated'));

      // write back to persistence manage and flush it
      $this->_em->persist($user);
      $this->_em->flush();

      // log verification
      Unplagged_Dao_Log::log("user", "verification", $user);

      // send registration mail
      Unplagged_Mailer::sendActivationMail($user);
      Unplagged_Dao_Log::log("mailer", "verification", $user);

      $this->_helper->flashMessenger->addMessage('Verification finished successfully.');
      $this->_helper->redirector('index', 'index');
    }
  }

  /**
   * Displays a form for editing a user profile.
   */
  public function editAction(){
    $userId = preg_replace('/[^0-9]/', '', $this->getRequest()->getParam('id'));
    // if either the user is not logged in or no valid user id is defined
    if(empty($userId)){
      $userId = $this->_defaultNamespace->userId;
    }

    $user = $this->_em->getRepository('Application_Model_User')->findOneById($userId);
    if(empty($user)){
      $this->_helper->flashMessenger->addMessage('User Profile saved successfully.');
      $this->_helper->redirector('index', 'index');
    }elseif($this->_defaultNamespace->userId != $userId){
      $this->_helper->flashMessenger->addMessage('No permission to edit other users.');
      $this->_helper->redirector('index', 'index');
    }else{
      // display the form with user data pre-loaded
      $profileForm = new Application_Form_User_Profile($userId);

      // form has been submitted through post request
      if($this->_request->isPost()){
        $formData = $this->_request->getPost();

        // if the form doesn't validate, pass to view and return
        if($profileForm->isValid($formData)){
          print_r($formData);
          
          $adapter = new Zend_File_Transfer_Adapter_Http();
          $adapter->setOptions(array('useByteString'=>false));
          
          // collect file information
         $avatarfileName = pathinfo($adapter->getFileName(), PATHINFO_BASENAME);
         $fileExt = pathinfo($adapter->getFileName(), PATHINFO_EXTENSION);
          
          // store file in database to get an id
            $data = array();
            $data["size"] = $adapter->getFileSize('avatarfilepath');
            //if the mime type is always application/octet-stream, then the 
            //mime magic and fileinfo extensions are probably not installed
            $data["mimetype"] = $adapter->getMimeType('avatarfilepath');
            $data["filename"] = !empty($newName) ? $newName . "." . $fileExt : $avatarfileName;
            $data["extension"] = $fileExt;
            $data["location"] = "application" . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "files";

            $file = new Application_Model_File($data);
            $id = $this->_em->persist($file);
            $this->_em->flush();
            
          // select the user and update the values
          $user->setFirstname($this->getRequest()->getParam('firstname'));
          $user->setLastname($this->getRequest()->getParam('lastname'));
          $user->setAvatarfilepath($file->getId());
          // write back to persistence manage and flush it
          $this->_em->persist($user);
          $this->_em->flush();

          $this->_helper->flashMessenger->addMessage('User Profile saved successfully.');
          $this->_helper->redirector('index', 'index');
        }
      }

      // send form to view
      $this->view->profileForm = $profileForm;
    }
  }

  public function setCurrentCaseAction(){
    $caseId = preg_replace('/[^0-9]/', '', $this->getRequest()->getParam('case'));

    if($caseId){
      $case = $this->_em->getRepository('Application_Model_Case')->findOneById($caseId);
      if($case){
        $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
        $user->setCurrentCase($case);

        $this->_em->persist($user);
        $this->_em->flush();
      }
    }

    $result["caseId"] = $caseId;
    $result["response"] = "200";
    $this->_helper->json($result);
  }

  public function resetCurrentCaseAction(){
    $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);

    $user->unsetCurrentCase();
    $this->_em->persist($user);
    $this->_em->flush();

    $result["response"] = "200";
    $this->_helper->json($result);
  }

  /**
   * Selects 5 users based on matching first and lastname with the search string and sends their ids as json string back.
   * @param String from If defined it selects only users of a specific rank.
   */
  public function autocompleteNamesAction(){
    $search_string = $this->_getParam('term');
    // user ids to skip
    $skipIds = $this->_getParam('skip');

    // no self select possible
    //$skipIds .= ", " . $this->_defaultNamespace->userId;
    if(substr($skipIds, 0, 1) == ","){
      $skipIds = substr($skip_userids, 1);
    }

    if($skipIds != ""){
      $skipIds = " AND u.id NOT IN (" . $skipIds . ")";
    }

    $qb = $this->_em->createQueryBuilder();
    $qb->add('select', "CONCAT(CONCAT(u.firstname, ' '), u.lastname) AS name, u.id AS value")
        ->add('from', 'Application_Model_User u')
        ->where("CONCAT(CONCAT(u.firstname, ' '), u.lastname) LIKE '%" . $search_string . "%' " . $skipIds);
    $qb->setMaxResults(5);

    $dbresults = $qb->getQuery()->getResult();
    $results = array();
    foreach($dbresults as $key=>$value){
      $results[] = $value;
    }
    $this->_helper->json($results);
  }

}
