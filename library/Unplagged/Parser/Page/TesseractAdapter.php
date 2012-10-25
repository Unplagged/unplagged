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
 * This class wraps the command line calls to the tesseract ocr tool.
 * 
 * @author Unplagged
 * 
 * @todo handing over the tesseract path from user input is a possible security issue, because this gets executed on the
 * command line, we should at least think about, whether we want to keep it that way
 */
final class Unplagged_Parser_Page_TesseractAdapter{

  private static $tesseractPath;
  
  private $inputFileLocation;
  private $outputFileLocation;
  private $language;

  // tesseract uses a 3 character language notation, although we use two characters, so let's map them
  private $installedLanguages;
  
  /**
   * This constructor needs to be provided with all arguments, that would also make a normal command line call of 
   * tesseract work. The documentation of the projects is a bit sparse, but some parts can be found 
   * {@link http://code.google.com/p/tesseract-ocr/ at google code}.
   * 
   * @param string $inputFileLocation The name of the input tiff file.
   * @param string $outputFileLocation The name of the output file name, the extension .txt is added automatically.
   * @param string $tesseractPath The path of the directory from which the tesseract command can be called. 
   * If nothing is specified, it is assumed, that the tesseract call works from everywhere, beause it can be found via
   * the $PATH environment variable. Should have a trailing slash if specified.
   * @param string $language The language which tesseract should use as the basis for it's parsing, silently falls back 
   * to english if the given language isn't found.
   */
  public function __construct($inputFileLocation, $outputFileLocation, $language = 'en', $tesseractPath = '', array $installedLanguages = array('en'=>'eng')){
    $message = $this->checkForInvalidArguments($inputFileLocation, $outputFileLocation);
    $this->installedLanguages = $installedLanguages;
    
    if($message === false){
      $this->inputFileLocation = $inputFileLocation;
      $this->outputFileLocation = $outputFileLocation;
      self::$tesseractPath = $tesseractPath;
      
      if(array_key_exists($language, $this->installedLanguages)){
        $this->language = $this->installedLanguages[$language];
      }else {
        //fallback to english, because it should always be present, as it is the default language, with which tesseract gets shipped
        $this->language = 'eng';
      }
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
    $command = self::$tesseractPath . 'tesseract ' . $this->inputFileLocation . ' ' . $this->outputFileLocation . ' ' . $this->language;
    exec($command, $op, $returnVal);

    if(file_exists($this->outputFileLocation . '.txt')){
      return true;
    }else{
      return false;
    }
  }

  /**
   * This function returns true if it could confirm, that tesseract is working.
   * 
   * @return bool
   */
  public static function checkTesseract(){
    //we simply try to call tesseract without arguments
    //the 2>&1 bit is to supress output on stderr
    $output = shell_exec(self::$tesseractPath . 'tesseract 2>&1');

    //the common bit of the ouput between windows and Linux seems to be 'Usage:'
    if(stripos($output, 'Usage:') !== false){
      return true;
    }else{
      return false;
    }
  }

  /**
   * @param string $inputFileLocation
   * @param string $outputFileLocation
   * @return string|bool  False if the arguments are correct or an error message, if something went wrong.
   */
  private function checkForInvalidArguments($inputFileLocation, $outputFileLocation){
    $message = false;

    if(!file_exists($inputFileLocation)){
      $message = 'The input file doesn\'t exist.';
    }elseif(!is_string($outputFileLocation) || $outputFileLocation === '' || file_exists($outputFileLocation)){
      $message = 'The output file name needs to be specified as a string and can not exist.';
    }elseif(!is_writable(dirname($outputFileLocation))){
      $message = 'The location for the output file is not writeable.';
    }

    return $message;
  }

}