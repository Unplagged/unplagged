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

require_once("../Base.php");

/**
 * This class represents a cronjob for parsing larger files into documents using OCR.
 *
 * @author benjamin
 */
class Cron_Document_Parser extends Cron_Base{

  public static function init(){
    parent::init();
  }
  
  public static function start(){
    // @todo: dummy stuff, do something real here
    $query = self::$em->createQuery("SELECT f FROM Application_Model_File f WHERE f.id > :id");
    $query->setParameter("id", 1);

    $files = $query->getResult();
    
    foreach($files as $file) {
      echo $file->getId() . "\n";
    }
  }

}

Cron_Document_Parser::init();
Cron_Document_Parser::start();

?>
