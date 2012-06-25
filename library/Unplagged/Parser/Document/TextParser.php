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
class Unplagged_Parser_Document_TextParser implements Unplagged_Parser_Document_Parser{

  public function __construct(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
  }

  public function parseToDocument(Application_Model_File $file, $language, $documentId = null, $taskId = null){
    // create temp folder
    $imagemagickTempPath = TEMP_PATH . DIRECTORY_SEPARATOR . 'imagemagick' . DIRECTORY_SEPARATOR . uniqid();
    mkdir($imagemagickTempPath, 0777);
    $document = $this->_em->getRepository('Application_Model_Document')->findOneById($documentId);

    try{
      // create the document
      if(!$documentId){
        $data["file"] = $file;
        $data["title"] = $file->getFilename();

        $document = new Application_Model_Document($data);
        $this->_em->persist($document);
        $this->_em->flush();
        $documentId = $document->getId();
      }

      // store task and document ids, because we need to clear the entity manager from time to time and would lose the objects
      $task = $this->_em->getRepository('Application_Model_Task')->findOneById($taskId);
      $document = $this->_em->getRepository('Application_Model_Document')->findOneById($documentId);

      $parser = new Unplagged_Parser_Page_TextParser();

      $page = $parser->parseToPage($file, $language);
      $page->setPageNumber(1);
      $page->setDocument($document);

      $this->_em->persist($page);

      if($taskId){
        $perc = 100;
        $task->setProgressPercentage($perc);
        $this->_em->persist($task);
        $task = $this->_em->getRepository('Application_Model_Task')->findOneById($taskId);
      }

      return $document;
    }catch(Exception $e){
      //parsing wasn't successful
      return null;
    }
  }

}

?>
