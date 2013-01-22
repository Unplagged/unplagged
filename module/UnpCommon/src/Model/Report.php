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

use UnpCommon\Model\Base;
use UnpCommon\Model\Feature\Linkable;
use Doctrine\ORM\Mapping AS ORM;

/**
 * The class represents a report of a file.
 * 
 * @ORM\Entity 
 * @ORM\Table(name="report")
 */
class Report extends Base implements Linkable{

  /**
   * @ORM\Column(type="string")
   */
  private $name = '';

  /**
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\User")
   * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
   */
  private $creator;

  /**
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\Document")
   * @ORM\JoinColumn(name="target_document_id", referencedColumnName="id")
   */
  private $targetDocument;

  /**
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\File")
   * @ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $file;

  /**
   * ORM\ManyToOne(targetEntity="\UnpCommon\Model\PlagiarismCase", inversedBy="reports")
   * ORM\JoinColumn(name="case_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $case;

  /**
   * ORM\Column(type="array") 
   */
  private $parameters = array();

  public function __construct(\UnpCommon\Model\User $creator, \UnpCommon\Model\PlagiarismCase $case, \UnpCommon\Model\Document $targetDocument = null, $name = ''){
    parent::__construct();
    $this->creator = $creator;
    $this->targetDocument = $targetDocument;
    $this->case = $case;
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getName(){
    return $this->name;
  }

  /**
   * @return \UnpCommon\Model\User
   */
  public function getCreator(){
    return $this->creator;
  }

  /**
   * @return \UnpCommon\Model\Document
   */
  public function getTargetDocument(){
    return $this->targetDocument;
  }

  /**
   * @return \UnpCommon\Model\PlagiarismCase
   */
  public function getCase(){
    return $this->case;
  }

  /**
   * @return \UnpCommon\Model\File
   */
  public function getFile(){
    return $this->file;
  }

  /**
   * @param \UnpCommon\Model\File $file
   */
  public function setFile(\UnpCommon\Model\File $file){
    $this->file = $file;
  }

  /**
   * @param string $name
   * @param string $value
   */
  public function setParameter($name, $value = null){
    if($value){
      $this->parameters[$name] = $value;
    }else{
      unset($this->parameters[$name]);
    }
  }

  /**
   * @param string $name
   * @return string
   */
  public function getParameter($name){
    if(array_key_exists($name, $this->parameters)){
      return $this->parameters[$name];
    }
    return null;
  }

  public function getDirectName(){
    return $this->name;
  }

  public function getDirectLink(){
    return '/report/list/id/' . $this->id;
  }

  public function getIconClass(){
    return 'icon-report';
  }

}