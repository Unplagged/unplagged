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
 * Doctrine 2 compatible Paginator Adapter.
 * 
 * @author Benjamin Oertel <benjamin.oertel@me.com>
 * @version 1.0
 */
class Unplagged_Paginator_Adapter_DoctrineQuery implements Zend_Paginator_Adapter_Interface{

  protected $query;
  protected $countQuery;

  public function __construct($query, $countQuery, $additionalConditions = array(), $orderBy = null, Application_Model_ModelPermission $readAllPermission = null, $selectRemovedItems = false){
    $em = Zend_Registry::getInstance()->entitymanager;
    $user = Zend_Registry::getInstance()->user;

    $permissionStatement = '';

    $conditions = array();
    if(!$selectRemovedItems){
      $conditions[] = "s.name != 'deleted'";
      $permissionStatement .= ' JOIN b.state s';
    }
    if(isset($additionalConditions)){
      foreach($additionalConditions as $field=>$value){
        if($value != 'IS NULL'){
          if(!is_array($value)){
            $conditions[] = $field . " = '" . $value . "'";
          }elseif(count($value) == 2){
            $conditions[] = $field . sprintf(" %s '%s'", $value[0], $value[1]);
          }
        }else{
          $conditions[] = $field . " IS NULL";
        }
      }
    }

    if(isset($readAllPermission)){
      // 1) check if the user has the right to see all elements , then we do not have to check permission on each file
      $canAccessAll = $user->getRole()->hasPermission($readAllPermission);

      // 2) if not, check permission on each file
      if(!$canAccessAll){
        if($readAllPermission->getAction()){
          $permissionQuery = " JOIN b.permissions pe WITH (pe INSTANCE OF Application_Model_ModelPermission AND pe.base = b.id AND pe.action = :permissionAction AND :roleId MEMBER OF pe.roles)";

          $permissionStatement .= sprintf($permissionQuery, $readAllPermission->getAction());
        }
      }
    }
    $condition = !empty($conditions) ? ' WHERE ' . implode(' AND ', $conditions) : '';
    $orderBy = isset($orderBy) ? ' ORDER BY ' . $orderBy : '';

    $this->query = $em->createQuery($query . $permissionStatement . $condition . $orderBy);
    $this->countQuery = $em->createQuery($countQuery . $permissionStatement . $condition . $orderBy);
    if(isset($readAllPermission) && !$canAccessAll){
      $this->query->setParameter('permissionAction', $readAllPermission->getAction());
      $this->query->setParameter('roleId', $user->getRole()->getId());
      $this->countQuery->setParameter('permissionAction', $readAllPermission->getAction());
      $this->countQuery->setParameter('roleId', $user->getRole()->getId());
    }
  }

  /**
   * Selects the currently shown elements.
   * 
   * @param $offset integer starting point to select elements from
   * @param $itemsPerPage integer how many elements are displayed at once
   * @see Zend_Paginator_Adapter_Interface::getItems()
   */
  public function getItems($offset, $itemsPerPage){
    return $this->query
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($offset)
            ->getResult();
  }

  /**
   * Counts all elements that match the query without limits.
   * 
   * @see Countable::count()
   */
  public function count(){
    return $this->countQuery->getSingleScalarResult();
  }

}
