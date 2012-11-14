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
 * This class can be used to build scheduled asynchronous services(i. e. cronjobs), that retrieve necessary data from 
 * the database. It exposes a common interface and gives functionalities to benchmark the runtime and memory usage
 * of the service.
 */
abstract class Unplagged_Cron_Base{

  private $startTime = 0;
  private $stopTime = 0;
  private $startMemory = 0;
  private $stopMemory = 0;
  protected $em = null;

  final public function __construct(Doctrine\ORM\EntityManager $entityManager){
    $this->em = $entityManager;
  }

  /**
   * This function can be called to get the runtime of this job.
   * 
   * It will only return useful data after {@see start()} has been called.
   * 
   * @return int
   */
  final public function getRunTime(){
    $resulttime = $this->stopTime - $this->startTime;
    return $resulttime;
  }

  /**
   * Creates and prints a string that includes all gathered benchmark data.
   * 
   * It will only return useful data after {@see start()} has been called.
   */
  final public function printBenchmark(){
    echo 'Time [' . $this->getRunTime() . '] Memory [' . ($this->getUsedMemory() / 1024) . 'MB]' . PHP_EOL;
  }

  /**
   * @return int The used memory in kB.
   */
  final public function getUsedMemory(){
    return ($this->stopMemory - $this->startMemory) / 1024;
  }

  /**
   * Runs the service and gathers the benchmark data.
   */
  final public function start(){
    $this->startBenchmark();
    $this->run();
    $this->stopBenchmark();
  }

  /**
   * Queries the database for tasks.
   * 
   * @param string $action
   * @param string $state
   * @param int $maxResults
   * @return array
   */
  final protected function findTasks($action, $state = 'scheduled', $maxResults = 1){
    $query = $this->em->createQuery('SELECT t, a, s 
      FROM Application_Model_Task t 
      JOIN t.action a 
      JOIN t.state s 
      WHERE a.name = :action 
      AND s.name = :state'
    );
    $query->setParameter('action', $action);
    $query->setParameter('state', $state);
    $query->setMaxResults($maxResults);

    return $query->getResult();
  }

  /**
   * @param Application_Model_Task $task The task that should be changed.
   * @param bool $flush Indicates whether the entity manager should be flushed.
   * @param string $stateName The name of the state that should be set for the task.
   * @param int $percentage The progress percentage of the task.
   */
  final protected function updateTaskProgress(Application_Model_Task $task, $flush = false, $stateName = 'completed', $percentage = 100){
    $task->setState($this->em->getRepository('Application_Model_State')->findOneByName($stateName));
    $task->setProgressPercentage($percentage);
    $this->em->persist($task);

    if($flush === true){
      $this->em->flush();
    }
  }

  /**
   * Stores the current time and memory to calculate the benchmark. Should be called before {@see run()}.
   */
  final private function startBenchmark(){
    $this->startTime = microtime(true);
    $this->startMemory = memory_get_usage();
  }

  /**
   * Stores the current time and memory to calculate the benchmark. Should be called after {@see run()}.
   */
  final private function stopBenchmark(){
    $this->stopTime = microtime(true);
    $this->stopMemory = memory_get_usage();
  }

  /**
   * Executes the actual functionality of this service.
   */
  abstract protected function run();
}
