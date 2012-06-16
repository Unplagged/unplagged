<?php

/**
 * Doctrine 2 compatible Paginator Adapter.
 * 
 * @author Benjamin Oertel <benjamin.oertel@me.com>
 * @version 1.0
 */
class Unplagged_Paginator_Adapter_DoctrineQuery implements Zend_Paginator_Adapter_Interface{

  protected $query;
  protected $countQuery;

  public function __construct($query, $countQuery, $additionalConditions = array(), $orderBy = null, $permissionType = null, $permissionAction = null){
    $em = Zend_Registry::getInstance()->entitymanager;
    $user = Zend_Registry::getInstance()->user;

    $conditions = array();

    if(isset($permissionAction)){
      $conditions[] = 'u.id = ' . $user->getId();
    }
    if(isset($additionalConditions)){
      foreach($additionalConditions as $field=>$value){
        $conditions[] = $field . " = '" . $value . "'";
      }
    }
    $condition = implode(' AND ', $conditions);
    $orderBy = isset($orderBy) ? ' ORDER BY ' . $orderBy : '';
    $permissionStatement = '';
    if(isset($permissionAction)){
      $permissionStatement = " JOIN b.permissions pe WITH (pe.base = b.id AND pe.action = '%s') JOIN pe.roles re JOIN re.user u WHERE ";
      $permissionStatement = sprintf($permissionStatement, $permissionAction);
    }elseif(!empty($condition)){
      $permissionStatement = ' WHERE ';
    }
    // @todo: remove the condition below when the permission stuff is uncommented again
    /* if(!empty($condition)){
      $permissionStatement = ' WHERE ';
      } */
    $this->query = $em->createQuery($query . $permissionStatement . $condition . $orderBy);
    $this->countQuery = $em->createQuery($countQuery . $permissionStatement . $condition . $orderBy);
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
