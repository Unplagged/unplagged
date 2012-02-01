<?php

/**
 * Doctrine 2 compatible Paginator Adapter.
 * 
 * @author Benjamin Oertel <benjamin.oertel@me.com>
 * @version 1.0
 */
class Unplagged_Paginator_Adapter_DoctrineQuery implements Zend_Paginator_Adapter_Interface
{
    protected $_query;
    protected $_count_query;
    
    public function __construct($query, $_count_query)
    {
        $this->_query = $query;
        $this->_count_query = $_count_query;
    }
    
    /**
     * Selects the currently shown elements.
     * 
     * @param $offset integer starting point to select elements from
     * @param $itemsPerPage integer how many elements are displayed at once
     * @see Zend_Paginator_Adapter_Interface::getItems()
     */
    public function getItems($offset, $itemsPerPage)
    { 
        return $this->_query
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($offset)
            ->getResult();
    }
    
    /**
     * Counts all elements that match the query without limits.
     * 
     * @see Countable::count()
     */
    public function count()
    {	
        return $this->_count_query->getSingleScalarResult();
    }
}
