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

require_once(realpath(dirname(__FILE__)) . "/init.php");

/**
 * This class represents an abstract class that initalizes any cron, each
 * cron should extend it.
 *
 * @author benjamin
 */
abstract class Cron_Base{
  protected static $em;
  
  public static function init() {
    self::$em = Zend_Registry::getInstance()->entitymanager;
  }
}

?>
