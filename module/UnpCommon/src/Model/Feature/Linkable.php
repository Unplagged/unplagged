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
namespace UnpCommon\Model\Feature;

/**
 * Implementing classes can be directly linked to on html page.
 */
interface Linkable{

  /**
   * Returns a reference to an appropriate representation of this element. 
   * 
   * @return string
   */
  public function getDirectLink();

  /**
   * Returns the title or name of this element. 
   * 
   * @return string
   */
  public function getDirectName();
  
  /**
   * Returns a styling class name for an icon of this linkable element. When no icon is used the return will be an
   * empty string.
   * 
   * @return string
   */
  public function getIconClass();
}