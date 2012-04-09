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
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 * 
 * @Entity
 * @Table(name="cases")
 * @HasLifeCycleCallbacks
 */
class Application_Model_Case extends Application_Model_Base{

  /**
   * The "real" name of the case, under which it will get published later on.
   * 
   * @var string
   * 
   * @Column(type="string") 
   */
  private $name = '';

  /**
   * The alias is used to show everyone who doesn't have the permission to see the real case name.
   * 
   * @var string
   * 
   * @Column(type="string") 
   */
  private $alias = '';

  /**
   * @var string  
   */
  private $state = '';

  /**
   * The date when the document was created.
   * @var string The creation date.
   * 
   * @Column(type="datetime")
   */
  private $created;

  /**
   * The date when the document was updated the last time.
   * @var string The update date.
   * 
   * @Column(type="datetime", nullable=true)
   */
  private $updated;

  /**
   * @ManyToMany(targetEntity="Application_Model_Document")
   * @JoinTable(name="case_has_document",
   *      joinColumns={@JoinColumn(name="case_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="document_id", referencedColumnName="id")}
   *      )
   */
  private $documents;

  /**
   * @ManyToMany(targetEntity="Application_Model_File")
   * @JoinTable(name="case_has_file",
   *      joinColumns={@JoinColumn(name="case_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="file_id", referencedColumnName="id")}
   *      )
   */
  private $files;

  /**
   * @ManyToMany(targetEntity="Application_Model_Tag")
   * @JoinTable(name="case_has_tag",
   *      joinColumns={@JoinColumn(name="case_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="tag_id", referencedColumnName="id")}
   *      )
   */
  private $tags;

  /**
   * @ManyToMany(targetEntity="Application_Model_User")
   * @JoinTable(name="case_has_collaborator",
   *      joinColumns={@JoinColumn(name="case_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="user_id", referencedColumnName="id")}
   *      )
   */
  private $collaborators;

  public function __construct($name, $alias){
    $this->documents = new \Doctrine\Common\Collections\ArrayCollection();
    $this->files = new \Doctrine\Common\Collections\ArrayCollection();
    $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    $this->collaborators = new \Doctrine\Common\Collections\ArrayCollection();
    $this->name = $name;
    $this->alias = $alias;
  }

  /**
   * Method auto-called when object is persisted to database for the first time.
   * 
   * @PrePersist
   */
  public function created(){
    $this->created = new DateTime("now");
  }

  /**
   * Method auto-called when object is updated in database.
   * 
   * @PreUpdate
   */
  public function updated(){
    $this->updated = new DateTime("now");
  }

  /**
   * @return string 
   */
  public function getName(){
    return $this->name;
  }

  /**
   * @return string
   */
  public function getAlias(){
    return $this->alias;
  }

  /**
   * This function returns the current name of the case depending on the state it is in, i. e. the alias at default and
   * the name if the case is already public.
   */
  public function getPublishableName(){
    $publishableName = $this->getAlias();

    if($this->getState() === 'public'){
      $publishableName = $this->getName();
    }

    return $publishableName;
  }

  /**
   * @return string
   */
  public function getState(){
    return $this->state;
  }

  public function getUpdated(){
    return $this->updated;
  }

  public function getCreated(){
    return $this->created;
  }

  public function addTag(Application_Model_Tag $tag){
    return $this->tags->add($tag);
  }

  public function removeTag(Application_Model_Tag $tag){
    return $this->tags->removeElement($tag);
  }

  public function getTags(){
    return $this->tags;
  }

  public function clearTags(){
    $this->tags->clear();
  }

  public function addCollaborator(Application_Model_User $user){
    return $this->collaborators->add($tag);
  }

  public function removeCollaborator(Application_Model_User $user){
    return $this->collaborators->removeElement($tag);
  }

  public function getCollaborators(){
    return $this->collaborators;
  }

  public function clearCollaborators(){
    $this->collaborators->clear();
  }

  public function getDirectName(){
    return "case";
  }
  
  public function getDirectLink(){
    return "/case/show/id/" . $this->id;
  }
  
  public function getIconClass(){
    return "case-icon";
  }

  public function toArray(){
    $result = array();

    if(!empty($this->name)){
      $result["name"] = $this->name;
    }
    if(!empty($this->alias)){
      $result["alias"] = $this->alias;
    }

    return $result;
  }

}

?>
