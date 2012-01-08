<?php

/**
 * File for class {@link TesseractAdapter}.
 */

/**
 * This class wraps the command line calls to the tesseract ocr tool.
 * 
 * @author Dominik Horb <dominik.horb@googlemail.com>
 * 
 * @todo handing over the tesseract path from user input is a possible security issue, because this gets executed on the
 * command line, we should at least think about, whether we want to keep it that way
 */
class Unplagged_Parser_TesseractAdapter{

  private $tesseractCall;
  private $inputFileLocation;
  private $outputFileLocation;
  private $language;

  /**
   * This constructor needs to be provided with all arguments, that would also make a normal command line call of 
   * tesseract work. The documentation of the projects is a bit sparse, but some parts can be found 
   * {@link http://code.google.com/p/tesseract-ocr/ at google code}.
   * 
   * @param string $inputFileLocation The name of the input tiff file.
   * @param string $outputFileLocation The name of the output file name, the extension will be .txt.
   * @param string $tesseractPath The path to the tesseract tool, must be callable via the command line. If nothing is specified
   * it is assumed, that tesseract can be called from anywhere.
   * @param string $language The language which tesseract should use for it's parsing.
   */
  public function __construct($inputFileLocation, $outputFileLocation, $language = 'deu'){
    $message = $this->checkConstructorArguments($inputFileLocation, $outputFileLocation);

    if($message === ''){
      $this->inputFileLocation = $inputFileLocation;
      $this->outputFileLocation = $outputFileLocation;
      $this->tesseractCall = Zend_Registry::get('config')->parser->tesseractPath;
      $this->language = $language;
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
    $command = $this->tesseractCall . ' ' . $this->inputFileLocation . ' ' . $this->outputFileLocation . ' -l ' . $this->language;

    exec($command, $output);
  }

  /**
   * This function returns true if it could confirm, that tesseract is working.
   * 
   * @return bool 
   * @todo See note in class doc about possible security issues with executing any given command.
   */
  public static function checkTesseract($command = 'tesseract'){
    //the 2>&1 bit is to supress output on stderr
    $output = shell_exec($command . ' 2>&1');

    //the common bit of the ouput between windows and Linux seems to be 'Usage:tesseract'
    if(stripos($output, 'Usage:tesseract') !== false){
      return true;
    }else{
      return false;
    }
  }

  /**
   * @param string $inputFileLocation
   * @param string $outputFileLocation
   * @return string 
   */
  private function checkConstructorArguments($inputFileLocation, $outputFileLocation){
    $message = '';

    if(!file_exists($inputFileLocation)){
      $message = 'The input file doesn\'t exist.';
    }elseif(!is_string($outputFileLocation) || empty($outputFileLocation)){
      $message = 'The output file name needs to be specified as a string';
    }

    return $message;
  }

}

?>