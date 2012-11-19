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
 * This controller is reached when any error occured during the processing of the 
 * users request.
 */
class ErrorController extends Unplagged_Controller_Action{

  private $priority;

  /**
   * This action will mostly be autocalled when an error occurs during the processing of
   * the users request.
   */
  public function errorAction(){
    $errors = $this->_getParam('error_handler');
    
    //if the error controller gets called directly, we have no $errors
    //so we need to get out here
    if(!$errors || !$errors instanceof ArrayObject){
      $this->view->message = 'You have reached the error page.';
      return;
    }
    
    $this->handleErrorSpecifically($errors);
    $this->appendWebmasterMail();
    $this->logException($errors);
    $this->checkExceptionDisplay($errors);

    $this->view->request = $errors->request;
  }

  /**
   * Finds out whether the exception needs to be displayed.
   */
  private function checkExceptionDisplay($errors){
    if($this->getInvokeArg('displayExceptions') == true){
      $this->view->exception = $errors->exception;
    }
  }
  
  /**
   * Checks the error type and takes appropriate action.
   */
  private function handleErrorSpecifically($errors){
    switch($errors->type){
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        $this->notFoundError();
        break;
      default:
        $this->applicationError();
        break;
    }
  }
  
  /**
   * Looks up the webmaster email and appends it to the message displayed to the user.
   */
  private function appendWebmasterMail(){
    $registry = Zend_Registry::getInstance();
    $webmasterConfig = $registry->config->get('contact')->get('webmaster');
    $webmasterEmail = $webmasterConfig->get('email');
    
    if(!empty($webmasterEmail)){
      $this->view->message .= ' If you think this is a severe problem, please be so kind to contact our webmaster at: <a href="mailto:' . $webmasterEmail . '">' . $webmasterEmail . '</a>';
    }
  }
  
  /**
   * Send a 500 error page.
   */
  private function applicationError(){
    $this->setResponseData(500, Zend_Log::CRIT, '500 - Internal Server Error', "<strong>We are sorry, but we encountered an internal problem that couldn't be resolved.</strong>");
  }

  /**
   * Send a 404 error page.
   */
  private function notFoundError(){
    $this->setResponseData(404, Zend_Log::NOTICE, '404 - Not Found', "<strong>We are sorry, but we couldn't find the page you requested.</strong><br /><br />This is probably our fault, but just to make sure, please check if you spelled the URL correctly.");
  }

  /**
   * Set the data for the current response.
   * 
   * @param int $statusCode
   * @param string $priority
   * @param string $title
   * @param string $message
   */
  private function setResponseData($statusCode, $priority, $title, $message){
    $this->getResponse()->setHttpResponseCode($statusCode);
    $this->priority = $priority;
    $this->setTitle($title);
    $this->view->message = $message;
  }
  
  /**
   * Writes the exception into a logfile.
   * 
   * @param array|ArrayObject $errors
   */
  private function logException($errors){
    $log = $this->getLog();
    if($log){
      $log->log($this->view->message, $this->priority, $errors->exception);
      $log->log('Request Parameters', $this->priority, $errors->request->getParams());
    }
  }

  /**
   * Tries to find the logger object.
   */
  private function getLog(){
    $bootstrap = $this->getInvokeArg('bootstrap');
    if(!$bootstrap->hasResource('Log')){
      return false;
    }
    $log = $bootstrap->getResource('Log');
    return $log;
  }

}