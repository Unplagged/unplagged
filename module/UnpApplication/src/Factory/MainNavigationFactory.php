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
namespace UnpApplication\Factory;

use \RecursiveIteratorIterator;
use \Zend\Navigation\Service\DefaultNavigationFactory;
use \Zend\ServiceManager\ServiceLocatorInterface;

/**
 * This factory enablees 'main' as a valid key for the navigation helper.
 */
class MainNavigationFactory extends DefaultNavigationFactory{

  public function getName(){
    return 'main';
  }

  /**
   * Sets all necessary variable parameters for the url creation. This could for
   * example be the id of the currently selected case.
   * 
   * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
   * @return \Zend\Navigation\Navigation
   */
  public function createService(ServiceLocatorInterface $serviceLocator){
    $navigation = parent::createService($serviceLocator);

    $currentCase = $serviceLocator->get('zfcuserauthservice')->getIdentity()->getCurrentCase();
    
    //set actual values for the placeholder params, for example the id of the current case
    $iterator = new RecursiveIteratorIterator($navigation, RecursiveIteratorIterator::SELF_FIRST);
    foreach($iterator as $page){
      $params = $page->get('params');
      if($params){
        foreach($params as $key=>$param){
          switch($key){
            case 'case_id':
              $params[$key]= $currentCase->getId();
              break;
          }
        }
         $page->setParams($params);
      }
    }

    return $navigation;
  }

}