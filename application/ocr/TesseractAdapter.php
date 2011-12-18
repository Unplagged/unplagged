<?php
/**
 * File for class {@link TesseractAdapter}.
 */

/**
 * This class wraps the command line calls to the tesseract ocr tool.
 * 
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class TesseractAdapter{
  
  private $tesseractCall;
  private $folderPath;
  private $inputFileName;
  private $outputFileName;
  private $language;
  
  /**
   *
   * @param string $folderPath The path to the folder where the input file is located and where the output .txt file will be stored.
   * @param string $inputFileName The name of the input tiff file.
   * @param string $outputFileName The name of the output file name, the extension will be .txt.
   * @param string $tesseractPath The path to the tesseract tool, must be callable via the command line. If nothing is specified
   * it is assumed, that tesseract can be called from anywhere.
   * @param string $language The language which tesseract should use for it's parsing.
   */
  public function __construct($folderPath, $inputFileName, $outputFileName, $tesseractPath = 'tesseract', $language = 'deu'){
    $message = $this->checkConstructorArguments($folderPath, $inputFileName, $outputFileName);
    
    if($message === ''){
      $this->folderPath = $folderPath;
      $this->tesseractCall = $tesseractPath;
      $this->outputFileName = $outputFileName;
      $this->language = $language;
      $this->inputFileName = $inputFileName;
    }
    else{
      throw new InvalidArgumentException($message); 
    }
  }
  
  /**
   *
   * @param string $folderPath
   * @param string $inputFileName
   * @param string $outputFileName
   * @return string 
   */
  private function checkConstructorArguments($folderPath, $inputFileName, $outputFileName){
    $message = '';
    
    if(!file_exists($folderPath . $inputFileName)){
      $message = 'The input file doesn\'t exist.';
    }elseif(!is_string($outputFileName) && !empty($outputFileName)){
      $message = 'The output file name needs to be specified as a string';
    }

    return $message;
  }
  
  /**
   * This function executes the command line call.
   * 
   * The result should be a .txt file with the name that was provided to the constructor as the $outputFileName.
   */
  public function execute(){
    $output = array();
    $command = $this->tesseractCall . ' ' . $this->folderPath . $this->inputFileName . ' ' . $this->folderPath . $this->outputFileName . ' -l ' . $this->language;
    
    exec($command, $output);
  }
  
}

?>
