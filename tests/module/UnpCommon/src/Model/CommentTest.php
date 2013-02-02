<?php

/*
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
namespace UnpCommonTest;

use PHPUnit_Framework_TestCase;
use UnpCommon\Model\Comment;

/**
 *
 */
class CommentTest extends PHPUnit_Framework_TestCase {

  protected $object;

  protected function setUp() {
    $this->object = new Comment(null, null, 'the-title', 'the-comment');
  }

  public function testGetEmptyDirectName() {
    $this->assertEquals('', $this->object->getDirectName());
  }
  
  public function testGetEmptyDirectLink() {
    $this->assertEquals('', $this->object->getDirectLink());
  }
  
  public function testGetIconClass(){
    $this->assertEquals('fam-icon-comment', $this->object->getIconClass());
  }

  public function testGetTitle() {
    $this->assertEquals('the-title', $this->object->getTitle());
  }

  public function testGetText() {
    $this->assertEquals('the-comment', $this->object->getText());
  }
  
  public function testToArray(){
    $this->assertInternalType('array', $this->object->toArray());
  }
  
  public function testCommentTargetCanBeRetrieved(){
    $source = new \UnpCommon\Model\File();
    $comment  = new Comment(null, $source, 'the-title', 'the-comment');
    
    $this->assertEquals($source, $comment->getCommentTarget());
  }
  
  public function testAuthorCanBeRetrieved(){
    $user = new \UnpCommon\Model\User();
    $comment  = new Comment($user, null, 'the-title', 'the-comment');
    
    $this->assertEquals($user, $comment->getAuthor());
  }
}