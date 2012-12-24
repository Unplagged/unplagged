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
namespace UnpInstaller;

/**
 *
 */
class TemplateParser {

  private $templateDirectory;

  public function __construct($templateDirectory) {
    $this->templateDirectory = $templateDirectory;
  }

  public function parse($template, $data) {
    $response = $template;
    if (!empty($data) || is_array($data)) {
      foreach ($data as $key => $value) {
        $response = str_replace('{$' . $key . '}', $value, $response);
      }
    }
    return $response;
  }

  public function parseFile($filename, $data) {
    return $this->parse(file_get_contents($this->templateDirectory . DIRECTORY_SEPARATOR . $filename), $data);
  }

  private function setValueAttribute($default = ''){
    
  }
}