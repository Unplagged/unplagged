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
 * The controller class handles all the user transactions as rights requests and user management.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 */
class TagController extends Unplagged_Controller_Action{

  public function indexAction(){
  }

  /**
   * Selects 5 users based on matching first and lastname with the search string and sends their ids as json string back.
   * @param String from If defined it selects only users of a specific rank.
   */
  public function autocompleteTitlesAction(){
    $search_string = $this->_getParam('term');
    // ids to skip
    $skipIds = $this->_getParam('skip');

    // no self select possible
    if(substr($skipIds, 0, 1) == ","){
      $skipIds = substr($skipIds, 1);
    }

    if($skipIds != ""){
      $skipIds = " AND t.id NOT IN (" . $skipIds . ")";
    }

    $qb = $this->_em->createQueryBuilder();
    $qb->add('select', "t.title as label, t.id AS value")
        ->add('from', 'Application_Model_Tag t')
        ->where("t.title LIKE '%" . $search_string . "%' " . $skipIds);
    $qb->setMaxResults(5);

    $dbresults = $qb->getQuery()->getResult();

    foreach($dbresults as $key=>$value){
      $results[] = $value;
    }
    $this->_helper->json($results);
  }

}
