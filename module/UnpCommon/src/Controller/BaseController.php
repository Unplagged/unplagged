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
namespace UnpCommon\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * This class bundles common functions used by many of the Unplagged controllers.
 */
abstract class BaseController extends AbstractActionController{

  protected $em;
  protected $translator;
  
  public function getTranslator(){
    if(!$this->translator){
      $this->translator = $this->getServiceLocator()->get('translator');
    }
    
    return $this->translator;
  }
  
  public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager){
    $this->em = $entityManager;
  }

  /**
   * Sets the page title to the given value and prepends it to the head title.
   * 
   * @param string $title 
   */
  protected function setTitle($title, $values = array()){
    /* 
      $title = vsprintf($title, $values);
      $this->view->title = $title;
      $this->view->headTitle()->prepend($title); */
  }

  /**
   * Looks up the session data and redirects the user to the page that was visited before. 
   * 
   * If the last url is the same as the current the default page is the activity stream.
   */
  protected function redirectToLastPage($permissionDenied = false){

    if($permissionDenied){
      $this->flashMessenger()->setNamespace('error')->addMessage('Permission denied.');
    }
    $historySessionNamespace = new \Zend\Session\Container('history');

    //check if the last url is the same as the current to avoid infinite loop
    if($historySessionNamespace->last !== $this->getRequest()->getUriString()){
      $this->redirect()->toUrl($historySessionNamespace->last);
    }else{
      //if we don't know where the user wants to go, here is the default:
      $this->redirect()->toRoute('home');
    }
  }

}