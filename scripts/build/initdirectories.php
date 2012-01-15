<?php
define('BASE_PATH', realpath(dirname(__FILE__) . '/../../'));

//add directories that should be created here
//make sure to include them in the right order, so that dependencies occur beforehand
$directories = array(
  'application' . DIRECTORY_SEPARATOR . 'storage', //@deprecated
  'application' . DIRECTORY_SEPARATOR . 'files', //@deprecated
  'data',
  'data' . DIRECTORY_SEPARATOR . 'uploads', 
  'temp',
  'temp' . DIRECTORY_SEPARATOR . 'ocr',
  'temp' . DIRECTORY_SEPARATOR . 'imagemagick'
);

foreach($directories as $directory){
  echo 'The creation of ' . $directory . ' ';
  $result = createDirectory($directory) ? 'succeded' : 'failed';
  echo $result . "\n";
}

/**
 * Creates the given directory if it didn't exist and sets the permissions to 755.
 * 
 * @param type $directory
 * @return bool A boolean indicating the success or failure of the operation.
 */
function createDirectory($directory){
  $fullPath = BASE_PATH . DIRECTORY_SEPARATOR . $directory;
  if(!is_dir($fullPath)){
    mkdir($fullPath);
    chmod($fullPath, 0755);
    
    return true;
  }
  
  return false;
}
?>