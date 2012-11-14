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
 * This class helps to handle file uploads.
 */
class Unplagged_Uploader{

  private $filename;
  private $description;
  private $storageDir;
  private $folder;
  private $file;

  /**
   * @param string $filename
   * @param string $description
   * @param string $storageDir
   * @param string $folder
   */
  public function __construct($filename, $description = '', $storageDir = './', $folder = null){
    $this->filename = $filename;
    $this->description = $description;
    $this->storageDir = $storageDir;
    $this->folder = $folder;
  }

  /**
   * Stores the uploaded file with the parameters specified in the constructor.
   * 
   * @return Application_Model_File
   */
  public function upload(Application_Model_User $user = null){
    //@todo move registry call out of this class
    $user = Zend_Registry::getInstance()->user;
    
    $adapter = new Zend_File_Transfer();
    if($adapter->getFileName()){
      $pathinfo = pathinfo($adapter->getFileName());

      $fileNames = $this->findFilename($pathinfo, $this->filename);
      $adapter->addFilter('Rename', $this->storageDir . $fileNames[1]);

      //move the uploaded file to the before specified location
      if($adapter->receive()){
        chmod($this->storageDir . $fileNames[1], 0755);

        $file = $this->createFileObject($adapter, $fileNames, $pathinfo, $this->description, $this->storageDir, $user, $this->folder);
        $this->file = $file;

        return $file;
      }
    }
  }

  /**
   * Creates a unique filename from the specified data.
   * 
   * @param array $pathinfo An array as returned by the {@see pathinfo()} function for the uploaded file.
   * @param string $newName Replaces the filename from the pathinfo() array if specified.
   * @return array An array containing the visible filename and a new unique filename to store the file locally.
   */
  private function findFilename($pathinfo, $newName = ''){
    $fileExtension = $pathinfo['extension'];

    $fileName = '';
    if(!empty($newName)){
      $fileName = $newName;
    }else{
      $fileName = $pathinfo['filename'];
    }
    $localFilename = $this->sanitizeFilename($fileName) . '_' . uniqid() . '.' . $fileExtension;
    $fileName .= '.' . $fileExtension;

    return array($fileName, $localFilename);
  }

  /**
   * Sanitizes a filename by removing special chars and replacing whitespace with dashes.
   * 
   * Removes special characters that are illegal in filenames on certain
   * operating systems and special characters requiring special escaping
   * to manipulate at the command line. Replaces spaces and consecutive
   * dashes with a single dash. Trim period, dash and underscore from beginning
   * and end of filename.
   *
   * Based on a Wordpress function.
   *
   * @param string $filename The filename to be sanitized.
   * @return string The sanitized filename.
   */
  private function sanitizeFilename($filename){
    $specialChars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",",
        "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
    $filename = str_replace($specialChars, '', $filename);
    $filename = preg_replace('/[\s-]+/', '-', $filename);
    $filename = trim($filename, '.-_');
    return $filename;
  }

  /**
   * Takes the data to create an {@see Application_Model_File} object.
   * 
   * @param Zend_File_Transfer $adapter
   * @param array $fileNames
   * @param array $pathinfo
   * @param string $description
   * @param string $storageDir
   * @return \Application_Model_File 
   */
  private function createFileObject(Zend_File_Transfer $adapter, array $fileNames, array $pathinfo, 
          $description, $storageDir, $user, $folder = null){
    $data = array();
    $data['size'] = $adapter->getFileSize();
    //if the mime type is always application/octet-stream, then the 
    //mime magic and fileinfo extensions are probably not installed
    $data['mimetype'] = $adapter->getMimeType();
    $data['filename'] = $fileNames[0];
    $data['extension'] = $pathinfo['extension'];
    $data['location'] = $storageDir;
    $data['description'] = $description;
    $data['localFilename'] = $fileNames[1];
    $data['uploader'] = $user;
    $data['folder'] = $folder;

    return new Application_Model_File($data);
  }

  /**
   * 
   * @param type $thumbWidth
   * @param type $thumbHeight
   * @return type
   * 
   * @todo maybe move into another class with more specialized image functionality
   */
  public function crop($thumbWidth, $thumbHeight){
    //getting the image dimensions
    list($width, $height) = getimagesize($this->file->getFullPath());
    $ratio = $width * 1. / $height;

    //saving the image into memory (for manipulation with GD Library)
    $myImage = imagecreatefromjpeg($this->file->getFullPath());

    // setting the crop size
    if($width < $height){
      $twidth = $width;
      $theight = $width;
      $x = 0;
      $y = $height / 2. - $width / 2.;
    }else{
      $twidth = $height;
      $theight = $height;
      $x = $width / 2. - $height / 2.;
      $y = 0;
    }

    // creating the thumbnail
    $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
    imagecopyresampled($thumb, $myImage, 0, 0, $x, $y, $thumbWidth, $thumbHeight, $twidth, $theight);

    imagejpeg($thumb, $this->file->getFullPath());

    imagedestroy($thumb);
    imagedestroy($myImage);

    return $this->file;
  }

}