<?php

DEFINE ("HANDLEDOCUMENTTYPE", '

								if($(this).val() == \'full\' || $(this).val() == \'periodikum\' ) 
								{ 
									$(\'#zeitschrift-element\').show();		$(\'#zeitschrift-label\').show();	
									$(\'#monat-element\').show(); 			$(\'#monat-label\').show();
									$(\'#tag-element\').show();				$(\'#tag-label\').show();
									$(\'#nummer-element\').show();			$(\'#nummer-label\').show();
								}
			
								else {
									$(\'#zeitschrift-element\').hide();		$(\'#zeitschrift-label\').hide();
									$(\'#monat-element\').hide(); 			$(\'#monat-label\').hide();									
									$(\'#tag-element\').hide();				$(\'#tag-label\').hide();
									$(\'#nummer-element\').hide();			$(\'#nummer-label\').hide();									
								}
								
								if($(this).val() == \'full\' || $(this).val() == \'aufsatz\' ) 
								{ 
									$(\'#sammlung-element\').show();		$(\'#sammlung-label\').show();
									$(\'#hrsg-element\').show();			$(\'#hrsg-label\').show();
									$(\'#issn-element\').show();			$(\'#issn-label\').show();						
								}
			
								else {
									$(\'#sammlung-element\').hide();		$(\'#sammlung-label\').hide();
									$(\'#hrsg-element\').hide();	 		$(\'#hrsg-label\').hide();
									$(\'#issn-element\').hide();			$(\'#issn-label\').hide();
								}
								
								if($(this).val() == \'full\' || $(this).val() == \'aufsatz\' || $(this).val() == \'periodikum\') 
								{ 
									$(\'#seiten-element\').show();			$(\'#seiten-label\').show();	
																		
								}
			
								else {
									$(\'#seiten-element\').hide();			$(\'#seiten-label\').hide();
											
								}
								
								if($(this).val() == \'full\' || $(this).val() == \'buch\' || $(this).val() == \'aufsatz\') 
								{ 
									$(\'#isbn-element\').show();			$(\'#isbn-label\').show();
																
								}
			
								else {
									$(\'#isbn-element\').hide();			$(\'#isbn-label\').hide();
											
								}
								if($(this).val() == \'full\'){
									$(\'#kuerzel-element\').show();			$(\'#kuerzel-label\').show();
									$(\'#beteiligte-element\').show();		$(\'#beteiligte-label\').show();
									$(\'#ausgabe-element\').show();			$(\'#ausgabe-label\').show();
									$(\'#umfang-element\').show();			$(\'#umfang-label\').show();
									$(\'#reihe-element\').show();			$(\'#reihe-label\').show();
									$(\'#doi-element\').show();				$(\'#doi-label\').show();
									$(\'#urn-element\').show();				$(\'#urn-label\').show();
									$(\'#wp-element\').show();				$(\'#wp-label\').show();
									$(\'#schluessel-element\').show();		$(\'#schluessel-label\').show();
								}
								else{
									$(\'#kuerzel-element\').hide();			$(\'#kuerzel-label\').hide();
									$(\'#beteiligte-element\').hide();		$(\'#beteiligte-label\').hide();
									$(\'#ausgabe-element\').hide();			$(\'#ausgabe-label\').hide();
									$(\'#umfang-element\').hide();			$(\'#umfang-label\').hide();
									$(\'#reihe-element\').hide();			$(\'#reihe-label\').hide();
									$(\'#doi-element\').hide();				$(\'#doi-label\').hide();
									$(\'#urn-element\').hide();				$(\'#urn-label\').hide();
									$(\'#wp-element\').hide();				$(\'#wp-label\').hide();
									$(\'#schluessel-element\').hide();		$(\'#schluessel-label\').hide();
								}
								
');
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
 * This class reprents a form for creating and updating a document.
 */
class Application_Form_Document_Modify extends Zend_Form{

  /**
   * Creates the form to create a new case.
   * 
   * @see Zend_Form::init()
   */
  public function init(){
    $this->setMethod('post');
    $this->setAction("/document/create");
	
	// general group
    $titleElement = new Zend_Form_Element_Text('title');
    $titleElement->setLabel("Title");
    $titleElement->addValidator('regex', false, array('/^[a-z0-9ßöäüâáàéèñ]/i'));
    $titleElement->addValidator('stringLength', false, array(2, 64));
    $titleElement->setRequired(true);
	
	// bibTex group
	$typeElement = new Zend_Form_Element_Select('type');
	$typeElement->setLabel("Document type: ");
	$typeElement->addMultiOptions(array('full'=>'Vollständiges Formular', 'buch'=>'Buchformular', 'periodikum'=>'Periodikumformular', 'aufsatz' => 'Aufsatzsammlungsformular' ));
	$typeElement->setRequired(true);
	$typeElement->setAttrib('onchange',HANDLEDOCUMENTTYPE);
     
	$kuerzelElement = new Zend_Form_Element_Text('kuerzel');
    $kuerzelElement->setLabel("Kuerzel");
		
	$autorElement = new Zend_Form_Element_Text('autor');
    $autorElement->setLabel("Autor");
	$autorElement->setRequired(true);
	
	$titelElement = new Zend_Form_Element_Text('titel');
    $titelElement->setLabel("Titel");
	$titelElement->setRequired(true);
	
	$zeitschriftElement = new Zend_Form_Element_Text('zeitschrift');
    $zeitschriftElement->setLabel("Zeitschrift");
    
	$sammlungElement = new Zend_Form_Element_Text('sammlung');
	$sammlungElement->setLabel("Sammlung");	
	
	$hrsgElement = new Zend_Form_Element_Text('hrsg');
	$hrsgElement->setLabel("Hrsg.");	
	
	$beteiligteElement = new Zend_Form_Element_Text('beteiligte');
	$beteiligteElement->setLabel("Beteiligte");	
	
	$ortElement = new Zend_Form_Element_Text('ort');
	$ortElement->setLabel("Ort");	
	
	$verlagElement = new Zend_Form_Element_Text('verlag');
	$verlagElement->setLabel("Verlag");	
	
	$ausgabeElement = new Zend_Form_Element_Text('ausgabe');
	$ausgabeElement->setLabel("Ausgabe");	
	
	$jahrElement = new Zend_Form_Element_Text('jahr');
	$jahrElement->setLabel("Jahr");	
	$jahrElement->setRequired(true);
	
	$monatElement = new Zend_Form_Element_Text('monat');
	$monatElement->setLabel("Monat");	
	
	$tagElement = new Zend_Form_Element_Text('tag');
	$tagElement->setLabel("Tag");	
	
	$nummerElement = new Zend_Form_Element_Text('nummer');
	$nummerElement->setLabel("Nummer");	
	
	$seitenElement = new Zend_Form_Element_Text('seiten');
	$seitenElement->setLabel("Seiten");
	
	$umfangElement = new Zend_Form_Element_Text('umfang');
	$umfangElement->setLabel("Umfang");
	
	$reiheElement = new Zend_Form_Element_Text('reihe');
	$reiheElement->setLabel("Reihe");
	
	$anmerkungElement = new Zend_Form_Element_Text('anmerkung');
	$anmerkungElement->setLabel("Anmerkung");
	
	$isbnElement = new Zend_Form_Element_Text('isbn');
	$isbnElement->setLabel("ISBN");
	
	$issnElement = new Zend_Form_Element_Text('issn');
	$issnElement->setLabel("ISSN");
	
	$doiElement = new Zend_Form_Element_Text('doi');
	$doiElement->setLabel("DOI");
	
	$urlElement = new Zend_Form_Element_Text('url');
	$urlElement->setLabel("URL");
	
	$urnElement = new Zend_Form_Element_Text('urn');
	$urnElement->setLabel("URN");
	
	$wpElement = new Zend_Form_Element_Text('wp');
	$wpElement->setLabel("WP");
	
	$inlitElement = new Zend_Form_Element_Text('inlit');
	$inlitElement->setLabel("inLit");
	
	$infnElement = new Zend_Form_Element_Text('infn');
	$infnElement->setLabel("inFN");
	
	$schluesselElement = new Zend_Form_Element_Text('schluessel');
	$schluesselElement->setLabel("Schluessel");
	
	//submit
    $submitElement = new Zend_Form_Element_Submit('submit');
    $submitElement->setLabel('Create document');
    $submitElement->setOptions(array('class'=>'btn btn-primary'));

	
    $this->addElements(array(
       $titleElement
      , $typeElement
      //, $bibTexElement
	  
	  , $kuerzelElement
	  , $autorElement
	  , $titelElement
	  , $zeitschriftElement
	  , $sammlungElement
	  , $hrsgElement
	  , $beteiligteElement
	  , $ortElement
	  , $verlagElement
	  , $ausgabeElement
	  , $jahrElement
	  , $monatElement
	  , $tagElement
	  , $nummerElement
	  , $seitenElement
	  , $umfangElement
	  , $reiheElement
	  , $anmerkungElement
	  , $isbnElement
	  , $issnElement
	  , $doiElement
	  , $urlElement
	  , $urnElement
	  , $wpElement
	  , $inlitElement
	  , $infnElement
	  , $schluesselElement
    ));

    $this->addDisplayGroup(array('title', 'type')
        , 'generalGroup'
        , array('legend'=>'Document Information')
    );
	
	$this->addDisplayGroup(array(
		//'bibTex'
		
		 'kuerzel'
		, 'autor'
		, 'titel'
		, 'zeitschrift'
		, 'sammlung'
		, 'hrsg'
		, 'beteiligte'
		, 'ort'
		, 'verlag'
		, 'ausgabe'
		, 'jahr'
		, 'monat'
		, 'tag'
		, 'nummer'
		, 'seiten'
		, 'umfang'
		, 'reihe'
		, 'anmerkung'
		, 'isbn'
	    , 'issn'
	    , 'doi'
	    , 'url'
	    , 'urn'
		, 'wp'
	    , 'inlit'
	    , 'infn'
	    , 'schluessel'
		)
        , 'bibTexGroup'
        , array('legend'=>'BiBTex Information')
    );
	
    $this->addElements(array(
      $submitElement
        )
    );
  }

}
?>