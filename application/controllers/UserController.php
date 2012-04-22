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
class UserController extends Unplagged_Controller_Action{

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
        $data["state"] = $this->_em->getRepository('Application_Model_State')->findOneByName('user_registered');
        $user = new Application_Model_User($data);

        // write back to persistence manager and flush it
        $this->_em->persist($user);
        $this->_em->flush();

        // log registration
        Unplagged_Helper::notify("user_registered", $user, $user);

        // send registration mail
        Unplagged_Mailer::sendRegistrationMail($user);

        $this->_helper->flashMessenger->addMessage('In order to finish your registration, please check your E-Mails.');
        $this->_helper->redirector('index', 'index');
      }
    }

    // send form to view
    $this->view->registerForm = $registerForm;
  }

  public function filesAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());
    
    $this->setTitle('Personal Files');

    $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
    $userFiles = $user->getFiles();

    $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($userFiles->toArray()));
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

    $user->addFile($file);
    $this->_em->persist($user);
    $this->_em->flush();
    
    $this->redirectToLastPage();
  }

  /**
   * Verifies a user by a given hash in database.
   */
  public function verifyAction(){
    $input = new Zend_Filter_Input(array('hash'=>'Alpha'), null, $this->_getAllParams());

    // if no valid verification hash is set
    if(empty($input->hash)){
      $this->_helper->redirector('index', 'index');
    }

    $user = $this->_em->getRepository('Application_Model_User')->findOneByVerificationHash($input->hash);
    if(empty($user) || $user->getState()->getTitle() != 'user_registered'){
      $this->_helper->flashMessenger->addMessage('Verification failed.');
      $this->_helper->redirector('index', 'index');
    }else{
      $user->setState($this->_em->getRepository('Application_Model_State')->findOneByName('user_activated'));
      $user->setVerificationHash(Unplagged_Helper::generateRandomHash());

      // write back to persistence manage and flush it
      $this->_em->persist($user);
      $this->_em->flush();

      // notification
      Unplagged_Helper::notify("user_verified", $user, $user);

      // send registration mail
      Unplagged_Mailer::sendActivationMail($user);

      $this->_helper->flashMessenger->addMessage('Verification finished successfully.');
      $this->_helper->redirector('index', 'index');
    }
  }

  /**
   * Recovers a users password
   */
  public function recoverPasswordAction(){
    $input = new Zend_Filter_Input(array('hash'=>'Alpha'), null, $this->_getAllParams());

    $user = $this->_em->getRepository('Application_Model_User')->findOneByVerificationHash($input->hash);

    if(empty($user)){
      $recoverForm = new Application_Form_User_Password_Recover();
    }else{
      $recoverForm = new Application_Form_User_Password_Reset($input->hash);
    }

    // form has been submitted through post request
    if($this->_request->isPost()){
      $formData = $this->_request->getPost();

      // if the form doesn't validate, pass to view and return
      if($recoverForm->isValid($formData)){
        // send a recovery mail to the user associated with this e-mail address.
        if(empty($user)){
          $email = $this->getRequest()->getParam('email');
          $user = $this->_em->getRepository('Application_Model_User')->findOneByEmail($email);

          $lastNotificationAction = $this->_em->getRepository('Application_Model_Notification_Action')->findOneByName("user_requested_password");
          $lastNotification = $this->_em->getRepository('Application_Model_Notification')->findOneBy(array("action"=>$lastNotificationAction->getId(), "user"=>$user->getId()));

          if($lastNotification && ($lastNotification->getCreated()->getTimestamp() > time() - NOTIFICATIONS_TIME_INTERVAL)){
            $this->_helper->flashMessenger->addMessage('There was already a password recovery request for this account.');
            $this->_helper->redirector('recover-password', 'user');
          }else{
            Unplagged_Mailer::sendPasswordRecoveryMail($user);
            Unplagged_Helper::notify("user_requested_password", $user, $user);

            $this->_helper->flashMessenger->addMessage('An E-Mail has been sent to your address, follow the instructions in this mail.');
            $this->_helper->redirector('index', 'index');
          }
          // reset the password to the new one
        }else{
          $password = $this->getRequest()->getParam('password');
          $user->setPassword(Unplagged_Helper::hashString($password));
          $user->setVerificationHash(Unplagged_Helper::generateRandomHash());

          // write back to persistence manager and flush it
          $this->_em->persist($user);
          $this->_em->flush();

          $this->_helper->flashMessenger->addMessage('Your password has been reset successfully, you can login now.');
          $this->_helper->redirector('login', 'auth');
        }
      }
    }

    // send form to view
    $this->view->recoverForm = $recoverForm;
  }

  /**
   * Displays a form for editing a user profile.
   */
  public function editAction(){
    $input = new Zend_Filter_Input(array('hash'=>'Digits'), null, $this->_getAllParams());
    print_r($input);

    // if either the user is not logged in or no valid user id is defined
    if(empty($input->id)){
      $userId = $this->_defaultNamespace->userId;
    }

    $user = $this->_em->getRepository('Application_Model_User')->findOneById($input->id);
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
            $data["size"] = $adapter->getFileSize('avatar');
            //if the mime type is always application/octet-stream, then the
            //mime magic and fileinfo extensions are probably not installed
            $data["mimetype"] = $adapter->getMimeType('avatar');
            $data["filename"] = !empty($newName) ? $newName . "." . $fileExt : $avatarfileName;
            $data["extension"] = $fileExt;
            $data["location"] = "application" . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "files";

            $file = new Application_Model_File($data);
            $id = $this->_em->persist($file);
            $this->_em->flush();
            
          // select the user and update the values
          $user->setFirstname($this->getRequest()->getParam('firstname'));
          $user->setLastname($this->getRequest()->getParam('lastname'));
          
          $user->setAvatar($file->getId());

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

  /**
   * Sets the current Case of this User based on the request parameter 'case' if the user has
   * the permission for the case. 
   */
  public function setCurrentCaseAction(){
    $input = new Zend_Filter_Input(array('hash'=>'Digits'), null, $this->_getAllParams());

    $case = null;
    if($input->id){
      $case = $this->_em->getRepository('Application_Model_Case')->findOneById($input->id);
    }
    $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
    $user->setCurrentCase($case);

    $this->_em->persist($user);
    $this->_em->flush();

    $this->redirectToLastPage();
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
    // @todo clean inpit
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

  /**
   * Removes the own user account.
   */
  public function removeAccountAction(){
    $user = $this->_em->getRepository('Application_Model_User')->findOneById($this->_defaultNamespace->userId);
    // display the form with user data pre-loaded
    $removalForm = new Application_Form_User_Remove();

    // form has been submitted through post request
    if($this->_request->isPost()){
      $formData = $this->_request->getPost();

      // if the form doesn't validate, pass to view and return
      if($removalForm->isValid($formData)){

        // @todo: handle mail
        // write back to persistence manage and flush it
        $this->_em->remove($user);
        $this->_em->flush();

        $this->_helper->redirector('logout', 'auth');
        $this->_helper->flashMessenger->addMessage('User Profile removed successfully.');
        $this->_helper->redirector('index', 'index');
      }
    }

    // send form to view
    $this->view->removalForm = $removalForm;
  }

}
