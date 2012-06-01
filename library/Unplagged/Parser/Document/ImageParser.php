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

  public function parseToDocument(Application_Model_File $file, $language, Application_Model_Document $document = null, Application_Model_Task &$task = null){
    try{
      $inputFileLocation = $file->getFullPath();
      $imagemagickTempPath = TEMP_PATH . DIRECTORY_SEPARATOR . 'imagemagick';

      if($file->getExtension() === 'pdf'){
        $outputFileLocation = $imagemagickTempPath . DIRECTORY_SEPARATOR . $file->getId() . '-%d.tif';
      }else{
        $outputFileLocation = $imagemagickTempPath . DIRECTORY_SEPARATOR . $file->getId() . '.tif';
      }
      $adapter = new Unplagged_Parser_Document_ImagemagickAdapter($inputFileLocation, $outputFileLocation);
      $adapter->execute();
      //var_dump($outputFileLocation);
      //die('hier');
      // create the document
      if(!$document){
        $data["file"] = $file;
        $data["title"] = $file->getFilename();
        $document = new Application_Model_Document($data);
      }
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
      $prevPerc = 0; // percentage of the previous iteration
      $handler = opendir($imagemagickTempPath);
      while($tifFile = readdir($handler)){
        if($tifFile != "." && $tifFile != ".."){
          if(preg_match('/' . $file->getId() . '(-(\d)*){0,1}.tif/', $tifFile)){
            // for loop over pages
            $fileData = array('filename'=>$tifFile, 'localFilename'=>$tifFile, 'extension'=>'tif', 'mimeType'=>'image/tiff', 'location'=>BASE_PATH . DIRECTORY_SEPARATOR . 'temp/imagemagick' . DIRECTORY_SEPARATOR);
            $tempFile = new Application_Model_File($fileData);
            
            $page = $parser->parseToPage($tempFile, $language);
            $page->setPageNumber($i);
            $document->addPage($page);

            $this->_em->persist($page);

            // remove the converted imaged, because it should be in the database now
            $tifPath = $imagemagickTempPath . DIRECTORY_SEPARATOR . $tifFile;
            unlink($tifPath);

            if($task){
              $perc = round($i * 1.0 / $pagesCount * 100 / 10) * 10;
              if($perc > $prevPerc){
                $task->setProgressPercentage($perc);
                $this->_em->persist($task);
                $this->_em->flush();
                $prevPerc = $perc;
              }
            }

            $i++;
          }
        }
      }

      $this->_em->persist($document);
      $this->_em->flush();

      return $document;
    }catch(Exception $e){
      //parsing wasn't successful
      return null;
    }
  }

}

?>