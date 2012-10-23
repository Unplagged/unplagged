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
 * This class represents a cronjob for parsing larger files into documents using OCR.
 */
class Unplagged_Cron_Document_Parser extends Unplagged_Cron_Base{

  protected function run(){
    $tasks = $this->findTasks();

    if($tasks){
      $task = $tasks[0];

      $taskId = $task->getId();

      $task->setState($this->em->getRepository('Application_Model_State')->findOneByName('running'));
      $this->em->persist($task);
      $this->em->flush();

      $document = $task->getResource();
      $file = $document->getInitialFile();
      $documentId = $document->getId();

      $parser = Unplagged_Parser::factory($file->getMimeType());
      $document = $parser->parseToDocument($file, $document->getLanguage(), $documentId, $taskId);
      
      if($document instanceof Application_Model_Document){
        // update document
        $document->setState($this->em->getRepository('Application_Model_State')->findOneByName('parsed'));

        // update task
        $task = $this->em->getRepository('Application_Model_Task')->findOneById($taskId);
        $task->setState($this->em->getRepository('Application_Model_State')->findOneByName('completed'));
        $task->setProgressPercentage(100);

        $this->em->persist($document);
        $this->em->persist($task);

        $this->em->flush();

        // add notification to activity stream
        Unplagged_Helper::notify("document_created", $document, $task->getInitiator());

        $this->em->clear();
      }else{
        $task->setState($this->em->getRepository('Application_Model_State')->findOneByName('completed'));
        $task->setProgressPercentage(100);
        $this->em->persist($task);

        $document = $task->getResource();
        $document->setState($this->em->getRepository('Application_Model_State')->findOneByName('error'));

        $this->em->persist($document);
        $this->em->flush();
      }
    }
  }

  private function findTasks(){
    $query = $this->em->createQuery('SELECT t, a, s FROM Application_Model_Task t JOIN t.action a JOIN t.state s WHERE a.name = :action AND s.name = :state');
    $query->setParameter('action', 'file_parse');
    $query->setParameter('state', 'scheduled');
    $query->setMaxResults(1);

    return $query->getResult();
  }

}