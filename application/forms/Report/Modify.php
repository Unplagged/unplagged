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
 * Contains all elements for creating a new fragment.
 */
class Application_Form_Report_Modify extends Zend_Form {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Creates the form to register a new user.
     * @see Zend_Form::init()
     */
    public function init() {
        $this->setMethod('post');
        $this->setAction('/report/create');
        
        $reportTitle = new Zend_Form_Element_Text('reportTitle');
        $reportTitle->setLabel("Report Titel");
        $reportTitle->setValue('Dokumentation von Plagiaten in der Dissertation {...} von {...}. Berlin. 2012');
        $reportTitle->setIgnore(true);

        $groupName = new Zend_Form_Element_Text('reportGroupName');
        $groupName->setLabel("Name der Arbeitsgruppe");
        $groupName->setValue('VroniPlag');
        $groupName->setIgnore(true);

        $intro = new Zend_Form_Element_Textarea('reportIntroduction');
        $intro->setLabel('Bitte geben sie den Einleitungstext für den Bericht ein.');
        $intro->setValue('Gegenstand dieses Berichts ist die Untersuchung der {...} im Verlag {...} veröffentlichten Dissertation.');
        
        $evaluation = new Zend_Form_Element_Textarea('reportEvaluation');
        $evaluation->setLabel('Bitte geben sie den Text für die Vorläufige Bewertung ein.');
        $evaluation->setValue('Bezüglich der in diesem Bericht dokumentierten Plagiate lässt sich zusammenfassend feststellen:');
        $evaluation->setAttrib('rows', '500');
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Save');
        $submit->setOptions(array('class' => 'btn btn-primary'));
        
        $this->addElements(array(
            $groupName
            , $reportTitle
            , $intro
            , $evaluation
        ));

        $this->addDisplayGroup(
                array(
            'reportTitle'
            , 'reportGroupName'
                ), 'personalGoup', array(
            'legend' => 'Deckblatt Informationen'
                )
        );

        $this->addDisplayGroup(
            array(
                'reportIntroduction'                
            ), 
            'introductionGroup', 
            array('legend' => 'Einleitung')
        );
        
        $this->addDisplayGroup(
            array(
              'reportEvaluation'  
            ),
            'evaluationGroup',
            array('legend' => 'Vorläufige Bewertung')
        );

        $this->addElements(array(
            $submit
        ));
    }

}