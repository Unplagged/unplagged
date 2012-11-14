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
 * A factory that initalizes the correct data parser based on the mime type.
 */
class Unplagged_Parser{

  private static $mimeMappings = array(
    'image/tif' => 'Document_ImageParser'
    , 'image/tiff' => 'Document_ImageParser'
    , 'application/octet-stream' => 'Document_ImageParser'
    , 'image/jpeg' => 'Document_ImageParser'
    , 'image/gif' => 'Document_ImageParser'
    , 'image/png' => 'Document_ImageParser'
    , 'image/jpg' => 'Document_ImageParser'
    , 'image/*' => 'Document_ImageParser'
    , 'application/pdf' => 'Document_ImageParser'
    , 'text/plain' => 'Document_TextParser'
  );

  public static function factory($mimeType){

    if(array_key_exists($mimeType, self::$mimeMappings)){
      $parserName = 'Unplagged_Parser_' . self::$mimeMappings[$mimeType];
      return new $parserName();
    }
  }

}