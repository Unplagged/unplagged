<?php
class Unplagged_Simtext_SimtextAdapter{

  private $simtextCall;
  private $inputFileLocation1;
  private $inputFileLocation2;
  private $outputFileLocation;
  private $outputFile;
  //private $language;

 
  public function __construct($inputFileLocation1, $inputFileLocation2, $outputFileLocation, $outputFile){
    $message = $this->checkForInvalidArguments($inputFileLocation1, $inputFileLocation2, $outputFileLocation);

    if($message === false){
      $this->inputFileLocation1 = $inputFileLocation1;
	    $this->inputFileLocation2 = $inputFileLocation2;
	    $this->outputFileLocation = $outputFileLocation;
      $this->outputFile = $outputFile;
      //@todo it would probably be better to supply this also via a constructor argument
      //this would ensure the best independece from the rest of the application
	 
      $this->simtextCall = Zend_Registry::get('config')->simtext->simtextPath;
      //$this->language = $language;
    }else{
      throw new InvalidArgumentException($message);
    }
  }

  /**
   * This function executes the command line call.
   * 
   * The result should be a .txt file with the name that was provided to the constructor as the $outputFileName.
   */
  public function execute(){
    $output = array();
    $command = $this->simtextCall . ' ' . $this->inputFileLocation1 . ' ' . $this->inputFileLocation2;// . ' ' .$this->outputFileLocation;

    exec($command, $output);
    
    return $output;
  }

  
  /**
   * @param string $inputFileLocation
   * @param string $outputFileLocation
   * @return string|bool  False if the arguments are correct or an error message, if something went wrong.
   */
  private function checkForInvalidArguments($inputFileLocation1, $inputFileLocation2, $outputFileLocation){
    $message = false;

    if(!file_exists($inputFileLocation1)){
      $message = 'The 1. input file doesn\'t exist.';
    }elseif(!file_exists($inputFileLocation2)){
      $message = 'The 2. input file doesn\'t exist.';
    }elseif(!is_string($outputFileLocation) || $outputFileLocation===''){
      $message = 'The output file name needs to be specified as a string';
    } elseif(!is_writable(dirname($outputFileLocation))){
      $message = 'The location for the output file is not writeable.';
    }

    return $message;
  }

}
?>