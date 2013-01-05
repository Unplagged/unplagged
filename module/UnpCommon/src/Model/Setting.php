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
namespace UnpCommon\Model;

/**
 * This class can be used to store simple settings with an ini style key and their value. Mostly this should be
 * corresponded to the ZF2 config for this application, so that a user can change those values over the interface, 
 * which later on will be written to an autogerenerated config file.
 * 
 * For example if we want to change the Doctrine host, we would store a Setting with a key and value like this:
 * 
 *   key = "doctrine.connection.orm_default.params.host" value="localhost"
 *   
 * Which in turn could be parsed to:
 * 
 *   array('doctrine'=>array(
 *           'connection'=>array(
 *             'orm_default'=>array(
 *               'params'=>array(
 *                 'host'=>'localhost'
 *                )
 *              )
 *            )
 *          )
 *        )   
 * 
 * @Entity
 * @Table(name="setting")
 * @UniqueEntity("settingKey")
 */
class Setting{

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