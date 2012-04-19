<?php

/*
 * Validator for checking that a record does not exist yet.
 */

/**
 * This validator helps to check if a specific column in the databse contains a specific record already.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 */
class Unplagged_Validate_RecordExists extends Zend_Validate_Abstract{

  private $_table;
  private $_field;
  private $_conditions;

  const OK = '';

  protected $_messageTemplates = array(
    self::OK=>"'%value%' could not be found in the database."
  );

  public function __construct($table, $field, $conditions = array()){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
    $this->_table = $table;
    $this->_field = $field;
    $this->_conditions = $conditions;
  }

  public function isValid($value){
    $this->_setValue($value);

    $this->_conditions = array_merge(array($this->_field => $value), $this->_conditions);
    
    $element = $this->_em->getRepository($this->_table)->findBy($this->_conditions);

    if(!empty($element)){
      return true;
    } else {
      $this->_error(self::OK);
      return false;
    }
  }

}
