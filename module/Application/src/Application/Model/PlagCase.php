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
namespace Application\Model;

use Application\Model\Base;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * A case represents a collection of related documents that need checking.
 * 
 * The naming to "PlagCase" is due to the fact that "case" is a reserved word in PHP.
 * 
 * @Entity
 * @Table(name="case")
 * @HasLifeCycleCallbacks
 */
class PlagCase extends Base {

  const ICON_CLASS = 'icon-case';

  /**
   * @var string The "real" name of the case, under which it will get published later on.
   * 
   * @Column(type="string") 
   */
  private $name;

  /**
   * @var string The alias is used to show everyone who doesn't have the permission to see the real case name.
   * 
   * @Column(type="string") 
   */
  private $alias;

  /**
   * @var string The date when the document was updated the last time.
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
   * @OrderBy({"created" = "DESC"})
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
   * @JoinTable(name="case_has_defaultroles",
   *      joinColumns={@JoinColumn(name="case_id", referencedColumnName="id")},
   *      inverseJoinColumns={@JoinColumn(name="role_id", referencedColumnName="id")}
   *      )
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
   * @var array The data to generate the barcode from is cached here.
   * 
   * @Column(type="array", nullable=true)
   */
  private $barcodeData;

  /**
   * @var int The amount of users that have to approve a fragment in the case in order to lock it.
   * 
   * @Column(type="integer") 
   */
  private $requiredFragmentRatings;
  
  private $em;

  public function __construct($data = array(), Doctrine\ORM\EntityManager $em = null) {
    /*parent::__construct($data);

    if($em){
      $this->em = $em;
    } else {
      $this->em = Zend_Registry::getInstance()->entitymanager;
    }
    
    $this->documents = new ArrayCollection();
    $this->files = new ArrayCollection();
    $this->collaborators = new ArrayCollection();
    $this->defaultRoles = new ArrayCollection();

    if (array_key_exists('name', $data)) {
      $this->name = $data['name'];
    }
    if (array_key_exists('alias', $data)) {
      $this->alias = $data['alias'];
    }
    if (array_key_exists('requiredFragmentRatings', $data)) {
      $this->requiredFragmentRatings = $data['requiredFragmentRatings'];
    }

    $this->reports = new ArrayCollection();
    $this->documents = new ArrayCollection();
    $this->files = new ArrayCollection();*/
  }

  /**
   * Method auto-called when object is updated in database.
   * 
   * @PreUpdate
   */
  public function updated() {
    $this->updated = new DateTime('now');
  }

  /**
   * @return string 
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return string
   */
  public function getAlias() {
    return $this->alias;
  }

  /**
   * This function returns the current name of the case depending on the state it is in, i. e. the alias at default and
   * the name if the case is already public.
   */
  public function getPublishableName() {
    $publishableName = $this->getAlias();

//    if ($this->getState() && $this->getState()->getName() === 'published') {
//      $publishableName = $this->getName();
//    }

    return $publishableName;
  }

  public function getUpdated() {
    return $this->updated;
  }
/*
  public function addFile(Application_Model_File $file) {
    if (!$this->hasFile($file)) {
      $this->files->add($file);
    }
  }

  public function removeFile(Application_Model_File $file) {
    if ($this->hasFile($file)) {
      $this->files->removeElement($file);
    }
  }

  public function getFiles() {
    return $this->files;
  }

  public function hasFile(Application_Model_File $file) {
    return $this->files->contains($file);
  }

  public function clearFiles() {
    $this->files->clear();
  }
*/
  public function getDirectName() {
    return $this->name; // @todo: change to getpublishablename
  }

  public function getDirectLink() {
//return "/case/show/id/" . $this->id;
    return "/case/list";
  }
/*
  public function toArray() {
    $result = array();

    if (!empty($this->name)) {
      $result["name"] = $this->name;
    }
    if (!empty($this->alias)) {
      $result["alias"] = $this->alias;
    }

    return $result;
  }

  public function setName($name) {
    $this->name = $name;
  }

  public function setAlias($alias) {
    $this->alias = $alias;
  }

  public function getRoles() {
    return $this->defaultRoles;
  }*/

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

  public function getTarget() {
    return $this->target;
  }

  public function setTarget($target) {
    $this->target = $target;
  }

  public function getBarcodeData() {
    return $this->barcodeData;
  }*/

  /**
   * Updates the data used for barcode generation. 
   *//*
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

  public function getDocuments() {
    return $this->documents;
  }

  public function addDocument(Application_Model_Document $document) {
    $document->setCase($this);
    $this->documents->add($document);
  }

  public function getReports() {
    return $this->reports;
  }

  public function addReport(Application_Model_Report $report) {
    $report->setCase($this);
    $this->reports->add($report);
  }

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

  public function addCollaborator(Application_Model_User $collaborator) {
    $this->collaborators->add($collaborator);
  }

  public function removeCollaborator(Application_Model_User $collaborator) {
    $this->collaborators->removeElement($collaborator);
  }

  public function setCollaborators($collaboratorIds = array()) {
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

  public function clearCollaborators() {
    $this->collaborators->clear();
  }

  public function hasDefaultRole(Application_Model_User_Role $defaultRole) {
    return $this->defaultRoles->contains($defaultRole);
  }

  public function getRequiredFragmentRatings() {
    return $this->requiredFragmentRatings;
  }

  public function setRequiredFragmentRatings($requiredFragmentRatings) {
    $this->requiredFragmentRatings = $requiredFragmentRatings;
  }
*/
}