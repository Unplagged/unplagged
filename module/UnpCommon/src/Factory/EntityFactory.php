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
namespace UnpCommon\Factory;

/**
 * Bundles common tasks when instantiating models for Doctrine
 */
class EntityFactory{
  
  /**
   * Tries to create an entity object that extends \UnpCommon\Model\Base.
   * 
   * This methods is useful, because it bundles common initalization tasks and removes the entitymanager dependency from
   * the model classes.
   * 
   * @param \Doctrine\ORM\EntityManager $em
   * @param string $type
   * @param array $params
   * @return \UnpCommon\Factory\type
   * @throws InvalidArgumentException
   */
  public static function createBaseEntity(\Doctrine\ORM\EntityManager $em, $type = '', array $params = array()){
    $entity = null;
    
    if(class_exists($type) && $type instanceof \UnpCommon\Model\Base){
      $entity = new $type();
      
      //use the state set by the method user or take the default one from the database
      if(!isset($params['state'])){
        $params['state'] = $em->getRepository('\UnpCommon\Model\State')->findOneByName('created');
      }
      $entity->setState($params['state']);
    } else {
      throw new \InvalidArgumentException('Class not found');
    }
    
    return $entity;
  }
}