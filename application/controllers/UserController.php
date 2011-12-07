<?php

/**
 * The controller class handles all the user transactions as rights requests and user management.
 *
 * @author Benjamin Oertel <benjamin.oertel@student.htw-berlin.de>
 * @version 1.0
 */
class UserController extends Zend_Controller_Action
{
    /**
     * Initalizes registry and namespace instance in the controller and allows to display flash messages in the view.
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $this->_em = Zend_Registry::getInstance()->entitymanager;
        $this->_defaultNamespace = new Zend_Session_Namespace('Default');
        $this->view->flashMessages = $this->_helper->flashMessenger->getMessages();
    }
    
    public function indexAction() {
        
    }
    
    /**
     * Displays a form for registering a user.
     */
    public function registerAction() {
        // create the form
        $registerForm = new Application_Form_User_Register();

        // form has been submitted through post request
        if($this->_request->isPost())
        {
            $formData = $this->_request->getPost();

            // if the form doesn't validate, pass to view and return
            if($registerForm->isValid($formData))
            {
                // create new user object
                $user = new Application_Model_User();
                $user->setUsername($this->getRequest()->getParam('username'));
                $user->setPassword(Unplagged_Helper::hashString($this->getRequest()->getParam('password')));
                $user->setEmail($this->getRequest()->getParam('email'));
                $user->setVerificationHash(Unplagged_Helper::generateRandomHash());
                $user->setState($this->_em->getRepository('Application_Model_User_State')->findOneByTitle('registered'));
                
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
    
    public function verifyAction() {        
        $verificationHash = preg_replace('/[^0-9a-z]/i', '', $this->getRequest()->getParam('hash'));
        
        // if no valid verification hash is set
        if(empty($verificationHash)) {
            $this->_helper->redirector('index', 'index');
        }
        
        $user = $this->_em->getRepository('Application_Model_User')->findOneByVerificationHash($verificationHash);
        if(empty($user) || $user->getState()->getTitle() != 'registered') {
            $this->_helper->flashMessenger->addMessage('Verification failed.');
            $this->_helper->redirector('index', 'index');
        } else {
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
    public function editAction() {
        $userId = preg_replace('/[^0-9]/', '', $this->getRequest()->getParam('id'));
        // if either the user is not logged in or no valid user id is defined
        if(empty($userId) || !$this->_defaultNamespace->user) {
            $this->_helper->redirector('index', 'index');
        }
        
        $user = $this->_em->getRepository('Application_Model_User')->findOneById($userId);
        if(empty($user)) {
            $this->_helper->flashMessenger->addMessage('User Profile saved successfully.');
            $this->_helper->redirector('index', 'index');
        } elseif($this->_defaultNamespace->user->getId() != $userId) {
            $this->_helper->flashMessenger->addMessage('No permission to edit other users.');
            $this->_helper->redirector('index', 'index');
        } else {
            // display the form with user data pre-loaded
            $profileForm = new Application_Form_User_Profile($userId);
            
            // form has been submitted through post request
            if($this->_request->isPost())
            {
                $formData = $this->_request->getPost();

                // if the form doesn't validate, pass to view and return
                if($profileForm->isValid($formData))
                {
                    // select the user and update the values
                    $user->setFirstname($this->getRequest()->getParam('firstname'));
                    $user->setLastname($this->getRequest()->getParam('lastname'));
                    
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
}
