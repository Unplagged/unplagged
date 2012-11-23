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
 * This class wraps the command line calls to the Imagemagick convert tool.
 * 
 * It can be used to convert images to different types.
 * 
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class Unplagged_Parser_Document_ImagemagickAdapter{

  private $command;
  private $inputFilePath;
  private $outputFilePath;

  public function __construct($inputFilePath, $outputFilePath, $extension = null){
    $message = $this->checkForInvalidArguments($inputFilePath, $outputFilePath);

    if($message === false){
      $this->inputFilePath = $inputFilePath;
      $this->outputFilePath = $outputFilePath;

      if($extension == 'pdf'){
        // use ghotscript for pdfs, because it is much faster to call it directly than through imagemagick
        $this->command = sprintf(Zend_Registry::get('config')->parser->ghostscriptPath, $this->outputFilePath, $this->inputFilePath);
      }else{
        $this->command = sprintf(Zend_Registry::get('config')->parser->imagemagickPath, $this->inputFilePath, $this->outputFilePath);;
      }
    }else{
      throw new InvalidArgumentException($message);
    }
  }

  public function execute(){
    $output = array();

    $ret = system($this->command, $returnVal);

    if($returnVal == 0){
      $directoryAndFile = pathinfo($this->outputFilePath);

      $file = $directoryAndFile['basename'];
      $input = new Zend_Filter_Input(array('file'=>'Digits'), null, array('file'=>$file));
      $directory = $directoryAndFile['dirname'] . DIRECTORY_SEPARATOR;
      $handler = opendir($directory);

      while($file = readdir($handler)){
        if($file != "." && $file != ".."){
          // check if 59, or 59-0 or 59-1,...
          if(preg_match('/' . $input->file . '(-(\d)*){0,1}.tif/', $file)){
            chmod($directory . $file, 0755);
          }
        }
      }
      return true;
    }else{
      throw new Exception("Image could not be converted.");
    }
  }

  private function checkForInvalidArguments($inputFileLocation, $outputFileLocation){
    $message = false;

    if(!is_readable($inputFileLocation)){
      $message = 'The input file doesn\'t exist.';
    }elseif(!is_string($outputFileLocation) || $outputFileLocation === ''){
      $message = 'The output file name needs to be specified as a string';
    }elseif(!is_writable(dirname($outputFileLocation))){
      $message = 'The location for the output file is not writeable.';
    }

    return $message;
  }

}