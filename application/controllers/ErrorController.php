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
 * 
 */
class ErrorController extends Unplagged_Controller_Action{

  public function errorAction(){
    Zend_Layout::getMvcInstance()->sidebar = null;
    
    $errors = $this->_getParam('error_handler');

    if(!$errors || !$errors instanceof ArrayObject){
      $this->view->message = 'You have reached the error page';
      return;
    }

    switch($errors->type){
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        // 404 error -- controller or action not found
        $this->getResponse()->setHttpResponseCode(404);
        $priority = Zend_Log::NOTICE;
        $this->setTitle('404 - Not Found');
        $this->view->message = "<strong>We are sorry, but we couldn't find the page you requested.</strong><br /><br />This is probably our fault, but just to make sure, please check if you spelled the URL correctly.";
        
        
        break;
      default:
        // application error
        $this->getResponse()->setHttpResponseCode(500);
        $priority = Zend_Log::CRIT;
        $this->setTitle('500 - Internal Server Error');
        $this->view->message = "<strong>We are sorry, but we encountered an internal problem that couldn't be resolved.</strong>";
        break;
    }

    $registry = Zend_Registry::getInstance();
    $webmasterConfig = $registry->config->get('contact')->get('webmaster');
    $webmasterEmail = $webmasterConfig->get('email');
    
    if(!empty($webmasterEmail)){
      $this->view->message .= ' If you think this is a severe problem, please notify our webmaster at: <a href="mailto:' . $webmasterEmail . '">' . $webmasterEmail . '</a>';
    }
    
    // Log exception, if logger available
    if($log = $this->getLog()){
      $log->log($this->view->message, $priority, $errors->exception);
      $log->log('Request Parameters', $priority, $errors->request->getParams());
    }

    // conditionally display exceptions
    if($this->getInvokeArg('displayExceptions') == true){
      $this->view->exception = $errors->exception;
    }

    $this->view->request = $errors->request;
  }

  public function getLog(){
    $bootstrap = $this->getInvokeArg('bootstrap');
    if(!$bootstrap->hasResource('Log')){
      return false;
    }
    $log = $bootstrap->getResource('Log');
    return $log;
  }

}