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

  public function init(){
    parent::init();
    $this->auth = Zend_Auth::getInstance();

    Zend_Layout::getMvcInstance()->sidebar = 'default';
    Zend_Layout::getMvcInstance()->cases = $this->_em->getRepository('Application_Model_Case')->findAll();
  }

  public function indexAction(){
    
  }

  /**
   * Handles the registration data or displays a form for registering a user.
   */
  public function registerAction(){
    // create the form
    $registerForm = new Application_Form_User_Register();

    // form has been submitted through post request
    if($this->_request->isPost()){
      $this->handleRegistration($registerForm);
    }

    // send form to view
    $this->view->registerForm = $registerForm;
  }

  private function handleRegistration(Application_Form_User_Register $registerForm){
    $formData = $this->_request->getPost();

    // if the form doesn't validate, pass to view and return
    if($registerForm->isValid($formData)){
      $user = $this->createNewUserFromFormData($formData);

      // log registration
      Unplagged_Helper::notify('user_registered', $user, $user);

      $config = Zend_Registry::get('config');
      $locale = Zend_Registry::get('Zend_Locale');
      $languageString = $locale->getLanguage();
      $mailer = new Unplagged_Mailer('registration.phtml', $languageString);
      $subject = Zend_Registry::get('Zend_Translate')->translate('%s: Registration verification required');

      try{
        $mailer->sendMail($user, sprintf($subject, $config->default->applicationName));
        $this->_helper->FlashMessenger(array('success'=>'Please check your emails to verify your account.'));
        $this->_helper->redirector('index', 'index');
      }catch(Zend_Mail_Transport_Exception $e){
        $this->_helper->FlashMessenger(array('error'=>'Sorry, we were unable to send a verification mail, please try again later'));
        Zend_Registry::get('Log')->err('Unable to send mail for user ' . $user->getUsername() . '.');
      }
    }else{
      //set filled and valid data into the form
      $registerForm->populate($this->_request->getPost());
    }
  }

  private function createNewUserFromFormData(array $formData){
    $data = array();
    $data['username'] = $formData['username'];
    $data['password'] = Unplagged_Helper::hashString($formData['password']);
    $data['email'] = $formData['email'];
    $data['verificationHash'] = Unplagged_Helper::generateRandomHash();
    $data['state'] = $this->_em->getRepository('Application_Model_State')->findOneByName('user_registered');

    // @todo: change to global roleId user, when implemented
    $roleTemplate = $this->_em->getRepository('Application_Model_User_Role')->findOneBy(array('roleId'=>'user', 'type'=>'global'));
    $role = new Application_Model_User_Role();
    $role->setType('user');
    foreach($roleTemplate->getPermissions() as $permission){
      $role->addPermission($permission);
    }
    $adminRole = $this->_em->getRepository('Application_Model_User_Role')->findOneBy(array('roleId'=>'admin', 'type'=>'global'));
    $role->addInheritedRole($adminRole);

    $this->_em->persist($role);
    $data['role'] = $role;

    $user = new Application_Model_User($data);

    // write back to persistence manager and flush it
    $this->_em->persist($user);
    $this->_em->flush();
    $role->setRoleId($user->getId());
    $this->_em->persist($role);
    $this->_em->flush();

    return $user;
  }

  public function filesAction(){
    $input = new Zend_Filter_Input(array('page'=>'Digits'), null, $this->_getAllParams());

    $this->setTitle('Personal Files');

    $user = Zend_Registry::getInstance()->user;
    $userFiles = $user->getFiles();

    $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($userFiles->toArray()));
    $paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    $paginator->setCurrentPageNumber($input->page);

    // generate the action dropdown for each file
    // @todo: use centralised method for all three file lists
    foreach($paginator as $file){
      $file->actions = array();

      $action['link'] = '/file/parse/id/' . $file->getId();
      $action['label'] = 'Parse';
      $action['icon'] = 'images/icons/page_gear.png';
      $file->actions[] = $action;

      $action['link'] = '/file/download/id/' . $file->getId();
      $action['label'] = 'Download';
      $action['icon'] = 'images/icons/disk.png';
      $file->actions[] = $action;
      $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'file', 'action'=>'delete', 'base'=>$file));
      if(Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
        $action['link'] = '/file/delete/id/' . $file->getId();
        $action['label'] = 'Delete';
        $action['icon'] = 'images/icons/delete.png';
        $file->actions[] = $action;
      }
      $action['link'] = '/case/add-file/id/' . $file->getId();
      $action['label'] = 'Add to current case';
      $action['icon'] = 'images/icons/package_add.png';
      $file->actions[] = $action;


      $permission = $this->_em->getRepository('Application_Model_ModelPermission')->findOneBy(array('type'=>'file', 'action'=>'authorize', 'base'=>$file));
      if(Zend_Registry::getInstance()->user->getRole()->hasPermission($permission)){
        $action['link'] = '/permission/edit/id/' . $file->getId();
        $action['label'] = 'Set permissions';
        $action['icon'] = 'images/icons/shield.png';
        $file->actions[] = $action;
      }
    }

    $this->view->paginator = $paginator;
    $this->view->uploadLink = '/file/upload?area=personal';

    //change the view to the one from the file controller
    $this->_helper->viewRenderer->renderBySpec('list', array('controller'=>'file'));
    Zend_Layout::getMvcInstance()->sidebar = null;
    Zend_Layout::getMvcInstance()->cases = null;
  }

  public function addFileAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    $file = $this->_em->getRepository('Application_Model_File')->findOneById($input->id);
    if($file){
      $user = Zend_Registry::getInstance()->user;

      if(!$user->hasFile($file)){
        $user->addFile($file);
        $this->_em->persist($user);
        $this->_em->flush();
        $this->_helper->FlashMessenger(array('success'=>'The file has successfully been added to your personal files.'));
      }else{
        $this->_helper->FlashMessenger(array('error'=>'The file already belongs to your personal files.'));
      }
    }else{
      $this->_helper->FlashMessenger(array('error'=>'The specified file does not exist.'));
    }

    $this->_helper->viewRenderer->setNoRender(true);
    $this->redirectToLastPage();
  }

  /**
   * Verifies a user by a given hash in database.
   */
  public function verifyAction(){
    $input = new Zend_Filter_Input(array('hash'=>'Alnum'), null, $this->_getAllParams());

    // if no valid verification hash is set
    if(empty($input->hash)){
      $this->_helper->redirector('index', 'index');
    }

    $user = $this->_em->getRepository('Application_Model_User')->findOneByVerificationHash($input->hash);
    if(empty($user) || $user->getState()->getName() != 'user_registered'){
      $this->_helper->FlashMessenger('Verification failed.');
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

      $this->_helper->FlashMessenger('Verification finished successfully.');
      $this->_helper->redirector('index', 'index');
    }
  }

  /**
   * Recovers a users password
   */
  public function recoverPasswordAction(){
    $input = new Zend_Filter_Input(array('hash'=>'Alnum'), null, $this->_getAllParams());

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

          $lastNotificationAction = $this->_em->getRepository('Application_Model_Action')->findOneByName("user_requested_password");
          $lastNotification = $this->_em->getRepository('Application_Model_Notification')->findOneBy(array("action"=>$lastNotificationAction->getId(), "user"=>$user->getId()));

          if($lastNotification && ($lastNotification->getCreated()->getTimestamp() > time() - NOTIFICATIONS_TIME_INTERVAL)){
            $this->_helper->FlashMessenger('There was already a password recovery request for this account.');
            $this->_helper->redirector('recover-password', 'user');
          }else{
            Unplagged_Mailer::sendPasswordRecoveryMail($user);
            Unplagged_Helper::notify("user_requested_password", $user, $user);

            $this->_helper->FlashMessenger('An E-Mail has been sent to your address, follow the instructions in this mail.');
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

          $this->_helper->FlashMessenger('Your password has been reset successfully, you can login now.');
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
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    // if either the user is not logged in or no valid user id is defined
    if(empty($input->id)){
      $input->id = $this->_defaultNamespace->userId;
    }

    $user = $this->_em->getRepository('Application_Model_User')->findOneById($input->id);
    if(empty($user)){
      $this->_helper->FlashMessenger('User Profile saved successfully.');
      $this->_helper->redirector('index', 'index');
    }elseif($this->_defaultNamespace->userId != $input->id){
      $this->_helper->FlashMessenger('No permission to edit other users.');
      $this->_helper->redirector('index', 'index');
    }else{
      // display the form with user data pre-loaded
      $profileForm = new Application_Form_User_Profile($input->id);

      // form has been submitted through post request
      if($this->_request->isPost()){
        $formData = $this->_request->getPost();

        // if the form doesn't validate, pass to view and return
        if($profileForm->isValid($formData)){
          // select the user and update the values
          $user->setFirstname($this->getRequest()->getParam('firstname'));
          $user->setLastname($this->getRequest()->getParam('lastname'));

          // write back to persistence manage and flush it
          $this->_em->persist($user);
          $this->_em->flush();

          Unplagged_Helper::notify("user_updated_profile", $user, $user);

          $this->_helper->FlashMessenger('User Profile saved successfully.');
          $this->_helper->redirector('index', 'index');
        }
      }

      // send form to view
      $this->view->profileForm = $profileForm;
    }
    Zend_Layout::getMvcInstance()->sidebar = null;
    Zend_Layout::getMvcInstance()->cases = null;
  }

  /**
   * Sets the current Case of this User based on the request parameter 'case' if the user has
   * the permission for the case. 
   */
  public function setCurrentCaseAction(){
    $input = new Zend_Filter_Input(array('case'=>'Digits'), null, $this->_getAllParams());
    $user = Zend_Registry::get('user');
    //die($input->case);
    $case = null;
    if($input->case){
      $case = $this->_em->getRepository('Application_Model_Case')->findOneById($input->case);
    }

    $user->setCurrentCase($case);

    //only persist the current case if we have no guest
    if($user->getUsername() !== 'guest'){
      $this->_em->persist($user);
      $this->_em->flush();
    }else{
      //otherwise store it in the session
      $defaultNamespace = new Zend_Session_Namespace('Default');
      if($case){
        $defaultNamespace->case = $case->getId();
      }else{
        $defaultNamespace->case = '';
      }
    }

    $this->redirectToLastPage();
  }

  /**
   * Selects 5 users based on matching first and lastname with the search string and sends their ids as json string back.
   * @param String from If defined it selects only users of a specific rank.
   */
  public function autocompleteAction(){
    $input = new Zend_Filter_Input(array('term'=>'Alnum', 'case'=>'Digits', 'skip'=>'StringTrim'), null, $this->_getAllParams());

    if(!empty($input->skip)){
      $input->skip = ' AND u.id NOT IN (' . $input->skip . ')';
    }
    $caseCondition = '';
    if(!empty($input->case)){
      $caseCondition = ' :caseId MEMBER OF u.cases AND';
    }

    // skip has to be passed in directly and can't be set as a parameter due to a doctrine bug
    $query = $this->_em->createQuery("SELECT u.id value, u.username label, r.id role FROM Application_Model_User u JOIN u.role r WHERE " . $caseCondition . " u.username LIKE :term" . $input->skip);
    $query->setParameter('term', '%' . $input->term . '%');
    if(!empty($input->case)){
      $query->setParameter('caseId', $input->case);
    }
    $query->setMaxResults(5);

    $result = $query->getArrayResult();
    $this->_helper->json($result);
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
        $this->_helper->FlashMessenger('User Profile removed successfully.');
        $this->_helper->redirector('index', 'index');
      }
    }

    // send form to view
    $this->view->removalForm = $removalForm;
  }

  public function editRoleAction(){
    $this->view->roleForm = new Application_Form_User_Role();
  }

}
