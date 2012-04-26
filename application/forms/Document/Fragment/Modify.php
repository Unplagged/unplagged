<?php
require_once(realpath(dirname(__FILE__)) . "/../../../../scripts/jobs/Document/Page/Compare_text.php");
DEFINE("AJAX_CALL",'$.ajax(
            {
                url: "../simtext/ajax",
                data: {
                    left: $("textarea#candidateText").val(),
                    right: $("textarea#sourceText").val()
                }
            }).done(
                function(data){
                    $("div#compared_source_Text").empty();
                    $("div#compared_source_Text").html(data)
                }
            )');

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
class Application_Form_Document_Fragment_Modify extends Zend_Form {

    private $types = array();
    private $documents = array();

    public function __construct() {
        $em = Zend_Registry::getInstance()->entitymanager;

        $query = $em->createQuery("SELECT t FROM Application_Model_Document_Fragment_Type t");
        $types = $query->getResult();

        $params["types"] = array();
        foreach ($types as $type) {
            $params["types"][$type->getId()] = $type->getName();
        }

        $query = $em->createQuery("SELECT d FROM Application_Model_Document d");
        $documents = $query->getResult();

        $params["documents"] = array();
        foreach ($documents as $document) {
            $params["documents"][$document->getId()] = $document->getTitle();
        }

        $this->types = $params['types'];
        $this->documents = $params['documents'];

        parent::__construct();
    }

    /**
     * Creates the form to register a new user.
     * @see Zend_Form::init()
     */
    public function init() {
        $pageExistsValidator = new Unplagged_Validate_RecordExists('Application_Model_Document_Page', 'pageNumber', array("document" => 6));

        $this->setMethod('post');
        $this->setAction("/document_fragment/create/");

        // general group
        $typeElement = new Zend_Form_Element_Select('type');
        $typeElement->setLabel("Type");
        $typeElement->addMultiOptions($this->types);
        $typeElement->setRequired(true);

        $noteElement = new Zend_Form_Element_Textarea('note');
        $noteElement->setLabel("Note");

        // candidate group
        $candidateDocumentElement = new Zend_Form_Element_Select('candidateDocument');
        $candidateDocumentElement->setLabel("Document");
        $candidateDocumentElement->addMultiOption("new", "Neues Dokument");
        $candidateDocumentElement->addMultiOptions($this->documents);
        $candidateDocumentElement->setAttrib('onchange', 'if($(this).val() == \'new\') { $(\'#candidateBibTex-label label\').show(); $(\'#candidateBibTex-element\').show(); } else { $(\'#candidateBibTex-label label\').hide(); $(\'#candidateBibTex-element\').hide(); }');

        $candidateBibTexElement = new Zend_Form_Element_Textarea('candidateBibTex');
        $candidateBibTexElement->setLabel("Candidate BiBTex");

        $candidatePageFromElement = new Zend_Form_Element_Text('candidatePageFrom');
        $candidatePageFromElement->setLabel("Page from");
        $candidatePageFromElement->setRequired(true);

        $candidateLineFromElement = new Zend_Form_Element_Text('candidateLineFrom');
        $candidateLineFromElement->setLabel("Line from");
        $candidateLineFromElement->setRequired(true);

        $candidatePageToElement = new Zend_Form_Element_Text('candidatePageTo');
        $candidatePageToElement->setLabel("Page to");
        $candidatePageToElement->setRequired(true);

        $candidateLineToElement = new Zend_Form_Element_Text('candidateLineTo');
        $candidateLineToElement->setLabel("Line to");
        $candidateLineToElement->setRequired(true);  
        $candidateTextElement = new Zend_Form_Element_Textarea('candidateText');
        $candidateTextElement->setAttrib('onchange', AJAX_CALL);
        $candidateTextElement->setLabel("Text");

        // source group
        $sourceDocumentElement = new Zend_Form_Element_Select('sourceDocument');
        $sourceDocumentElement->setLabel("Document");
        $sourceDocumentElement->addMultiOption("new", "Neues Dokument");
        $sourceDocumentElement->addMultiOptions($this->documents);
        $sourceDocumentElement->setAttrib('onchange', 'if($(this).val() == \'new\') { $(\'#sourceBibTex-label label\').show(); $(\'#sourceBibTex-element\').show(); } else { $(\'#sourceBibTex-label label\').hide(); $(\'#sourceBibTex-element\').hide(); }');

        $sourcePageFromElement = new Zend_Form_Element_Text('sourcePageFrom');
        $sourcePageFromElement->setLabel("Page from");
        $sourcePageFromElement->setRequired(true);

        $sourceLineFromElement = new Zend_Form_Element_Text('sourceLineFrom');
        $sourceLineFromElement->setLabel("Line from");
        $sourceLineFromElement->setRequired(true);

        $sourcePageToElement = new Zend_Form_Element_Text('sourcePageTo');
        $sourcePageToElement->setLabel("Page to");
        $sourcePageToElement->setRequired(true);

        $sourceLineToElement = new Zend_Form_Element_Text('sourceLineTo');
        $sourceLineToElement->setLabel("Line to");
        $sourceLineToElement->setRequired(true);

        $sourceTextElement = new Zend_Form_Element_Textarea('sourceText');
        $sourceTextElement->setLabel('Text');
        $sourceTextElement->setAttrib('onchange', AJAX_CALL);

        $sourceBibTexElement = new Zend_Form_Element_Textarea('sourceBibTex');
        $sourceBibTexElement->setLabel("Source BiBTex");


        $submitElement = new Zend_Form_Element_Submit('submit');
        $submitElement->setLabel('Create fragment');
        $submitElement->setIgnore(true);
        $submitElement->setAttrib('class', 'submit');
        $submitElement->removeDecorator('DtDdWrapper');

        $this->addElements(array(
            $typeElement
            , $noteElement
            , $candidateDocumentElement
            , $candidatePageFromElement
            , $candidateLineFromElement
            , $candidatePageToElement
            , $candidateLineToElement
            , $candidateTextElement
            , $candidateBibTexElement
            , $sourceDocumentElement
            , $sourceBibTexElement
            , $sourcePageFromElement
            , $sourceLineFromElement
            , $sourcePageToElement
            , $sourceLineToElement
            , $sourceTextElement
        ));

        $this->addDisplayGroup(array(
            'type'
            , 'note'
                )
                , 'generalGroup'
                , array('legend' => 'General Information')
        );

        $this->addDisplayGroup(array(
            'candidateDocument'
            , 'candidateBibTex'
            , 'candidatePageFrom'
            , 'candidateLineFrom'
            , 'candidatePageTo'
            , 'candidateLineTo'
            , 'candidateText'
                )
                , 'candidateGroup'
                , array('legend' => 'Candidate Information', 'class' => 'two-column-form')
        );

        $this->addDisplayGroup(array(
            'sourceDocument'
            , 'sourceBibTex'
            , 'sourcePageFrom'
            , 'sourceLineFrom'
            , 'sourcePageTo'
            , 'sourceLineTo'
            , 'sourceText'
                )
                , 'sourceGroup'
                , array('legend' => 'Source Information', 'class' => 'two-column-form')
        );

        $this->addElement(
                'hidden', 'candidate', array(
            'required' => false,
            'ignore' => true,
            'autoInsertNotEmptyValidator' => false,
            'decorators' => array(
                array(
                    'HtmlTag', array(
                        'tag' => 'div',
                        'id' => 'compared_candidate_Text',
                        'class' => 'wmd-panel',
                        'style' => ' float:left; width = "500 px"'
                    )
                )
            )
                )
        );
        $this->candidate->clearValidators();

        $this->addElement(
                'hidden', 'source', array(
            'required' => false,
            'ignore' => true,
            'autoInsertNotEmptyValidator' => false,
            'decorators' => array(
                array(
                    'HtmlTag', array(
                        'tag' => 'div',
                        'id' => 'compared_source_Text',
                        'class' => 'wmd-panel',
                        'style' => ' width = "500 px"'
                    )
                )
            )
                )
        );
        $this->source->clearValidators();

        $this->addElements(array(
            $submitElement
        ));
    }

}
