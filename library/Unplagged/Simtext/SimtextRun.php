<?php

/**
 * File for class {@link Unplagged_Parser_TesseractParser}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class Unplagged_Simtext_SimtextRun{

  public function __construct(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
  }

  /**
   * This function returns true if it could confirm, that tesseract is working.
   * @param $file The previous uploaded file.
   * @param $language The language of the uploaded file.
   * 
   * @return Application_Model_Document
   */
  public function runSimtext($text1_path, $text2_path, $report_path){

    try{      

      $inputFileLocation1 = $text1_path;
	  $inputFileLocation2 = $text2_path;
	  $outputFileLocation = $report_path;
	  
	  // output of simtext will be saved in an array
      $output = array();

	  // report is creating with simtext
      $adapter = new Unplagged_Simtext_SimtextAdapter($inputFileLocation1, $inputFileLocation2, $outputFileLocation, $output);
      //$adapter->execute();
	
	  // try to read content from output array and save to a file
	  file_put_contents($outputFileLocation, $output);
	  
	  // init file
	  // 
       $data["size"] = filesize($outputFileLocation); 
	   $data["mimetype"] = 'application/octet-stream';
       $data["filename"] = 'test_report.txt';
       $data["extension"] = 'txt';
       $data["location"] = 'storage/reports/';
      //$data["title"] = $file->getFilename();
	  
	  // only content can be read --> but how will this be created in files?
	  //$content = nl2br(file_get_contents($outputFileLocation));

      $file = new Application_Model_File($data);
      //$this->_em->persist($file);
      //$this->_em->flush();
	  
	  //chmod($report_path, 0755);
	  
      return $file;
	  //return $outputFileLocation;
	  //return $inputFileLocation1;
	  
    }catch(InvalidArgumentException $e){
      //text analysing wasn't successful
      return null;
    }
  }

}
?>