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
require_once('report/ReportHelper.php');

/**
 * This class represents a cronjob for creating reports including fragments.
 *
 * @author elsa
 */
class Cron_Document_Page_Reportcreator extends Cron_Base {

    public function start() {

        //$query = $this->em->createQuery("SELECT t, a, s FROM Application_Model_Task t JOIN t.action a JOIN t.state s WHERE a.name = :action AND s.name = :state");
        $query = $this->em->createQuery("SELECT t, a, s 
            FROM Application_Model_Task t, Application_Model_Action a, Application_Model_State s 
            WHERE
                t.action=a.id AND 
                t.state=s.id AND 
                a.name = :action AND 
                s.name = :state");
        $query->setParameter("action", "report_requested");
        $query->setParameter("state", "scheduled");
        $query->setMaxResults(1);

        $tasks = $query->getResult();

        if ($tasks) {
            $task = $tasks[0];

            $task->setState($this->em->getRepository('Application_Model_State')->findOneByName("running"));

            //some fake percentage to show it's running
            $task->setProgressPercentage(20);

            $fragments = $query->getResult();
            //$query = $this->em->createQuery("SELECT f FROM Application_Model_Fragment f f.state s WHERE f.document = :document AND s.name = :state");
            $query = $this->em->createQuery("SELECT t, a, s 
                FROM Application_Model_Task t, Application_Model_Action a, Application_Model_State s 
                WHERE
                    t.action=a.id AND 
                    t.state=s.id AND 
                    a.name = :action AND 
                    s.name = :state");
            $query->setParameter("document", $task->getRessource()->getTarget()->getId());
            $query->setParameter("state", "approved");

            $fragments = $query->getResult();

            $reportHelper = new ReportHelper();
            $report = $reportHelper->createReport();
            //$report = $this->createReport($fragments, $task->getRessource());
            // update task

            $report->setState($this->em->getRepository('Application_Model_State')->findOneByName('generated'));

            $this->em->persist($report);
            $this->em->flush();

            $task->setState($this->em->getRepository('Application_Model_State')->findOneByName("completed"));
            $task->setProgressPercentage(100);

            $this->em->persist($task);
            $this->em->flush();

            // notification
            Unplagged_Helper::notify("report_created", $report, $task->getInitiator());
        }
    }

}

$reportCreator = new Cron_Document_Page_Reportcreator();
$reportCreator->start();
?>
