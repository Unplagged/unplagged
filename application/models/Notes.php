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
 * The class represents a file.
 * It defines also the structure of the database table for the ORM.
 *
 * @author unplagged <unplagged@benjaminoertel.com>
 * @version 1.0
 * 
 * @Entity 
 * @Table(name="notes")
 * @HasLifeCycleCallbacks
 */

class Application_Model_Notes{
 
   /**
   * The notesId is an unique identifier for each file.
   * @var string The notesId.
   * 
   * @Id @GeneratedValue @Column(type="integer")
   */
    protected $notesId;
    
    /**
   * The userId is an unique identifier for each user.
   * @var string The userId.
   * 
   * @Id @Column(type="integer")
   */
    protected $userId;
    
   /**
   * The text of the notes.
   * @var string The notes.
   * 
   * @Column(type="string", length=255)
   */
    protected $notes;
    
   /**
   * The caseId is an unique identifier for each case.
   * @var string The caseId.
   * 
   * @Id @Column(type="integer")
   */
    protected $caseId;
    
    public function __construct($data = array()){
    if(isset($data["userId"])){
      $this->userId = $data["userId"];
    }

    if(isset($data["notes"])){
      $this->notes = $data["notes"];
    }

    if(isset($data["caseId"])){
      $this->caseId = $data["caseId"];
    }
  }

  public function getCaseId() {
      return $this->caseId;
  }

  public function getUserId() {
      return $this->userId;
  }
  
  public function getNotes() {
      return $this->notes;
  }
  
  public function setNotesId($notesId){
    $this->notesId = $notesId;
  }
  
  public function setCaseId($caseId){
    $this->caseId = $caseId;
  }

  public function setUserId($userId){
    $this->userId = $userId;
  }

  public function setNotes($notes){
    $this->notes = $notes;
  }
}
?>
