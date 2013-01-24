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
namespace UnpApplication\Cron;

use UnpCommon\Cron;
use UnpCommon\Model\Document;
use Unplagged_Helper;
use Unplagged_Parser;

/**
 * Gets the scheduled OCR tasks and runs them.
 * 
 * @todo should we run all that we find or just one? All sounds better, but we need to check somewhere, 
 * that the crons don't get in each others way..
 */
class ParserCron extends Cron{

  protected function run(){
    $tasks = $this->findTasks('file_parse');

    foreach($tasks as $task){
      $this->updateTaskProgress($task, true, 'running', 20);

      $document = $task->getResource();
      $file = $document->getInitialFile();

      $parser = Unplagged_Parser::factory($file->getMimeType());
      $document = $parser->parseToDocument($file, $document->getLanguage(), $document->getId(), $task->getId());

      if($document instanceof Document){
        $this->setDocumentState($document, 'parsed');
        // add notification to activity stream
        Unplagged_Helper::notify('document_created', $document, $task->getInitiator());
      }else{
        $document = $task->getResource();
        $this->setDocumentState($document, 'error');
      }
      $this->updateTaskProgress($task);

      $this->em->persist($document);
      $this->em->flush();
    }
  }

  protected function setDocumentState(Document $document, $stateName){
    $document->setState($this->em->getRepository('\UnpCommon\Model\State')->findOneByName($stateName));
  }

}