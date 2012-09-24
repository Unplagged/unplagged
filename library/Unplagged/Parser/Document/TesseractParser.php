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
class Unplagged_Parser_Document_TesseractParser {

  public function __construct(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
  }

  public function parseToDocument(Application_Model_File $file, $language, Application_Model_Document $document = null, $taskId = null){
    // init document
    if(!$document){
      $data["file"] = $file;
      $data["title"] = $file->getFilename();
      $data["language"] = $language;

      $document = new Application_Model_Document($data);
      $this->_em->persist($document);
      $this->_em->flush();
    }

    $parser = new Unplagged_Parser_Page_TesseractParser();
    $page = $parser->parseToPage($file, $language);

    $page->setPageNumber(1);
    $this->_em->persist($page);
    $this->_em->flush();


    return $document;
  }

}
?>