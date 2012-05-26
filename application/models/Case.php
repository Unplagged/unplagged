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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * A case is one of the most important model classes of the Unplagged system.
 * 
 * 
 * @Entity
 * @Table(name="cases")
 * @HasLifeCycleCallbacks
 */
class Application_Model_Case extends Application_Model_Base{

  const ICON_CLASS = 'icon-case';

  /**
   * The "real" name of the case, under which it will get published later on.
   * 
   * @var string
   * 
   * @Column(type="string") 
   */
  private $name;

  /**
   * The alias is used to show everyone who doesn't have the permission to see the real case name.
   * 
   * @var string
   * 
   * @Column(type="string") 
   */
  private $alias;

  /**
   * @var string  
   */
  private $state;

  /**
   * An abbreviation for the case.
   *
   * @var string
   *
   * @Column(type="string", length=4) 
   */
  private $abbreviation;

  /**
   * The date when the document was updated the last time.
   * 
   * @var string The update date.
   * 
   * @Column(type="datetime", nullable=true)
   */
  private $updated;

  /**
   * @OneToMany(targetEntity="Application_Model_Document", mappedBy="case")
   */
  private $documents;

  /**
   * @OneToMany(targetEntity="Application_Model_Report", mappedBy="case")
   */
  private $reports;

  /**
   * @ManyToMany(targetEntity="Application_Model_File")
   * @JoinTable(name="case_has_file",
   *      joinColumns={@JoinColumn(name="case_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="file_id", referencedColumnName="id")}
   *      )
   */
  private $files;

  /**
   * @ManyToMany(targetEntity="Application_Model_User")
   * @JoinTable(name="case_has_collaborator",
   *      joinColumns={@JoinColumn(name="case_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="user_id", referencedColumnName="id")}
   *      )
   */
  private $collaborators;

  /**
   * @ManyToMany(targetEntity="Application_Model_User_InheritableRole", cascade={"persist", "remove"}) 
   */
  private $defaultRoles;

  /**
   * The document that is inspected in this case.
   * 
   * @ManyToOne(targetEntity="Application_Model_Document")
   * @JoinColumn(name="target_document_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $target;

  /**
   * The data to generate the barcode from is cached here.
   * 
   * @var array The barcode data.
   * 
   * @Column(type="array", nullable=true)
   */
  private $barcodeData;

  public function __construct($name = null, $alias = null, $abbreviation = null){
    $this->documents = new ArrayCollection();
    $this->files = new ArrayCollection();
    $this->collaborators = new ArrayCollection();
    $this->defaultRoles = new ArrayCollection();

    $this->name = $name;
    $this->alias = $alias;
    $this->abbreviation = $abbreviation;
  }

  /**
   * Method auto-called when object is updated in database.
   * 
   * @PreUpdate
   */
  public function updated(){
    $this->updated = new DateTime('now');
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

  public function addCollaborator(Application_Model_User $user){
    return $this->collaborators->add($user);
  }

  public function removeCollaborator(Application_Model_User $user){
    return $this->collaborators->removeElement($user);
  }

  public function getCollaborators(){
    return $this->collaborators;
  }

  public function clearCollaborators(){
    $this->collaborators->clear();
  }

  public function addFile(Application_Model_File $file){
    return $this->files->add($file);
  }

  public function removeFile(Application_Model_File $file){
    return $this->file->removeElement($file);
  }

  public function getFiles(){
    return $this->files;
  }

  public function hasFile(Application_Model_File $file){
    return $this->files->contains($file);
  }

  public function clearFiles(){
    $this->files->clear();
  }

  public function getDirectName(){
    return $this->name; // @todo: change to getpublishablename
  }

  public function getDirectLink(){
    return "/case/show/id/" . $this->id;
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

  public function getAbbreviation(){
    return $this->abbreviation;
  }

  public function setName($name){
    $this->name = $name;
  }

  public function setAlias($alias){
    $this->alias = $alias;
  }

  public function setAbbreviation($abbreviation){
    $this->abbreviation = $abbreviation;
  }

  public function getRoles(){
    return $this->defaultRoles;
  }

  /**
   * Return the percentage of plagiarism in this case.
   * 
   * @return percentage value of plagiarism 
   */
  public function getPlagiarismPercentage(){
    if(is_array($this->barcodeData)){
      $pagesCount = count($this->barcodeData);
      $percentageSum = 0;

      foreach($this->barcodeData as $page){
        $percentageSum += $page['plagPercentage'];
      }
    }else{
      $pagesCount = 0;
    }
    return ($pagesCount != 0) ? round($percentageSum * 1. / $pagesCount / 10) * 10 : 0;
  }

  public function addDefaultRole(Application_Model_User_InheritableRole $role){
    $this->defaultRoles->add($role);
  }

  public function getDefaultRoles(){
    return $this->defaultRoles;
  }

  public function getBarcode($width, $height, $barHeight, $showLabels, $widthUnit){
    if($this->barcodeData){
      return new Unplagged_Barcode($width, $height, $barHeight, $showLabels, $widthUnit, $this->barcodeData);
    }
  }

  public function getTarget(){
    return $this->target;
  }

  public function setTarget($target){
    $this->target = $target;
  }

  public function getBarcodeData(){
    return $this->barcodeData;
  }

  /**
   * Updates the data used for barcode generation. 
   */
  public function updateBarcodeData(){
    if($this->target){
      $barcodeData = array();
      foreach($this->target->getPages() as $page){
        $pageData = array();
        $pageData['pageNumber'] = $page->getPageNumber();
        $pageData['plagPercentage'] = $page->getPlagiarismPercentage();
        $pageData['disabled'] = $page->getDisabled() ? 'true' : 'false';

        $barcodeData[] = $pageData;
      }

      $this->barcodeData = $barcodeData;
    }
  }

  public function getDocuments(){
    return $this->documents;
  }

  public function addDocument(Application_Model_Document $document){
    $document->setCase($this);
    $this->documents->add($document);
  }

  public function getReports(){
    return $this->reports;
  }

  public function addReport(Application_Model_Report $report){
    $report->setCase($this);
    $this->reports->add($report);
  }

}

?>
