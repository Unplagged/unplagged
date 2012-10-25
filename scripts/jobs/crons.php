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
 * This file runs all the needed Unplagged jobs.
 */
require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'initApplication.php';
bootstrapApplication();

$entityManager = Zend_Registry::getInstance()->entitymanager;

runCronjob('Unplagged_Cron_Document_Parser', $entityManager);
runCronjob('Unplagged_Cron_Document_Page_Reportcreator', $entityManager);
runCronjob('Unplagged_Cron_Document_Page_Simtext', $entityManager);

function runCronjob($cronClass, $entityManager){
  Zend_Registry::get('Log')->info('Starting cronjob: ' . $cronClass);
  $simtext = new $cronClass($entityManager);
  $simtext->start();
  $simtext->printBenchmark();
}