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

    Zend_Layout::getMvcInstance()->sidebar = null;
  }

  public function indexAction(){
    $registry = Zend_Registry::getInstance();
    $cases = $this->_em->getRepository("Application_Model_Case")->findAll();

    $barcodes = array();

    foreach($cases as $case){
      //$case = $user->getCurrentCase();
      $barcode = $case->getBarcode(100, 150, 100, true, '%');
      if($barcode){
        $barcodes[] = array(
          'graphic'=>$barcode->render(),
          'title' => $registry->Zend_Translate->translate('Barcode for') . ' "' . $case->getPublishableName() . '"'
        );
      }
    }
    $this->view->barcodes = $barcodes;
  }

  /**
   * Used to render an empty page when the user is not allowed to access the actual data. 
   */
  public function emptyAction(){
    $this->_helper->viewRenderer->setNoRender(true);
  }

  public function imprintAction(){
    $registry = Zend_Registry::getInstance();
    $imprintConfig = $registry->config->get('imprint');

    $this->view->address = $imprintConfig->get('address');
    $this->view->telephone = $imprintConfig->get('telephone');
    $this->view->email = $imprintConfig->get('email');

    $this->setTitle($registry->get('Zend_Translate')->translate('Imprint'));
  }

}
