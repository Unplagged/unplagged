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
use Unplagged_CompareText;
use Unplagged_Helper;

/**
 * This class retrieves all scheduled cronjobs for simtext reports and runs them.
 */
class SimtextCron extends Cron{

  public function run(){
    $tasks = $this->findTasks('page_simtext');

    foreach($tasks as $task){
      $this->updateTaskProgress($task, true, 'running', 0);

      $report = $task->getResource();

      // generate the simtext result
      $content = array();
      $left = $report->getPage()->getContent('array');
      foreach($left as $lineNumber=> $lineContent){
        $left[$lineNumber] = htmlentities($lineContent, ENT_COMPAT, 'UTF-8');
      }

      // count the pages to compare
      $documents = $report->getDocuments();
      $pagesCount = 0;
      foreach($documents as $documentId){
        $document = $this->em->getRepository('\UnpCommon\Model\Document')->findOneById($documentId);
        $pagesCount += $document->getPages()->count();
      }
      $prevPerc = 0; // the percentage of the previous iteration

      $i = 0;
      foreach($documents as $documentId){
        $document = $this->em->getRepository('\UnpCommon\Model\Document')->findOneById($documentId);
        $pages = $document->getPages();

        foreach($pages as $page){
          $i++;
          $right = $page->getContent('array');

          foreach($right as $lineNumber=> $lineContent){
            $right[$lineNumber] = htmlentities($lineContent, ENT_COMPAT, 'UTF-8');
          }

          $comparer = new Unplagged_CompareText(4);
          $simtextResult = $comparer->compare($left, $right); // do simtext with left and right

          $resultLeft = $this->makeHtmlList($simtextResult['left']);

          // if simtext found something on that page, append it to the report
          if(strpos($resultLeft, 'fragmark-') !== false){
            $resultRight = $this->makeHtmlList($simtextResult['right']);
            $pageResult = array();

            $pageResult['candidate']['page'] = $report->getPage()->getPageNumber();
            $pageResult['candidate']['document'] = $report->getPage()->getDocument()->getTitle();
            $pageResult['candidate']['text'] = $resultLeft;

            $pageResult['source']['page'] = $page->getPageNumber();
            $pageResult['source']['document'] = $document->getTitle();
            $pageResult['source']['text'] = $resultRight;

            $content[] = $pageResult;
          }

          $perc = round($i * 1.0 / $pagesCount * 100 / 10) * 10;
          if($perc > $prevPerc){
            $this->updateTaskProgress($task, true, 'running', $perc);
            $prevPerc = $perc;
          }
        }
      }

      // update report
      $report->setContent($content);
      $report->setState($this->em->getRepository('\UnpCommon\Model\State')->findOneByName("generated"));
      $this->em->persist($report);

      $this->updateTaskProgress($task);
      $this->em->flush();

      // notification
      Unplagged_Helper::notify('simtext_report_created', $report, $task->getInitiator());
    }
  }

  private function makeHtmlList(array $lines){
    $taggedLines = array();

    foreach($lines as $lineNumber=> $lineContent){
      $taggedLines[$lineNumber] = '<li value="' . $lineNumber . '">' . $lineContent . '</li>';
    }
    return '<ol>' . implode('', $taggedLines) . '</ol>';
  }

}