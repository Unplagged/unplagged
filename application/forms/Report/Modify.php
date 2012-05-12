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
 * The form class generates a form for creating a new fragment.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 */
class Application_Form_Report_Modify extends Zend_Form {

    private $cases = array();
    private $fragments = array();
	//private $documents = array();

    public function __construct() {
        $em = Zend_Registry::getInstance()->entitymanager;

        $query = $em->createQuery("SELECT t FROM Application_Model_Case t");
        $cases = $query->getResult();
		//$cases = $this->getCurrentCase()->getPublishableName();

        $params["cases"] = array();
        foreach ($cases as $case) {
            $params["cases"][$case->getId()] = $case->getName();
        }

        $query = $em->createQuery("SELECT d FROM Application_Model_Document_Fragment d");
        $fragments = $query->getResult();

        $params["fragments"] = array();
        foreach ($fragments as $fragment) {
            $params["fragments"][$fragment->getId()] = $fragment->getTitle();
        }
		/*
		$params["documents"] = array();
	    foreach($documents as $document){
	      $params["documents"][$document->getId()] = $document->getTitle();
	    }

	    $this->documents = $params['documents'];
*/
        $this->cases = $params['cases'];
        $this->fragments = $params['fragments'];

        parent::__construct();
    }

    /**
     * Creates the form to register a new user.
     * @see Zend_Form::init()
     */
    public function init() {
        $pageExistsValidator = new Unplagged_Validate_RecordExists('Application_Model_Document_Page', 'pageNumber', array("document" => 6));

        $this->setMethod('post');
        $this->setAction("/report/create");
		
		$caseElement = new Zend_Form_Element_Text('case');
		$caseElement->setLabel("Case");
		$caseElement->addValidator('regex', false, array('/^[a-z0-9����������]/i'));
		$caseElement->addValidator('stringLength', false, array(2, 64));
		$caseElement->setRequired(true);

        // general group
        // $caseElement = new Zend_Form_Element_Select('case');
        // $caseElement->setLabel("Case");
        // $caseElement->addMultiOptions($this->cases);
        // $caseElement->setRequired(true);

        $noteElement = new Zend_Form_Element_Textarea('note');
        $noteElement->setLabel("Note");
		
		$fragmentElement = new Zend_Form_Element_Multiselect('fragment');
        $fragmentElement->setLabel("Fragment");
        $fragmentElement->addMultiOptions($this->fragments);
		$fragmentElement->addValidator('regex', false, array('/^[0-9]/i'));
        $fragmentElement->setRequired(true);
		
        $noteElement = new Zend_Form_Element_Textarea('note');
        $noteElement->setLabel("Note");

 

        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setLabel('Create report');
        $submitElement->setIgnore(true);
        $submitElement->setAttrib('class', 'submit');
        $submitElement->removeDecorator('DtDdWrapper');

        $this->addElements(array(
            $caseElement
			//, $fragmentElement
            , $noteElement
            
        ));

        $this->addDisplayGroup(array(
            'case'
			//, 'fragment'
            , 'note'
                )
                , 'generalGroup'
                , array('legend' => 'General Information')
        );

       
        
        $this->addElements(array(
            $submitElement
        ));
    }

}
