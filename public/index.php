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
 * This file is the main entry point into the Unplagged application. It checks 
 * whether the application is installed and either starts the installation or 
 * the application.
 */
chdir(dirname(__DIR__));

require 'initApplication.php';

$configFilePath = BASE_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'application.config.php';
$application = Zend\Mvc\Application::init(require $configFilePath);
//@todo maybe better $application->getConfig() or something similar for the Installer?
$installer = new UnpInstaller\Installer($configFilePath);

if($installer->isInstalled()){
  $application->run();
}else{
  //maybe better redirect, so that installer can be always accessed if devel version is turned on
  $installer->install();
}