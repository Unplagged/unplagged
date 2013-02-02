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
namespace UnpCommon\Repository;

use \UnpCommon\Repository\BaseRepository;

/**
 * 
 */
class ActivityRepository extends BaseRepository{
  
  public function findByPage($pageNumber = 0){
    $query = 'SELECT n FROM \UnpCommon\Model\Activity n JOIN n.permissionSource b';
    $count = 'SELECT COUNT(n.id) FROM Application_Model_Notification n JOIN n.permissionSource b';
    return $this->_em->createQuery($query)->getResult();
        //$paginator->setItemCountPerPage(Zend_Registry::get('config')->paginator->itemsPerPage);
    //$paginator->setCurrentPageNumber($input->page);
  }
  
  public function findAllOrderedByCreated(){
    return $this->getEntityManager()
            ->createQuery('SELECT a FROM \UnpCommon\Model\Activity a ORDER BY a.created DESC')
            ->getResult();
  }
  
  
}