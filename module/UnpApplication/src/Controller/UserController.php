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
namespace UnpApplication\Controller;

use \UnpCommon\Controller\BaseController;
use \UnpCommon\Factory\EntityFactory;
use \Zend\Form\Factory;
use \Zend\View\Model\ViewModel;

/**
 * Handles action related to users.
 */
class UserController extends BaseController{

  public function currentCaseAction(){
    $id = $this->params()->fromPost('case-id');
    $response = $this->getResponse();
    
    $user = $this->zfcUserAuthentication()->getIdentity();
    $case = null;
    
    if(!empty($id)){
      $case = $this->em->getRepository('\UnpCommon\Model\PlagiarismCase')->findOneById($id);
    }
    
    $user->setCurrentCase($case);
    $this->em->persist($user);
    $this->em->flush();
    
    //we have nothing to send here, result is transmitted by status code
    //No Content - success, but nothing to return
    $response->setStatusCode(204);
    $response->setContent('');
    $this->redirectToLastPage();
    return $response;
  }

}