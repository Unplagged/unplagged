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
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Controller\BaseController;

/**
 * Controller to serve the index and other mostly static pages.
 */
class IndexController extends BaseController{
  
  public function indexAction(){
    $barcodes = array();
    //$this->translate('Cases');
    //$cases = $this->em->getRepository("\Application\Model\PlagCase")->findAll();
    //var_dump($cases);
    /*


    foreach($cases as $case){
      //$case = $user->getCurrentCase();
      if($case->getState() && $case->getState()->getName() === 'published'){
        $barcode = $case->getBarcode(100, 150, 100, true, '%');
        if($barcode){
          $barcodes[] = array(
              'graphic'=>$barcode->render(),
              'title'=>$registry->Zend_Translate->translate('Barcode for') . ' "' . $case->getPublishableName() . '"'
          );
        }
      }
    }*/
    $translator = $this->getServiceLocator()->get('translator');
    $this->flashMessenger()->setNamespace('success')->addMessage($translator->translate('The case was created successfully.'));
    return array('barcodes'=>$barcodes);
  }

  /**
   * Used to render an empty page when the user is not allowed to access the actual data. This is necessary in order
   * to always at least show some error message, when really nothing is allowed.
   */
  public function emptyAction(){
    $this->_helper->viewRenderer->setNoRender(true);
  }

  /**
   * Page that shows contact information required at least by german law. 
   */
  public function imprintAction(){
    $registry = Zend_Registry::getInstance();
    $imprintConfig = $registry->config->get('contact')->get('imprint');

    $this->view->address = $imprintConfig->get('address');
    $this->view->telephone = $imprintConfig->get('telephone');
    $this->view->email = $imprintConfig->get('email');

    $this->setTitle($registry->get('Zend_Translate')->translate('Imprint'));
  }

}
