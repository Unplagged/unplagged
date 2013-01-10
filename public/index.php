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
 * This file is the main entry point into the Unplagged application. 
 * It checks whether the application is installed and either redirects 
 * to the Installer or starts the application.
 */
chdir(dirname(__DIR__));
//file that initalizes basic constants and autoloading
require 'initApplication.php';

//the main configuration file
$configFilePath = BASE_PATH . '/config/application.config.php';
$application = Zend\Mvc\Application::init(require $configFilePath);

//query the application for the merged configs
$config = $application->getConfig();
$installer = new UnpInstaller\Installer(BASE_PATH);

//find out if we are already installing, so no need to redirect


if($installer->isInstalled($config) || isInstallingOrConsole()){
  $application->run();
}else{
  //redirect to the installer
  header('Location: ' . $application->getRequest()->getBaseUrl() . 'installer');
}