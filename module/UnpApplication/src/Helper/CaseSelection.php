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
namespace UnpApplication\Helper;

use \Doctrine\ORM\EntityManager;
use \UnpCommon\Model\PlagiarismCase;
use \Zend\Mvc\Controller\Plugin\FlashMessenger;
use \Zend\View\Helper\AbstractHelper;

/**
 * View helper that can be used to print a case selector to the user.
 */
class CaseSelection extends AbstractHelper{

  private $entityManager;

  public function setEntityManager(EntityManager $entityManager){
    $this->entityManager = $entityManager;
  }

  /**
   * Creates a selection form with all existing case.
   * 
   * @param \UnpModel\Model\PlagiarismCase $currentCase
   * @return string
   */
  public function __invoke(PlagiarismCase $currentCase = null){
    $cases = $this->entityManager->getRepository('\UnpCommon\Model\PlagiarismCase')->findAll();
    
    $form = '<form class="current-case form-inline pull-left" method="POST" action="' . $this->view->basePath('user/current-case') . '">' . PHP_EOL;
    $form .= '<select data-placeholder="' . $this->view->translate('Select a Case') . '" name="case-id">' . PHP_EOL;
    $form .= '<option></option>' . PHP_EOL;
    
    foreach($cases as $case){
      if($currentCase && $case->getId() === $currentCase->getId()){
        $form .= '<option selected="selected" value="' . $case->getId() . '">' . $case->getPublishableName() . '</option>' . PHP_EOL;
      }else{
        $form .= '<option value="' . $case->getId() . '">' . $case->getPublishableName() . '</option>' . PHP_EOL;
      }
    }

    $form .= '<button class="btn">' . $this->view->translate('OK') . '</button>' . PHP_EOL;
    $form .= '</select>' . PHP_EOL;
    $form .= '</form>' . PHP_EOL;
    return $form;
  }

}