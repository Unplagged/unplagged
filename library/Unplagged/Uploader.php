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
 * This class can be used to upload files.
 */
class Unplagged_Uploader{

  private $filename;
  private $description;
  private $storageDir;
  private $folder;
  private $file;

  public function __construct($filename, $description, $storageDir, $folder = null){
    $this->filename = $filename;
    $this->description = $description;
    $this->storageDir = $storageDir;
    $this->folder = $folder;
  }

  /**
   * Stores the uploaded file with the parameters specified in the constructor.
   * 
   * @return null 
   */
  public function upload(){
    $adapter = new Zend_File_Transfer();
    if($adapter->getFileName()){
      $pathinfo = pathinfo($adapter->getFileName());

      $fileNames = $this->findFilename($pathinfo, $this->filename);
      $adapter->addFilter('Rename', $this->storageDir . $fileNames[1]);

      //move the uploaded file to the before specified location
      if($adapter->receive()){
        chmod($this->storageDir . $fileNames[1], 0755);

        $file = $this->createFileObject($adapter, $fileNames, $pathinfo, null, $this->storageDir, Zend_Registry::getInstance()->user, $this->folder);
        $this->file = $file;

        return $file;
      }else{
        return null;
      }
    }else{
      return null;
    }
  }

  /**
   * Creates a unique filename from the specified data.
   * 
   * @param array $pathinfo An array as returned by the pathinfo() function for the uploaded file.
   * @param string $newName A different name for the file from user input.
   * @return array An array containing the original filename and a new unique filename to store the file locally. 
   */
  private function findFilename($pathinfo, $newName){
    $fileExtension = $pathinfo['extension'];

    $fileName = '';
    if($newName){
      $fileName = $newName;
    }else{
      $fileName = $pathinfo['filename'];
    }
    $localFilename = $this->sanitizeFilename($fileName) . '_' . uniqid() . '.' . $fileExtension;
    $fileName .= '.' . $fileExtension;

    return array($fileName, $localFilename);
  }

  /**
   * Based on Wordpress.
   * 
   * Sanitizes a filename replacing whitespace with dashes
   *
   * Removes special characters that are illegal in filenames on certain
   * operating systems and special characters requiring special escaping
   * to manipulate at the command line. Replaces spaces and consecutive
   * dashes with a single dash. Trim period, dash and underscore from beginning
   * and end of filename.
   *
   * @param string $filename The filename to be sanitized
   * @return string The sanitized filename
   */
  private function sanitizeFilename($filename){
    $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
    $filename = str_replace($special_chars, '', $filename);
    $filename = preg_replace('/[\s-]+/', '-', $filename);
    $filename = trim($filename, '.-_');
    return $filename;
  }

  /**
   * Takes the data to create an Application_Model_File object.
   * 
   * @param Zend_File_Transfer $adapter
   * @param array $fileNames
   * @param array $pathinfo
   * @param string $description
   * @param string $storageDir
   * @return \Application_Model_File 
   */
  private function createFileObject($adapter, $fileNames, $pathinfo, $description, $storageDir, $user, $folder = null){
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

    $file = new Application_Model_File($data);

    return $file;
  }

  public function crop($thumbWidth, $thumbHeight){
    //getting the image dimensions
    list($width, $height) = getimagesize($this->file->getFullPath());

    //saving the image into memory (for manipulation with GD Library)
    $myImage = imagecreatefromjpeg($this->file->getFullPath());
    ///--------------------------------------------------------
    //setting the crop size
    //--------------------------------------------------------
    if($width > $height)
      $biggestSide = $width;
    else
      $biggestSide = $height;

    //The crop size will be half that of the largest side
    $cropPercent = .5;
    $cropWidth = $biggestSide * $cropPercent;
    $cropHeight = $biggestSide * $cropPercent;

    //getting the top left coordinate
    $c1 = array("x"=>($width - $cropWidth) / 2, "y"=>($height - $cropHeight) / 2);

    //--------------------------------------------------------
    // Creating the thumbnail
    //--------------------------------------------------------
    $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
    imagecopyresampled($thumb, $myImage, 0, 0, $c1['x'], $c1['y'], $thumbWidth, $thumbHeight, $cropWidth, $cropHeight);

    imagejpeg($thumb, $this->file->getFullPath());

    imagedestroy($thumb);
    imagedestroy($myImage);

    return $this->file;
  }

}