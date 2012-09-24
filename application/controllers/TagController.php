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
 * The controller class handles tag related stuff.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 */
class TagController extends Unplagged_Controller_Action{

  public function indexAction(){
  }

  public function autocompleteAction(){
    $input = new Zend_Filter_Input(array('term'=>'Alnum', 'skip'=>'StringTrim'), null, $this->_getAllParams());
    
    if(!empty($input->skip)) {
      $input->skip = ' AND t.id NOT IN (' . $input->skip . ')';
    }
    
    // skip has to be passed in directly and can't be set as a parameter due to a doctrine bug
    $query = $this->_em->createQuery("SELECT t.id value, t.title label FROM Application_Model_Tag t WHERE t.title LIKE :term" . $input->skip);
    $query->setParameter('term', '%' . $input->term . '%');
    $query->setMaxResults(5);
    
    $result = $query->getArrayResult();
    $this->_helper->json($result);
  }

}
