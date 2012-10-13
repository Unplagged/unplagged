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
 * Description of FileTest.
 */
class FileTest extends PHPUnit_Framework_TestCase {

  private $object;

  public function setUp() {
    $this->object = new Application_Model_File(array('description' => 'a-description', 'extension' => 'jpg', 'size' => 1234, 'filename' => 'a-filename', 'mimetype' => 'image/jpeg', 'location' => 'the-location', 'localFilename' => 'the-local-filename', 'uploader' => 'the-uploader', 'folder' => 'the-folder'));
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

  public function testGetFilename() {
    $this->assertEquals('a-filename', $this->object->getFilename());
  }

  public function testGetLocalFilename() {
    $this->assertEquals('the-local-filename', $this->object->getLocalFilename());
  }

  public function testGetSize() {
    $this->assertEquals('1.21 KB', $this->object->getSize());
  }

  public function testGetFolder() {
    $this->assertEquals('the-folder', $this->object->getFolder());
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
    $this->assertEquals('/file/list', $this->object->getDirectLink());
  }

  public function testLocationIsChangeable() {
    $this->object->setLocation('a-different-location');
    $this->assertEquals('a-different-location', $this->object->getLocation());
  }

}