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
class IndexController extends Unplagged_Controller_Action{

  public function init(){
    parent::init();

    Zend_Layout::getMvcInstance()->sidebar = 'default';
    Zend_Layout::getMvcInstance()->cases = $this->_em->getRepository("Application_Model_Case")->findAll();
  }

  public function indexAction(){
    //Zend_Registry::get('Log')->debug('Index');
    $registry = Zend_Registry::getInstance();
    $user = $registry->user;

    Zend_Layout::getMvcInstance()->sidebar = null;
    foreach(Zend_Layout::getMvcInstance()->cases as $case){
      //$case = $user->getCurrentCase();
      if($case){
        $barcode = $case->getBarcode(100, 150, 100, true, '%');
        if($barcode){
          $this->view->currentCase = '<h4>' . $registry->Zend_Translate->translate('Barcode for') . ' "' . $case->getPublishableName() . '"</h4>';
          $this->view->barcode .= $barcode->render();
        }
      }
    }
  }

  /**
   * Used to render an empty page when the user is not allowed to access the actual data. 
   */
  public function emptyAction(){
    $this->_helper->viewRenderer->setNoRender(true);
    Zend_Layout::getMvcInstance()->sidebar = null;
  }

  public function imprintAction(){
    $registry = Zend_Registry::getInstance();
    $imprintConfig = $registry->config->get('imprint');
    
    $this->view->address = $imprintConfig->get('address');
    $this->view->telephone = $imprintConfig->get('telephone');
    $this->view->email = $imprintConfig->get('email');
    
    $this->setTitle($registry->get('Zend_Translate')->translate('Imprint'));
    Zend_Layout::getMvcInstance()->sidebar = null;
  }

}
