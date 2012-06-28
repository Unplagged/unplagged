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
require_once(realpath(dirname(__FILE__)) . "/../Base.php");

/**
 * This class represents a cronjob for parsing larger files into documents using OCR.
 */
class Cron_Document_Parser extends Cron_Base{

  public function start(){
    $tasks = $this->findTask();

    if($tasks){
      $task = $tasks[0];

      $taskId = $task->getId();

      $task->setState($this->em->getRepository('Application_Model_State')->findOneByName('task_running'));
      $this->em->persist($task);
      $this->em->flush();

      $document = $task->getRessource();
      $file = $document->getInitialFile();
      $documentId = $document->getId();

      $language = 'eng';
      $parser = Unplagged_Parser::factory($file->getMimeType());
      $document = $parser->parseToDocument($file, $language, $documentId, $taskId);

      if($document instanceof Application_Model_Document){
        // update document
        $document->setState($this->em->getRepository('Application_Model_State')->findOneByName('parsed'));

        // update task
        $task = $this->em->getRepository('Application_Model_Task')->findOneById($taskId);
        $task->setState($this->em->getRepository('Application_Model_State')->findOneByName('task_finished'));
        $task->setProgressPercentage(100);

        $this->em->persist($document);
        $this->em->persist($task);

        $this->em->flush();

        // add notification to activity stream
        Unplagged_Helper::notify("document_created", $document, $task->getInitiator());

        $this->em->clear();
      }
    }
  }

  private function findTask(){
    $query = $this->em->createQuery('SELECT t, a, s FROM Application_Model_Task t JOIN t.action a JOIN t.state s WHERE a.name = :action AND s.name = :state');
    $query->setParameter('action', 'file_parse');
    $query->setParameter('state', 'task_scheduled');
    $query->setMaxResults(1);

    return $query->getResult();
  }

}

$parser = new Cron_Document_Parser();
$parser->start();
?>
