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

use UnpCommon\Controller\BaseController;

/**
 * Controller to serve the index and other mostly static pages.
 */
class IndexController extends BaseController{

  /**
   * Action for the static about page.
   */
  public function aboutAction(){
    
  }
  
  /**
   * Action for the static credits page.
   */
  public function creditsAction(){
    
  }
  
  /**
   * Page that shows contact information required in some countries.
   * 
   * You can enable or disable this with the config setting 
   * unp_settings.imprint_enabled.
   */
  public function imprintAction(){
    $config = $this->getServiceLocator()->get('Config');
    if($config['unp_settings']['imprint_enabled']){
      $imprintConfig = $config['contact'];
      $translator = $this->getServiceLocator()->get('translator'); 
      $this->setTitle($translator->translate('Imprint'));
      return $imprintConfig;
    }else{
      $this->getResponse()->setStatusCode(404);
      return; 
    }
  }

}
