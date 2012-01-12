<?php
//set only as development environment, when nothing was defined before
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
 echo APPLICATION_PATH;
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


// create directories
$storagePath = BASE_PATH . "application" . DIRECTORY_SEPARATOR . "storage";
if(!is_dir($storagePath)){
  mkdir($storagePath);
}
chmod($storagePath, 0755);

  
$storageFilesPath = BASE_PATH . "application" . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "files";
if(!is_dir($storageFilesPath)){
  mkdir($storageFilesPath);
}
chmod($storageFilesPath, 0755);

  
$ocrPath = BASE_PATH . "temp" . DIRECTORY_SEPARATOR . "ocr";
if(!is_dir($ocrPath)){
  mkdir($ocrPath);
}
chmod($imagemagickTempPath, 0755);


$imagemagickTempPath = BASE_PATH . "temp" . DIRECTORY_SEPARATOR . "imagemagick";
if(!is_dir($imagemagickTempPath)){
  mkdir($imagemagickTempPath);
}
chmod($imagemagickTempPath, 0755);

?>
