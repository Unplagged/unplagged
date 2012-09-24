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
class Unplagged_Parser_Document_ImageParser implements Unplagged_Parser_Document_Parser{

  public function __construct(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
  }

  public function parseToDocument(Application_Model_File $file, $language, $documentId = null, $taskId = null){
 
    // create temp folder
    $imagemagickTempPath = TEMP_PATH . DIRECTORY_SEPARATOR . 'imagemagick' . DIRECTORY_SEPARATOR . uniqid();
    mkdir($imagemagickTempPath, 0777);
    
    try{
      $inputFileLocation = $file->getFullPath();

      if($file->getExtension() == "pdf"){
        $outputFileLocation = $imagemagickTempPath . DIRECTORY_SEPARATOR . $file->getId() . '-%03d.tif';
      }else{
        $outputFileLocation = $imagemagickTempPath . DIRECTORY_SEPARATOR . $file->getId() . '.tif';
      }

      $adapter = new Unplagged_Parser_Document_ImagemagickAdapter($inputFileLocation, $outputFileLocation, $file->getExtension());

      $adapter->execute();

      // create the document
      if(!$documentId){
        $data["file"] = $file;
        $data["title"] = $file->getFilename();
        $data["language"] = $language;
      
        $document = new Application_Model_Document($data);
        $this->_em->persist($document);
        $this->_em->flush();
        $documentId = $document->getId();
      }

      // store task and document ids, because we need to clear the entity manager from time to time and would lose the objects
      $task = $this->_em->getRepository('Application_Model_Task')->findOneById($taskId);
      $document = $this->_em->getRepository('Application_Model_Document')->findOneById($documentId);

      $parser = new Unplagged_Parser_Page_TesseractParser();

      $pagesCount = Unplagged_Helper::countFilesInDirectory($imagemagickTempPath . DIRECTORY_SEPARATOR, '*.tif');

      $i = 1;
      $handler = opendir($imagemagickTempPath);
      while($tifFile = readdir($handler)){
        if($tifFile != "." && $tifFile != ".."){
            echo 'read:' . $tifFile . "\n";
          // for loop over pages
          $fileData = array('filename'=>$tifFile, 'localFilename'=>$tifFile, 'extension'=>'tif', 'mimeType'=>'image/tiff', 'location'=>$imagemagickTempPath . DIRECTORY_SEPARATOR);
          $tempFile = new Application_Model_File($fileData);

          $page = $parser->parseToPage($tempFile, $language);
          $page->setPageNumber($i);
          $page->setDocument($document);

          $this->_em->persist($page);

          // clear memory every ten pages to free it up
          if($i % 10 == 0){
            // update the task perc
            if($taskId){
              $perc = round($i * 1.0 / $pagesCount * 100 / 10) * 10;
              $task->setProgressPercentage($perc);
              $this->_em->persist($task);
            }

            $this->_em->flush();
            $this->_em->clear();

            if($taskId){
              $task = $this->_em->getRepository('Application_Model_Task')->findOneById($taskId);
            }
            $document = $this->_em->getRepository('Application_Model_Document')->findOneById($documentId);
          }

          $i++;
        }
      }
      Unplagged_Helper::removeDirectory($imagemagickTempPath);

      $document = $this->_em->getRepository('Application_Model_Document')->findOneById($documentId);
      return $document;
    }catch(Exception $e){
      Unplagged_Helper::removeDirectory($imagemagickTempPath);
      //parsing wasn't successful
      return null;
    }
  }

}
?>
