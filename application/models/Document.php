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
 * The class represents a single document.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * 
 * @Entity 
 * @Table(name="documents")
 * @HasLifeCycleCallbacks
 */
class Application_Model_Document extends Application_Model_Base{

  const ICON_CLASS = 'icon-document';
  
  /**
   * The title of the document.
   * @var string The title.
   * 
   * @Column(type="string", length=64)
   */
  private $title;

  /**
   * The pages in the document.
   * 
   * @OneToMany(targetEntity="Application_Model_Document_Page", mappedBy="document")
   * @OrderBy({"pageNumber" = "ASC"})
   */
  private $pages;

  /**
   * The fragments in the document.
   * 
   * @OneToMany(targetEntity="Application_Model_Document_Fragment", mappedBy="fragment")
   */
  private $fragments;

  /**
   * The bibtex information of the document.
   * @var string The bibtex information.
   * 
   * @Column(type="array", nullable=true)
   */
  private $bibTex;

  /**
   * The current state of the report.
   * 
   * @ManyToOne(targetEntity="Application_Model_State")
   * @JoinColumn(name="state_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $state;
  
  /**
   * The file the document was initially created from.
   * 
   * @ManyToOne(targetEntity="Application_Model_File")
   * @JoinColumn(name="original_file_id", referencedColumnName="id", onDelete="SET NULL")
   */
  private $originalFile;

  public function __construct(array $data = null){

    if(isset($data["title"])){
      $this->title = $data["title"];
    }

    // if(isset($data["bibtex"])){
      // $this->bibTex = $data["bibtex"];
    // }
    if(isset($data["state"])){
      $this->state = $data["state"];
    }
    if(isset($data["originalFile"])){
      $this->originalFile = $data["originalFile"];
    }
	
	$this->bibTex = new \Doctrine\Common\Collections\ArrayCollection();
    $this->pages = new \Doctrine\Common\Collections\ArrayCollection();
    $this->fragments = new \Doctrine\Common\Collections\ArrayCollection();
  }

  public function getId(){
    return $this->id;
  }

  public function getTitle(){
    return $this->title;
  }

  public function getBibTex(){
    return $this->bibTex;
  }

  public function getPages(){
    return $this->pages;
  }

  public function addPage(Application_Model_Document_Page $page){
    $page->setDocument($this);
    $this->pages->add($page);
  }

  public function getFragments(){
    return $this->fragments;
  }

  public function addFragment(Application_Model_Document_Fragment $fragment){
    $fragment->setDocument($this);
    $this->fragments->add($fragment);
  }

  public function getDirectName(){
    return $this->title;
  }

  public function getDirectLink(){
    return "/document_page/list/id/" . $this->id;
  }

  public function setTitle($title){
    $this->title = $title;
  }

  public function getState(){
    return $this->state;
  }

  public function setState($state){
    $this->state = $state;
  }
  
  public function getOriginalFile(){
    return $this->originalFile;
  }
  
  // public function setBibTex($_bibTex){
	// $bibTex = $this->bibTex;
	// $bibTex['kuerzel']= $_bibTex['kuerzel'];
	// $bibTex['autor']= $_bibTex['autor'];
	// $bibTex['titel']= $_bibTex['titel'];
	// $bibTex['zeitschrift']= $_bibTex['zeitschrift'];
	// $bibTex['sammlung']= $_bibTex['sammlung'];
	// $bibTex['hrsg']= $_bibTex['hrsg'];
	// $bibTex['beteiligte']= $_bibTex['beteiligte'];
	// $bibTex['ort']= $_bibTex['ort'];
	// $bibTex['verlag']= $_bibTex['verlag'];
	// $bibTex['ausgabe']= $_bibTex['ausgabe'];
	// $bibTex['jahr']= $_bibTex['jahr'];
	// $bibTex['monat']= $_bibTex['monat'];
	// $bibTex['tag']= $_bibTex['tag'];
	// $bibTex['nummer']= $_bibTex['nummer'];
	// $bibTex['seiten']= $_bibTex['seiten'];
	// $bibTex['umfang']= $_bibTex['umfang'];
	// $bibTex['reihe']= $_bibTex['reihe'];
	// $bibTex['anmerkung']= $_bibTex['anmerkung'];
	// $bibTex['isbn']= $_bibTex['isbn'];
	// $bibTex['url']= $_bibTex['url'];
  // }
  
  
  public function toArray() {
    $data["id"] = $this->id;
    $data["bibTex"] = $this->bibTex;
    $data["pages"] = array();
    
    foreach($this->pages as $page){
      $data["pages"][] = $page->toArray();
    }
    
    return $data;
  }
  
	// function to set bibtex data
	
	public function setBibTexKuerzel ($kuerzel){
		$this->bibTex['kuerzel'] = $kuerzel;
	}
	
	public function setBibTexAutor ($autor){
		$this->bibTex['autor'] = $autor;
	}
	
	public function setBibTexTitel ($titel){
		$this->bibTex['titel'] = $titel;
	}
	
	public function setBibTexZeitschrift ($zeitschrift){
		$this->bibTex['zeitschrift'] = $zeitschrift;
	}
	
	public function setBibTexSammlung ($sammlung){
		$this->bibTex['sammlung'] = $sammlung;
	}
	
	public function setBibTexHrsg ($hrsg){
		$this->bibTex['hrsg'] = $hrsg;
	}
	
	public function setBibTexBeteiligte ($beteiligte){
		$this->bibTex['beteiligte'] = $beteiligte;
	}
	
	public function setBibTexOrt ($ort){
		$this->bibTex['ort'] = $ort;
	}
	
	public function setBibTexVerlag ($verlag){
		$this->bibTex['verlag'] = $verlag;
	}
		
	public function setBibTexAusgabe ($ausgabe){
		$this->bibTex['ausgabe'] = $ausgabe;
	}
	
	public function setBibTexJahr ($jahr){
		$this->bibTex['jahr'] = $jahr;
	}
	
	public function setBibTexMonat ($monat){
		$this->bibTex['monat'] = $monat;
	}
	
	public function setBibTexTag($tag){
		$this->bibTex['tag'] = $tag;
	}
	
	public function setBibTexNummer ($nummer){
		$this->bibTex['nummer'] = $nummer;
	}
	
	public function setBibTexSeiten ($seiten){
		$this->bibTex['seiten'] = $seiten;
	}
	
	public function setBibTexUmfang ($umfang){
		$this->bibTex['umfang'] = $umfang;
	}
	
	public function setBibTexReihe ($reihe){
		$this->bibTex['reihe'] = $reihe;
	}
	
	public function setBibTexAnmerkung ($anmerkung){
		$this->bibTex['anmerkung'] = $anmerkung;
	}
	
	public function setBibTexIsbn ($isbn){
		$this->bibTex['isbn'] = $isbn;
	}
	
	public function setBibTexUrl ($url){
		$this->bibTex['url'] = $url;
	}

}