<?php
//set only as development environment, when nothing was defined before
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
define('BASE_PATH', realpath(dirname(__FILE__) . '/../../'));

echo BASE_PATH;

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once ('Zend/Application.php');
 
// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$directories = array(
  'application' . DIRECTORY_SEPARATOR . 'storage',
  'application' . DIRECTORY_SEPARATOR . 'files',
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
