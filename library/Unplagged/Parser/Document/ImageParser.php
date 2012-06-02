<?php

/**
 * File for class {@link Unplagged_Parser_ImageParser}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
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
