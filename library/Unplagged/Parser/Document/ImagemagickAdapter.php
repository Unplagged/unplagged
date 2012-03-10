<?php
/**
 * This class wraps the command line calls to the Imagemagick convert tool.
 * 
 * It can be used to convert images to different types.
 * 
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class Unplagged_Parser_Document_ImagemagickAdapter{
  
  private $imagemagickCall;
  private $inputFilePath;
  private $outputFilePath;
  
  public function __construct($inputFilePath, $outputFilePath){
    $message = $this->checkForInvalidArguments($inputFilePath, $outputFilePath);
    
    if($message === false){
      $this->inputFilePath = $inputFilePath;
      $this->outputFilePath = $outputFilePath;
      $this->imagemagickCall = Zend_Registry::get('config')->parser->imagemagickPath;
    } else {
      throw new InvalidArgumentException($message);
    }
  }
  
  public function execute(){
    $output = array();
    $command = $this->imagemagickCall . ' ' . $this->inputFilePath . ' ' . $this->outputFilePath;
    
    //@todo: escapeshellcmd
    exec($command, $output, $returnVal);
    if($returnVal == 0) {
      chmod($this->outputFilePath, 0755);
      return true;
    } else {
      throw new Exception("Image could not be converted.");
    }    
  }
  
  private function checkForInvalidArguments($inputFileLocation, $outputFileLocation){
    $message = false;

    if(!file_exists($inputFileLocation)){
      $message = 'The input file doesn\'t exist.';
    }elseif(!is_string($outputFileLocation) || $outputFileLocation===''){
      $message = 'The output file name needs to be specified as a string';
    } elseif(!is_writable(dirname($outputFileLocation))){
      $message = 'The location for the output file is not writeable.';
    }

    return $message;
  }
}
?>
