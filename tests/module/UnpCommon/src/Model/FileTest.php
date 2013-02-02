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

namespace UnpCommonTest\Model;

use PHPUnit_Framework_TestCase;
use UnpCommon\Model\File;

/**
 * Description of FileTest.
 */
class FileTest extends PHPUnit_Framework_TestCase {

  private $object;

  public function setUp() {
    $this->object = new File(array(
        'description' => 'a-description', 
        'extension' => 'jpg', 
        'size' => 1234, 
        'originalFilename' => 'a-filename', 
        'mimetype' => 'image/jpeg',
        'localFilename' => 'the-local-filename', 
        'uploader' => 'the-uploader', 
        'path' => 'the-folder/')
            );
  }

  public function testUpdated() {
    $oldUpdated = $this->object->getUpdated();

    $this->object->updated();

    $this->assertNotSame($oldUpdated, $this->object->getUpdated());
  }

  public function testDescriptionIsChangeable() {
    $this->object->setDescription('a-different-description');
    $this->assertEquals('a-different-description', $this->object->getDescription());
  }

  public function testIsImage() {
    $this->assertTrue($this->object->isImage());
  }

  public function testGetDirectName() {
    $this->assertEquals('a-filename', $this->object->getDirectName());
  }

  public function testGetOriginalFilename() {
    $this->assertEquals('a-filename', $this->object->getOriginalFilename());
  }

  public function testGetLocalFilename() {
    $this->assertEquals('the-local-filename', $this->object->getLocalFilename());
  }

  public function testGetSize() {
    $this->assertEquals(1234, $this->object->getSize());
  }

  public function testGetPath() {
    $this->assertEquals('the-folder/', $this->object->getPath());
  }

  public function testGetUploader() {
    $this->assertEquals('the-uploader', $this->object->getUploader());
  }

  public function testGetExtension() {
    $this->assertEquals('jpg', $this->object->getExtension());
  }

  public function testGetMimetype() {
    $this->assertEquals('image/jpeg', $this->object->getMimetype());
  }

  public function testToArray() {
    $this->assertInternalType('array', $this->object->toArray());
  }

  public function testGetDirectLink() {
    $this->assertEquals('/file/show/id/', $this->object->getDirectLink());
  }
  
  public function testGetIconClass(){
    $this->assertEquals('fam-icon-disk', $this->object->getIconClass());
  }

  public function testGetFullPath(){
    $this->assertEquals('the-folder/the-local-filename', $this->object->getFullPath());
  }
}