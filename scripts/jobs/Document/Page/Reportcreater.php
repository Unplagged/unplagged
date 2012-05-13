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
            //self::$em->persist($task);
            //self::$em->flush();

            $query = self::$em->createQuery("SELECT f FROM Application_Model_Document_Fragment f");
            $fragments = $query->getResult();

            $filename = self::createReport("note", $fragments, $task->getInitiator());
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

    private static function createReport($note, $fragments, $userId) {
        
        $user = self::$em->getRepository('Application_Model_User')->findOneById($userId);
        $currentCase = $user->getCurrentCase();
        $casename = $currentCase->getName();
        
        $filepath = BASE_PATH . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "reports";
        $filename = $filepath . DIRECTORY_SEPARATOR . "Report_" . $casename . ".pdf";

        
        // save report to database to get an Id
        $data = array();
        $data["title"] = $casename;
        $data["state"] = self::$em->getRepository('Application_Model_State')->findOneByName('report_generated');
        $data["user"] = $user;
        $data["file"] = self::getFiles($currentCase);
        $data["filePath"] = $filename;
        $report = new Application_Model_Report($data);

        self::$em->persist($report);
        self::$em->flush();

        $html = Unplagged_HtmlLayout::htmlLayout($casename, $note, $fragments);

        $dompdf = new DOMPDF();
        $dompdf->set_paper('a4', 'portrait');
        $dompdf->load_html($html);
        $dompdf->render();
        //$dompdf->stream($filename);
        $output = $dompdf->output();
        file_put_contents($filename, $output);
        return $filename;
    }

    private static function getFiles($currentCase) {
        // get files of current case
        $files = $currentCase->getFiles();
        $rfile = null;

        foreach ($files as $file) {
            if ($file->getIsTarget()) {
                //$this->_helper->flashMessenger->addMessage( $file->getId());
                $rfile = $file;
            }
        }
        return $rfile;
    }
}

Cron_Document_Page_Reportcreater::init();
Cron_Document_Page_Reportcreater::start();
?>
