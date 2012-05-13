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
            
            $filename = Unplagged_Report::createReport("casename", "note", $fragments);
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

}

Cron_Document_Page_Reportcreater::init();
Cron_Document_Page_Reportcreater::start();
?>
