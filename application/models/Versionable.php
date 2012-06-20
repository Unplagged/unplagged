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
 * Description of Versionable
 *
 * @Entity
 * @Table(name="versionables") 
 * @HasLifeCycleCallbacks
 */
abstract class Application_Model_Versionable extends Application_Model_Base{

  /**
   * The current version of the fragment.
   * @var integer The version.
   * 
   * @Column(type="integer", length=64)
   * @version
   */
  protected $version;

  /**
   * @OneToMany(targetEntity="Application_Model_Versionable_Version", mappedBy="versionable", cascade={"persist", "update", "remove"})
   */
  protected $auditLog;

  public function __construct(){
    parent::__construct();

    $this->auditLog = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * Sets the creation time to the current time, if it is null.
   * 
   * This will normally be auto called the first time the object is persisted by doctrine.
   * 
   * @PrePersist @PreUpdate
   */
  public function logVersion(){

    $versionableVersion = new Application_Model_Versionable_Version($this);
    //$versionableVersion->setVersionable($this);

    $this->auditLog->add($versionableVersion);
    $this->_em = Zend_Registry::getInstance()->entitymanager;
  }

  /**
   * 
   * @PostPersist @PostUpdate
   */
  public function persistVersions(){
    foreach($this->auditLog as $logEntry){
      $this->_em->persist($logEntry);
    }

    $this->_em->flush();
  }

  public function getCurrentVersion(){
    return !empty($this->version) ? ($this->version + 1) : 1;
  }

  public function setVersion($version){
    $this->version = $version;
  }

  public function getAuditLog(){
    return $this->auditLog;
  }
  
    abstract public function toVersionArray();


}
?>