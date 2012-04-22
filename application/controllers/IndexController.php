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
class IndexController extends Unplagged_Controller_Action{

  public function init(){
    parent::init();
    
    Zend_Layout::getMvcInstance()->sidebar = 'default';
  }

  public function indexAction(){
    //Zend_Registry::get('Log')->debug('Index');
    
    /*
    // partial 1
    $data["page"] = $this->_em->getRepository('Application_Model_Document_Page')->findOneById(21);
    $data["linePos"] = 1;
    $data["characterPos"] = 1;
    $posStart = new Application_Model_Document_Page_Position($data);
    $this->_em->persist($posStart);
    
    unset($data);
    $data["page"] = $this->_em->getRepository('Application_Model_Document_Page')->findOneById(21);
    $data["linePos"] = 5;
    $data["characterPos"] = 10;
    $posEnd = new Application_Model_Document_Page_Position($data);
    $this->_em->persist($posEnd);
    
    unset($data);
    $data["title"] = "Hello World";
    $data["posStart"] = $posStart;
    $data["posEnd"] = $posEnd;
    $partialPlag = new Application_Model_Document_Fragment_Partial($data);
    
    
    // partial 2
    $data["page"] = $this->_em->getRepository('Application_Model_Document_Page')->findOneById(21);
    $data["linePos"] = 1;
    $data["characterPos"] = 1;
    $posStart = new Application_Model_Document_Page_Position($data);
    $this->_em->persist($posStart);
    
    unset($data);
    $data["page"] = $this->_em->getRepository('Application_Model_Document_Page')->findOneById(21);
    $data["linePos"] = 5;
    $data["characterPos"] = 10;
    $posEnd = new Application_Model_Document_Page_Position($data);
    $this->_em->persist($posEnd);
    
    unset($data);
    $data["title"] = "Hello World 2";
    $data["posStart"] = $posStart;
    $data["posEnd"] = $posEnd;
    $partialSource = new Application_Model_Document_Fragment_Partial($data);
    
    
    // fragment
    unset($data);
    $data["plag"] = $partialPlag; 
    $data["source"] = $partialSource;
    $data["title"] = "my fragment";

    $fragment = new Application_Model_Document_Fragment($data);
    $this->_em->persist($fragment);
    $this->_em->flush(); 
    */

    

    
    // update a fragment
   // $fragment = $this->_em->getRepository('Application_Model_Document_Fragment')->findOneById(3238);    
   // $fragment->setTitle(rand(0,10000));
   // $this->_em->persist($fragment);
   // $this->_em->flush();
    
    /*
    //$post = new Application_Model_BlogPost();
    $post = $this->_em->getRepository('Application_Model_BlogPost')->findOneById(1);
    $post->setTitle(rand(0,10000));
    $post->setBody("test");
     $this->_em->persist($post);
    $this->_em->flush();
     */
     
  }


}
