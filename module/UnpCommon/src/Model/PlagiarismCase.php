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

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use UnpCommon\Model\Report;
use UnpCommon\Model\Base;
use UnpCommon\Model\Document;
use UnpCommon\Model\Feature\ArrayCreator;
use UnpCommon\Model\Feature\Linkable;
use UnpCommon\Model\Feature\UpdateTracker;
use UnpCommon\Model\File;

/**
 * A case represents a collection of related documents that need checking.
 * 
 * The naming to "PlagiarismCase" is due to the fact that "case" is a reserved word in PHP.
 * 
 * @ORM\Entity
 * @ORM\Table(name="plagiarism_case")
 * @ORM\HasLifeCycleCallbacks
 */
class PlagiarismCase extends Base implements Linkable, UpdateTracker, ArrayCreator{

  /**
   * @var string The "real" name of the case, under which it will get published later on.
   * @ORM\Column(type="string") 
   */
  private $name = '';

  /**
   * @var string The alias is shown to everyone who doesn't have the permission to see the real case name.
   * @ORM\Column(type="string") 
   */
  private $alias = '';

  /**
   * @ORM\Column(type="text") 
   */
  private $description = '';

  /**
   * @var string The date when the case was updated the last time.
   * @ORM\Column(type="datetime", nullable=true)
   */
  private $updated;

  /**
   * 
   * @ORM\ManyToMany(targetEntity="\UnpCommon\Model\Document")
   * @ORM\JoinTable(name="case_has_source",
   *      joinColumns={@ORM\JoinColumn(name="case_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="document_id", referencedColumnName="id")}
   *      )
   * @ORM\OrderBy({"title" = "DESC"})
   */
  private $sources;

  /**
   * ORM\OneToMany(targetEntity="\UnpCommon\Model\Report", mappedBy="case")
   */
  private $reports;

  /**
   * @ORM\ManyToMany(targetEntity="\UnpCommon\Model\File")
   * @ORM\JoinTable(name="case_has_file",
   *      joinColumns={@ORM\JoinColumn(name="case_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="file_id", referencedColumnName="id")}
   *      )
   * @ORM\OrderBy({"created" = "DESC"})
   */
  private $files;

  /**
   * @ORM\ManyToMany(targetEntity="\UnpCommon\Model\User")
   * @ORM\JoinTable(name="case_has_collaborator",
   *      joinColumns={@ORM\JoinColumn(name="case_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
   *      )
   */
  private $collaborators;

  /**
   * ORM\ManyToMany(targetEntity="\UnpCommon\Model\InheritableRole", cascade={"persist", "remove"}) 
   * ORM\JoinTable(name="case_has_defaultrole",
   *      joinColumns={ORM\JoinColumn(name="case_id", referencedColumnName="id")},
   *      inverseJoinColumns={ORM\JoinColumn(name="role_id", referencedColumnName="id")}
   *      )
   */
  private $defaultRoles;

  /**
   * @var The documents that are inspected in this case.
   * 
   * @ORM\ManyToMany(targetEntity="\UnpCommon\Model\Document")
   * @ORM\JoinTable(name="case_has_target",
   *      joinColumns={@ORM\JoinColumn(name="case_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="target_id", referencedColumnName="id")}
   *      )
   * @ORM\OrderBy({"created" = "DESC"})
   */
  private $targetDocuments;

  /**
   * @var array The data to generate the barcode from is cached here.
   * 
   * @ORM\Column(type="array", nullable=true)
   */
  private $barcodeData;

  public function __construct($name = '', $alias = ''){
    parent::__construct();

    $this->name = $name;
    $this->alias = $alias;

    $this->documents = new ArrayCollection();
    $this->collaborators = new ArrayCollection();
    $this->defaultRoles = new ArrayCollection();
    $this->reports = new ArrayCollection();
    $this->files = new ArrayCollection();
    $this->targetDocuments = new ArrayCollection();
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
   * @return string
   */
  public function getDescription(){
    return $this->description;
  }

  /**
   * @param string $description
   */
  public function setDescription($description = ''){
    $this->description = $description;
  }

  /**
   * This function returns the current name of the case depending on the state it is in, i. e. the alias at default and
   * the name if the case is already public.
   */
  public function getPublishableName(){
    $publishableName = $this->getAlias();

//    if ($this->getState() && $this->getState()->getName() === 'published') {
//      $publishableName = $this->getName();
//    }

    return $publishableName;
  }

  /**
   * @param File $file
   */
  public function addFile(File $file){
    if(!$this->containsFile($file)){
      $this->files->add($file);
    }
  }

  /**
   * @param File $file
   */
  public function removeFile(File $file){
    if($this->containsFile($file)){
      $this->files->removeElement($file);
    }
  }

  /**
   * @return array
   */
  public function getFiles(){
    return $this->files->toArray();
  }

  /**
   * @param File $file
   * @return bool
   */
  public function containsFile(File $file){
    return $this->files->contains($file);
  }

  /*
    public function getRoles() {
    return $this->defaultRoles;
    } */

  /**
   * Return the percentage of plagiarism in this case.
   * 
   * @return percentage value of plagiarism 
   *//*
    public function getPlagiarismPercentage() {
    if (is_array($this->barcodeData)) {
    $pagesCount = count($this->barcodeData);
    $percentageSum = 0;

    foreach ($this->barcodeData as $page) {
    $percentageSum += $page['plagPercentage'];
    }
    } else {
    $pagesCount = 0;
    }
    return ($pagesCount != 0) ? round($percentageSum * 1. / $pagesCount / 10) * 10 : 0;
    }

    public function addDefaultRole(Application_Model_User_InheritableRole $role) {
    $this->defaultRoles->add($role);
    }

    public function getDefaultRoles() {
    return $this->defaultRoles;
    }

    public function getBarcode($width, $height, $barHeight, $showLabels, $widthUnit) {
    $barcode = null;

    if ($this->getBarcodeData()) {
    $barcode = new Unplagged_Barcode($width, $height, $barHeight, $showLabels, $widthUnit, $this->getBarcodeData());
    }

    return $barcode;
    }
   */

  /**
   * @return array
   */
  public function getTargetDocuments(){
    return $this->targetDocuments->toArray();
  }

  /**
   * @param Document $target
   */
  public function addTargetDocument(Document $target){
    if(!$this->containsTargetDocument($target)){
      $this->targetDocuments->add($target);
    }
  }
  
  public function removeTargetDocument(Document $target){
    if($this->containsTargetDocument($target)){
      $this->targetDocuments->removeElement($target);
    }
  }

  /**
   * @param Document $target
   * @return bool
   */
  public function containsTargetDocument(Document $target){
    return $this->targetDocuments->contains($target);
  }

  /*
    public function getBarcodeData() {
    return $this->barcodeData;
    }
   */

  /**
   * Updates the data used for barcode generation. 
   */
  /*
    public function updateBarcodeData() {
    if ($this->target) {
    $barcodeData = array();
    foreach ($this->target->getPages() as $page) {
    $pageData = array();
    $pageData['pageNumber'] = $page->getPageNumber();
    $pageData['plagPercentage'] = $page->getPlagiarismPercentage();
    $pageData['disabled'] = $page->getDisabled() ? 'true' : 'false';

    $barcodeData[] = $pageData;
    }

    $this->barcodeData = $barcodeData;
    }
    }
   */

  /**
   * @return \UnpCommon\Model\Document
   */
  public function getSources(){
    return $this->sources->toArray();
  }

  /**
   * @param \UnpCommon\Model\Document $source
   */
  public function addSource(Document $source){
    if(!$this->containsSource($source)){
      $source->setCase($this);
      $this->sources->add($source);
    }
  }

  /**
   * @param \UnpCommon\Model\Document $source
   */
  public function removeSource(Document $source){
    if($this->containsSource($source)){
      $this->sources->removeElement($source);
    }
  }
  
  /**
   * @param \UnpCommon\Model\Document $source
   * @return bool
   */
  public function containsSource(Document $source){
    return $this->sources->contains($source);
  }

  /**
   * @return Report
   */
  public function getReports(){
    return $this->reports->toArray();
  }

  /**
   * @param Report $report
   */
  public function addReport(Report $report){
    if(!$this->reports->contains($report)){
      $this->reports->add($report);
    }
    return $this;
  }

  /*
    public function getCollaboratorIds() {
    $collaboratorIds = array();
    foreach ($this->collaborators as $collaborator) {
    $collaboratorIds[] = $collaborator->getId();
    }
    return $collaboratorIds;
    }

    public function getCollaborators() {
    return $this->collaborators;
    }

    public function addCollaborator(User $collaborator) {
    $this->collaborators->add($collaborator);
    }

    public function removeCollaborator(User $collaborator) {
    $this->collaborators->removeElement($collaborator);
    }

    public function setCollaborators(array $collaboratorIds = array()) {
    $removedCollaborators = array();

    // 1) search all collaborators that already exist by their id
    if (!empty($this->collaborators)) {
    $this->collaborators->filter(function($collaborator) use (&$collaboratorIds, &$removedCollaborators) {
    if (in_array($collaborator->getId(), $collaboratorIds)) {
    $collaboratorIds = array_diff($collaboratorIds, array($collaborator->getId()));
    return true;
    }
    $removedCollaborators[] = $collaborator;
    return false;
    });
    }

    // 2) create new collaborators for those that don't exist yet
    foreach ($collaboratorIds as $collaboratorId) {
    $collaborator = $this->em->getRepository('Application_Model_User')->findOneById($collaboratorId);

    $this->addCollaborator($collaborator);
    }

    // 3) remove collaborators that belonged to the element before, but not anymore
    foreach ($removedCollaborators as $collaborator) {
    $this->removeCollaborator($collaborator);
    }
    }

    public function hasDefaultRole(User_Role $defaultRole) {
    return $this->defaultRoles->contains($defaultRole);
    }

   */

  /**
   * @ORM\PreUpdate
   */
  public function updated(){
    $this->updated = new DateTime('now');
  }

  /**
   * @return \DateTime
   */
  public function getUpdated(){
    return $this->updated;
  }

  public function toArray(){
    return array(
        'name'=>$this->name,
        'alias'=>$this->alias,
        'description'=>$this->description,
    );
  }

  public function getDirectName(){
    return $this->getPublishableName();
  }

  public function getDirectLink(){
    return '/case/overview/' . $this->id;
  }

  public function getIconClass(){
    return 'fam-icon-package';
  }

}