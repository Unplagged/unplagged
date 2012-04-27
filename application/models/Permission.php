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
 * This class can be used to store the name of a permission.
 * 
 * The general idea is to generate all needed permissions either upon 
 * installation or the creation of a new resource, in order to be able
 * to fetch all and register them with the authorization module.
 * 
 * @author Unplagged
 * 
 * @Entity
 * @Table(name="permissions")
 * @UniqueEntity("name")
 */
class Application_Model_Permission{

  /**
   * @Id
   * @GeneratedValue
   * @Column(type="integer") 
   */
  private $id;

  /**
   * @Column(type="string", length=255, unique=true)
   */
  private $name;

  public function __construct($name){

    if(is_string($name) && trim($name) !== ''){
      $this->name = $name;
    }else{
      throw new InvalidArgumentException('The argument $name must be a string');
    }
  }

  public function getName(){
    return $this->name;
  }

}
?>
