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
      // JOIN b.permissions pg WITH pt.base IS NULL AND pe.action = ... AND pe.type = ... JOIN pg.roles WITH roleId = 'case' 
      $permissionStatement = " JOIN b.permissions pe WITH (pe.base = b.id AND pe.action = '%s') JOIN pe.roles re JOIN re.user u WHERE ";
      //$permissionStatement = " JOIN b.permissions pe WITH (pe.base = b.id AND pe.action = '%s' AND (:userRoleId MEMBER OF pe.roles OR :caseRoleId MEMBER OF pe.roles))";

      $permissionStatement = sprintf($permissionStatement, $permissionAction);
    }elseif(!empty($condition)){
      $permissionStatement = ' WHERE ';
    }
    // @todo: remove the condition below when the permission stuff is uncommented again
    /* if(!empty($condition)){
      $permissionStatement = ' WHERE ';
      } */
    echo $query . $permissionStatement . $condition . $orderBy;
    $this->query = $em->createQuery($query . $permissionStatement . $condition . $orderBy);
    //$this->query->setParameter('userRoleId', 5);
    //$this->query->setParameter('caseRoleId', 5);
    $this->countQuery = $em->createQuery($countQuery . $permissionStatement . $condition . $orderBy);
    //$this->countQuery->setParameter('userRoleId', 5);
    //$this->countQuery->setParameter('caseRoleId', 5);
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
