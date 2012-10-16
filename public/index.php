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
 *
 *
 * This file is the main entry point into the Unplagged application. It defines 
 * the environment variables and initiates the bootstrapping process.
 */
require_once '..' . DIRECTORY_SEPARATOR . 'initApplication.php';
require_once BASE_PATH . '/scripts/build/Installer/Installer.php';

$installer = new Installer();

if($installer->isInstalled()){
  $application = bootstrapApplication();
  $application->run();
}else{
  $installer->install();
}