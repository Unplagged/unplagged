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
namespace UnpCommon\Model\Document;

use \UnpCommon\Model\Base;
use \Doctrine\ORM\Mapping as ORM;
use \UnpCommon\Model\Feature\ArrayCreator;

/**
 * Represents a single line in a document.
 * 
 * @ORM\Entity 
 * @ORM\Table(name="document_line")
 */
final class Line extends Base implements ArrayCreator{

  /**
   * @var \UnpCommon\Model\Document\Page The parent page of this line
   * @ORM\ManyToOne(targetEntity="\UnpCommon\Model\Document\Page", inversedBy="lines", cascade={"persist"})
   * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $page;

  /**
   * @var string The content of the line.
   * @ORM\Column(type="text", nullable=true)
   */
  private $content;

  public function __construct(\UnpCommon\Model\Page $page = null, $content = ''){
    parent::__construct();

    $this->page = $page;
    $this->content = $content;
  }

  /**
   * @return array
   */
  public function toArray(){
    $data = array(
        'id'=>$this->id,
        'lineNumber'=>$this->lineNumber,
        'content'=>$this->content,
    );

    return $data;
  }

  /**
   * @return string
   */
  public function getContent(){
    return $this->content;
  }

  /**
   * @param string $content
   */
  public function setContent($content = ''){
    $this->content = $content;
  }

  /**
   * @return \UnpCommon\Model\Document\Page
   */
  public function getPage(){
    return $this->page;
  }

}