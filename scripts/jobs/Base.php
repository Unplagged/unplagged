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
 * @const APPLICATION_ENV The application environment, from which the config values are taken. 
 */
defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'production');

define('BASE_PATH', realpath(dirname(__FILE__) . '/../../'));
require_once BASE_PATH . DIRECTORY_SEPARATOR . 'initApplication.php';

bootstrapApplication();

/**
 * This class represents an abstract class that initalizes any cron, each
 * cron should extend it.
 */
abstract class Cron_Base{

  private $startTime=0;
  private $stopTime=0;
  private $startMemory=0;
  private $stopMemory=0;
  protected $em=null;

  public function __construct($em=null){
    Zend_Registry::get('Log')->info('Starting cronjob ' . get_class());
    $this->em=Zend_Registry::getInstance()->entitymanager;
  }

  final private function startBenchmark(){
    $this->startTime=microtime(true);
    $this->startMemory=memory_get_usage();
  }

  final private function stopBenchmark(){
    $this->stopTime=microtime(true);
    $this->stopMemory=memory_get_usage();
  }

  final public function getRunTime(){
    $resulttime=$this->stopTime - $this->startTime;
    return $resulttime;
  }

  final public function printBenchmark(){
    echo 'Time [' . $this->getRunTime() . '] Memory [' . ($this->getUsedMemory() / 1024) . 'MB]' . PHP_EOL;
  }

  /**
   * @return int The used memory in kB.
   */
  final public function getUsedMemory(){
    return ($this->stopMemory - $this->startMemory) / 1024;
  }

  final public function start(){
    $this->startBenchmark();
    $this->run();
    $this->stopBenchmark();
  }

  abstract protected function run();
}
