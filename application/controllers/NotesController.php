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
 * 
 */
class NotesController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_em = Zend_Registry::getInstance()->entitymanager;
        $this->_defaultNamespace = new Zend_Session_Namespace('Default');
    }
    
    public function entryAction()
    {
        $ui = 1;
        $ci = 6;
        if($this->_request->isPost())
        {
            /*$qb = $this->_em->createQueryBuilder();
            $qb->add('select', 'n.notesId')
                    ->add('from','Application_Model_Notes n')
                    ->where("n.userId =".$ui." AND n.caseId=".$ci);
            $qb->setMaxResults(1);
            $result = $qb->getQuery()->getResult();*/
                        
            $notes = new Application_Model_Notes();
            $notes->setNotesId(NULL);
            $notes->setNotes($this->getRequest()->getParam('notesTextareaInput'));//  $user->setFirstname($this->getRequest()->getParam('firstname'));
            $notes->setUserId($ui);
            $notes->setCaseId($ci);            
            $this->_em->persist($notes);
            $this->_em->flush();
            
            /*$qb = $this->_em->createQueryBuilder();
            $qb->add('INSERT','INTO Application_Model_Notes VALUES (NULL,'.$ui.',\''
                    .$this->getRequest()->getParam('notesTextareaInput').'\','
                    .$ci.')');*/
            //$qb->getQuery()->getResult();
            //$this->_em->flush();
        }
        /*else{*/
                $qb = $this->_em->createQueryBuilder();
                $qb->add('select', "n.notes")
                    ->add('from', 'Application_Model_Notes n')
                    ->where("n.userId =".$ui." AND n.caseId=".$ci);

                $dbresults = $qb->getQuery()->getResult();
                $results = '';
                foreach($dbresults as $key=>$value){
                    $results .= $value['notes'] . "\n";
                }
                $this->view->entryForm = new Application_Form_Notes_Entry($results);
        /*}*/
        
    }
    
    
 }
?>
