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
 * Implements Zend_Auth_Adapter_Interface to be used in conjuction with a Doctrine managed database. 
 * 
 * This class is based on {@link http://nopaste.info/9ebe309daf_nl.html}
 * 
 * @author TheQ, Unplagged 
 */
class Unplagged_Auth_Adapter_Doctrine implements Zend_Auth_Adapter_Interface{

  /**
   * The Entity/Classname which holds  the authentication data .
   * @var string 
   */
  private $authEntityName;

  /**
   * The Field/Variable name which represents the users identity e.g. username.
   * @var string 
   */
  private $authIdentityField;

  /**
   * The Field/Variable name which represents the users credentials e.g. the password.
   * @var string 
   */
  private $authCredentialField;

  /**
   * The identity to be checked, normally the user id. 
   * @var string 
   */
  private $identity;

  /**
   * The credentials to be checked, e.g. the password. 
   * @var string 
   */
  private $credential;

  /**
   * Instance of an EntityManager 
   * @var  
   */
  private $entityManager;

  public function __construct($em = null, $authEntityName = null, $authIdentityField = null, $authCredentialField = null, $identity = null, $credential = null){
    $this->authEntityName = $authEntityName;
    $this->authIdentityField = $authIdentityField;
    $this->authCredentialField = $authCredentialField;
    $this->identity = $identity;
    $this->credential = $credential;
    $this->entityManager = $em;
  }

  /**
   * @see Zend_Auth_Adapter_Interface::authenticate() 
   */
  public function authenticate(){
    $authEntity = $this->entityManager->getRepository($this->authEntityName)
        ->findOneBy(array(
      $this->authIdentityField=>$this->identity,
      $this->authCredentialField=>$this->credential
        ));

    if($authEntity !== null){
      return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $authEntity);
    }else{
      return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null);
    }
  }

}