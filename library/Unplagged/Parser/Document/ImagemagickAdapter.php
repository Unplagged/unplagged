<?php

/**
 * This class wraps the command line calls to the Imagemagick convert tool.
 * 
 * It can be used to convert images to different types.
 * 
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class Unplagged_Parser_Document_ImagemagickAdapter{

  // for pdfs
  // convert -limit memory 1mb -limit map 1mb -colorspace RGB -density 300 /Users/benjamin/Sites/unplagged.local/application/storage/files/57.pdf /Users/benjamin/Sites/unplagged.local/temp/imagemagick/57-%d.tif

  private $imagemagickCall;
  private $inputFilePath;
  private $outputFilePath;

  public function __construct($inputFilePath, $outputFilePath){
    $message = $this->checkForInvalidArguments($inputFilePath, $outputFilePath);

    if($message === false){
      $this->inputFilePath = $inputFilePath;
      $this->outputFilePath = $outputFilePath;
      $this->imagemagickCall = Zend_Registry::get('config')->parser->imagemagickPath;
      $pdf = true;
      if($pdf){
        //$this->imagemagickCall .= " -limit memory 1mb -limit map 1mb -colorspace RGB -density 300";
       // $this->imagemagickCall = "gs -o page_%03d.tif -sDEVICE=tiffg4 -r720x720 5.pdf ";
      }
    }else{
      throw new InvalidArgumentException($message);
    }
  }

  public function execute(){
    $output = array();
    //$command = $this->imagemagickCall . ' ' . $this->inputFilePath . ' ' . $this->outputFilePath;
    $command = "gs -o " . $this->outputFilePath . " -sDEVICE=tiffg4 " . $this->inputFilePath;
    echo $command;
    //@todo: escapeshellcmd
    if(APPLICATION_ENV == "benjamin"){
      putenv("PATH=" . "/usr/local/bin");
    }
    $ret = system($command, $returnVal);

    if($returnVal == 0){
      $directoryAndFile = explode(DIRECTORY_SEPARATOR, $this->outputFilePath);
      
      $file = array_pop($directoryAndFile);
      $input = new Zend_Filter_Input(array('file'=>'Digits'), null, array("file" => $file));

      $directory = implode(DIRECTORY_SEPARATOR, $directoryAndFile);
      $handler = opendir($directory);
      while($file = readdir($handler)){
        if($file != "." && $file != ".."){
  //        echo $file . '\n';
          // check if 59, or 59-0 or 59-1,...
          if(preg_match('/' . $input->file . '(-(\d)*){0,1}.tif/', $file)){
            chmod($directory . DIRECTORY_SEPARATOR . $file, 0755);
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

    if(!file_exists($inputFileLocation)){
      $message = 'The input file doesn\'t exist.';
    }elseif(!is_string($outputFileLocation) || $outputFileLocation === ''){
      $message = 'The output file name needs to be specified as a string';
    }elseif(!is_writable(dirname($outputFileLocation))){
      $message = 'The location for the output file is not writeable.';
    }

    return $message;
  }

}

?>
