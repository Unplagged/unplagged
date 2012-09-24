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
require_once(realpath(dirname(__FILE__)) . "/../../Base.php");

/**
 * This class represents a cronjob for parsing larger files into documents using OCR.
 *
 * @author benjamin
 */
class Cron_Document_Page_Simtext extends Cron_Base{

  public function start(){
    $query = $this->em->createQuery("SELECT t, a, s FROM Application_Model_Task t JOIN t.action a JOIN t.state s WHERE a.name = :action AND s.name = :state");
    $query->setParameter("action", "page_simtext");
    $query->setParameter("state", "scheduled");
    $query->setMaxResults(1);

    $tasks = $query->getResult();

    if($tasks){
      $task = $tasks[0];

      $task->setState($this->em->getRepository('Application_Model_State')->findOneByName("running"));
      $this->em->persist($task);
      $this->em->flush();

      $report = $task->getRessource();

      // generate the simtext result
      $content = array();
      $left = $report->getPage()->getContent('array');
      foreach($left as $lineNumber=>$lineContent){
        $left[$lineNumber] = htmlentities($lineContent, ENT_COMPAT, 'UTF-8');
      }

      // count the pages to compare
      $documents = $report->getDocuments();
      $pagesCount = 0;
      foreach($documents as $documentId){
        $document = $this->em->getRepository('Application_Model_Document')->findOneById($documentId);
        $pagesCount += $document->getPages()->count();
      }
      $prevPerc = 0; // the percentage of the previous iteration

      $i = 0;
      $documents = $report->getDocuments();
      foreach($documents as $documentId){
        $document = $this->em->getRepository('Application_Model_Document')->findOneById($documentId);
        $pages = $document->getPages();

        foreach($pages as $page){
          $i++;
          $right = $page->getContent('array');

          foreach($right as $lineNumber=>$lineContent){
            $right[$lineNumber] = htmlentities($lineContent, ENT_COMPAT, 'UTF-8');
          }

          $simtextResult = Unplagged_CompareText::compare($left, $right, 4); // do simtext with left and right

          $leftRes = $simtextResult['left'];
          $rightRes = $simtextResult['right'];

          foreach($leftRes as $lineNumber=>$lineContent){
            $leftRes[$lineNumber] = '<li value="' . $lineNumber . '">' . $lineContent . '</li>';
          }

          foreach($rightRes as $lineNumber=>$lineContent){
            $rightRes[$lineNumber] = '<li value="' . $lineNumber . '">' . $lineContent . '</li>';
          }

          $result['left'] = '<ol>' . implode("\n", $leftRes) . '</ol>';
          $result['right'] = '<ol>' . implode("\n", $rightRes) . '</ol>';

          // if simtext found something on that page, append it to the report
          if(strpos($result['left'], "fragmark-") !== false){
            
            $pageResult = array();

            $pageResult['candidate']['page'] = $report->getPage()->getPageNumber();
            $pageResult['candidate']['document'] = $report->getPage()->getDocument()->getTitle();
            $pageResult['candidate']['text'] = $result['left'];
            
            $pageResult['source']['page'] = $page->getPageNumber();
            $pageResult['source']['document'] = $document->getTitle();
            $pageResult['source']['text'] = $result['right'];
            
            $content[] = $pageResult;
          }

          $perc = round($i * 1.0 / $pagesCount * 100 / 10) * 10;
          if($perc > $prevPerc){
            $task->setProgressPercentage($perc);
            $this->em->persist($task);
            $this->em->flush();
            $prevPerc = $perc;
          }
        }
      }

      // update report
      $report->setContent($content);
      $report->setState($this->em->getRepository('Application_Model_State')->findOneByName("generated"));

      // update task
      $task->setState($this->em->getRepository('Application_Model_State')->findOneByName("completed"));
      $task->setProgressPercentage(100);

      $this->em->persist($report);
      $this->em->persist($task);

      $this->em->flush();

      // notification
      Unplagged_Helper::notify("simtext_report_created", $report, $task->getInitiator());
    }
  }

}

$simtext = new Cron_Document_Page_Simtext();
$simtext->start();
?>