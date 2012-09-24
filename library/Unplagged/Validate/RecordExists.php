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
