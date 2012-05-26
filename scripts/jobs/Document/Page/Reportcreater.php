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
require_once(BASE_PATH . '/library/dompdf/dompdf_config.inc.php');
spl_autoload_register('DOMPDF_autoload');

/**
 * This class represents a cronjob for creating reports including fragments.
 *
 * @author elsa
 */
class Cron_Document_Page_Reportcreater extends Cron_Base {

    public static function init() {
        parent::init();
    }

    public static function start() {
        // @todo: dummy stuff, do something real here
        //$query = self::$em->createQuery("SELECT t, a, s FROM Application_Model_Task t JOIN t.action a JOIN t.state s WHERE a.name = :action AND s.name = :state");
        $query = self::$em->createQuery("SELECT t, a, s 
            FROM Application_Model_Task t, Application_Model_Action a, Application_Model_State s 
            WHERE
                t.action=a.id AND 
                t.state=s.id AND 
                a.name = :action AND 
                s.name = :state");
        $query->setParameter("action", "report_requested");
        $query->setParameter("state", "task_scheduled");
        $query->setMaxResults(1);

        $tasks = $query->getResult();

        if ($tasks) {
            $task = $tasks[0];

            $task->setState(self::$em->getRepository('Application_Model_State')->findOneByName("task_running"));
            
            $user = self::$em->getRepository('Application_Model_User')->findOneById($task->getInitiator());
            $target = $user->getCurrentCase()->getTarget();
            $fragments = $target->getFragments();

            $filename = self::createReport("note", $fragments, $user);
            // update task
            $task->setState(self::$em->getRepository('Application_Model_State')->findOneByName("task_finished"));
            $task->setProgressPercentage(100);

            //self::$em->persist($report);
            self::$em->persist($task);
            self::$em->flush();

            // notification
            $user = $task->getInitiator();
            //Unplagged_Helper::notify("simtext_report_created", $report, $user);
        }
    }

    private static function createReport($note, $fragments, $user) {
        
        $currentCase = $user->getCurrentCase();
        $casename = $currentCase->getAlias();
        $filepath = BASE_PATH . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "reports";
        
        $html = Unplagged_HtmlLayout::htmlLayout($casename, $note, $fragments);
        
        $dompdf = new DOMPDF();
        $dompdf->set_paper('a4', 'portrait');
        $dompdf->load_html($html);
        $dompdf->render();
        //$dompdf->stream($filename);
        $output = $dompdf->output();
       
        // save report to database to get an Id
        $data = array();
        $data["title"] = $casename;
        $data["user"] = $user;
        $data["target"] = $user->getCurrentCase()->getTarget();
        $report = new Application_Model_Report($data);

        self::$em->persist($report);
        $currentCase->addReport($report);
        
        self::$em->persist($currentCase);
        self::$em->flush();
        
        // after the flush, we can access the id and put a unique identifier in the report name
        $filename = $filepath . DIRECTORY_SEPARATOR . "Report_" . $casename . "_" . $report->getId() . ".pdf";
        $report->setFilePath($filename);
        $report->setState(self::$em->getRepository('Application_Model_State')->findOneByName('report_generated'));

        self::$em->persist($report);
        self::$em->flush();
        
        file_put_contents($filename, $output);
        return $filename;
    }
}

Cron_Document_Page_Reportcreater::init();
Cron_Document_Page_Reportcreater::start();
?>
