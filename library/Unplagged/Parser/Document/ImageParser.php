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
    try{
      $inputFileLocation = $file->getAbsoluteLocation() . DIRECTORY_SEPARATOR . $file->getId() . "." . $file->getExtension();
      $imagemagickTempPath = TEMP_PATH . DIRECTORY_SEPARATOR . 'imagemagick';

      if($file->getExtension() == "pdf"){
        $outputFileLocation = $imagemagickTempPath . DIRECTORY_SEPARATOR . $file->getId() . '-%03d.tif';
      }else{
        $outputFileLocation = $imagemagickTempPath . DIRECTORY_SEPARATOR . $file->getId() . '.tif';
      }
      $adapter = new Unplagged_Parser_Document_ImagemagickAdapter($inputFileLocation, $outputFileLocation);
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

      $pagesCount = 1;
      // iterate over converted files and ocr them
      $handler = opendir($imagemagickTempPath);
      while($tifFile = readdir($handler)){
        if($tifFile != "." && $tifFile != ".."){
          if(preg_match('/' . $file->getId() . '(-(\d)*){0,1}.tif/', $tifFile)){
            $pagesCount++;
          }
        }
      }

      $i = 1;
      $handler = opendir($imagemagickTempPath);
      while($tifFile = readdir($handler)){
        if($tifFile != "." && $tifFile != ".."){
          if(preg_match('/' . $file->getId() . '(-(\d)*){0,1}.tif/', $tifFile)){

            $fileData = array('filename'=>$tifFile, 'extension'=>'tif', 'mimeType'=>'image/tiff', 'location'=>'temp/imagemagick');
            $tempFile = new Application_Model_File($fileData);
            $tempFile->setId($file->getId());

            $page = $parser->parseToPage($tempFile, $language);
            $page->setPageNumber($i);
            $page->setDocument($document);

            $this->_em->persist($page);
            unset($tempFile);

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
      }

      // iterate over converted files and remove them
      $handler = opendir($imagemagickTempPath);
      while($tifFile = readdir($handler)){
        if($tifFile != "." && $tifFile != ".."){
          if(preg_match('/' . $file->getId() . '(-(\d)*){0,1}.tif/', $tifFile)){
            unlink($imagemagickTempPath . DIRECTORY_SEPARATOR . $tifFile);
          }
        }
      }

      //$this->_em->persist($document);
      //$this->_em->flush();

      return $document;
    }catch(Exception $e){
      //parsing wasn't successful
      return null;
    }
  }

}

?>