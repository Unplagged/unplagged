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

  public static function init(){
    parent::init();
  }

  public static function start(){
    // @todo: dummy stuff, do something real here
    $query = self::$em->createQuery("SELECT t, a, s FROM Application_Model_Task t JOIN t.action a JOIN t.state s WHERE a.name = :action AND s.name = :state");
    $query->setParameter("action", "page_simtext");
    $query->setParameter("state", "task_scheduled");
    $query->setMaxResults(1);

    $tasks = $query->getResult();

    if($tasks){
      $task = $tasks[0];

      $task->setState(self::$em->getRepository('Application_Model_State')->findOneByName("task_running"));
      self::$em->persist($task);
      self::$em->flush();

      $report = $task->getRessource();

      // generate the simtext result
      $content = "";
      $left = $report->getPage()->getContent('array');

      $documents = $report->getDocuments();
      foreach($documents as $documentId){
        $document = self::$em->getRepository('Application_Model_Document')->findOneById($documentId);
        $pages = $document->getPages();

        foreach($pages as $page){
          $right = $page->getContent('array');

          foreach($left as $lineNumber=>$lineContent){
            $left[$lineNumber] = htmlentities($lineContent, ENT_COMPAT, 'UTF-8');
          }

          foreach($right as $lineNumber=>$lineContent){
            $right[$lineNumber] = htmlentities($lineContent, ENT_COMPAT, 'UTF-8');
          }

          $simtextResult = Unplagged_CompareText::compare($left, $right, 4); // do simtext with left and right

          $left = $simtextResult['left'];
          $right = $simtextResult['right'];

          foreach($left as $lineNumber=>$lineContent){
            $left[$lineNumber] = '<li value="' . $lineNumber . '">' . $lineContent . '</li>';
          }

          foreach($right as $lineNumber=>$lineContent){
            $right[$lineNumber] = '<li value="' . $lineNumber . '">' . $lineContent . '</li>';
          }

          $result['left'] = '<ol>' . implode("\n", $left) . '</ol>';
          $result['right'] = '<ol>' . implode("\n", $right) . '</ol>';

          // if simtext found something on that page, append it to the report
          if(strpos($result['left'], "fragmark-") !== false){
            $content .= "<div style='clear:both;padding-top:10px;'><b>Document " . $document->getTitle() . " - Page " . $page->getPageNumber() . "</b><br />";

            $content .= '<div class="document-page diff clearfix">
                        <div class="src-wrapper">
                          <h3>Left</h3>
                          <div id="candidateText">' . $result['left'] . '</div>
                        </div>
                        <div class="src-wrapper">
                          <h3>Right</h3>
                          <div id="sourceText">' . $result['right'] . '</div>
                        </div>
                      </div>';
            $content .= '</div>';
          }
        }
      }

      // update report
      $report->setContent($content);
      $report->setState(self::$em->getRepository('Application_Model_State')->findOneByName("report_generated"));

      // update task
      $task->setState(self::$em->getRepository('Application_Model_State')->findOneByName("task_finished"));

      self::$em->persist($report);
      self::$em->persist($task);

      self::$em->flush();

      // notification
      $user = $task->getInitiator();
      Unplagged_Helper::notify("simtext_report_created", $report, $user);
    }
  }

}

Cron_Document_Page_Simtext::init();
Cron_Document_Page_Simtext::start();
?>
