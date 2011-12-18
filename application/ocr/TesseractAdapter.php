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
    if(is_dir($folderPath) && file_exists($inputFileName) && is_string($outputFileName)){
      $this->folderPath = $folderPath;
      $this->tesseractCall = $tesseractPath;
      $this->outputFileName = $outputFileName;
      $this->language = $language;
      $this->inputFileName = $inputFileName;
    }
    else{
      throw new InvalidArgumentException(); 
    }
  }
  
}

?>
