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
 * This class can be used to store simple settings with an ini style key and their value.
 * 
 * @Entity
 * @Table(name="setting")
 * @UniqueEntity("key")
 */
class Application_Model_Setting{

  /**
   * @var int
   * @Id @GeneratedValue @Column(type="integer")
   */
  private $id;

  /**
   * @var string
   * @Column(type="string", unique=true, length=255)
   */
  private $settingKey;

  /**
   * @var string 
   * 
   * @Column(type="string", length=255)
   */
  private $value = '';

  /**
   * @var string A label for display purposes.
   * 
   * @Column(type="string", length=255)
   */
  private $label = '';
  
  public function __construct($settingKey, $value='', $label=''){
      $this->settingKey = $settingKey;
      $this->value = $value;
      $this->label = $label;
  }
  
  public function getSettingKey(){
    return $this->settingKey;
  }

  public function getValue(){
    return $this->value;
  }

  public function setValue($value){
    $this->value = $value;  
  }
  
  public function getLabel(){
    return $this->label;  
  }
  
  public function setLabel($label){
    $this->label = $label;  
  }
}