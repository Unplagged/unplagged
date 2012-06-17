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

  public function __construct($query, $countQuery, $additionalConditions = array(), $orderBy = null, Application_Model_ModelPermission $readAllPermission = null){
    $em = Zend_Registry::getInstance()->entitymanager;
    $user = Zend_Registry::getInstance()->user;

    $conditions = array();

    if(isset($additionalConditions)){
      foreach($additionalConditions as $field=>$value){
        $conditions[] = $field . " = '" . $value . "'";
      }
    }

    $permissionStatement = '';
    if(isset($readAllPermission)){
      // 1) check if the user has the right to see all elements , then we do not have to check permission on each file
      $canAccessAll = $user->getRole()->hasPermission($readAllPermission);

      // 2) if not, check permission on each file
      if(!$canAccessAll){
        if($readAllPermission->getAction()){
          //$permissionStatement = " JOIN b.permissions pe WITH (pe.base = b.id AND pe.action = '%s') JOIN pe.roles re JOIN re.user u ";
          $permissionStatement = " JOIN b.permissions pe WITH (pe.base = b.id AND pe.action = :permissionAction AND :roleId MEMBER OF pe.roles)";

          $permissionStatement = sprintf($permissionStatement, $readAllPermission->getAction());
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
