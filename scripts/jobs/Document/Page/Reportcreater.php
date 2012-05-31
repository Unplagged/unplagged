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
require_once(BASE_PATH . '/library/html2pdf/html2pdf.class.php');
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

            $filename = self::createReport($fragments, $user);
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

    private static function createReport($fragments, $user) {

        // get page infos
      /*  $pageFromPlag = $fragment->getPlag()->getLineFrom()->getPage()->getPageNumber();
        $pageToPlag = $fragment->getPlag()->getLineTo()->getPage()->getPageNumber();

        $pageFromSource = $fragment->getSource()->getLineFrom()->getPage()->getPageNumber();
        $pageToSource = $fragment->getSource()->getLineTo()->getPage()->getPageNumber();

        // get line infos
        $lineFromPlag = $fragment->getPlag()->getLineFrom()->getLineNumber();
        $lineToPlag = $fragment->getPlag()->getLineTo()->getLineNumber();

        $lineFromSource = $fragment->getSource()->getLineFrom()->getLineNumber();
        $lineToSource = $fragment->getSource()->getLineTo()->getLineNumber();

        $html .= '<p> Page from: ' . $pageFromPlag . ' to:' . $pageToPlag . '</p>' .
                '<p> Page from: ' . $pageFromSource . ' to:' . $pageToSource . '</p>';

        $html .= '<p>Plagiarized Text </p>' .
                '<p> Line from: ' . $lineFromPlag . ' to:' . $lineToPlag . '</p>' .
                '<p>Source Text </p>' .
                '<p> Line from: ' . $lineFromSource . ' to:' . $lineToSource . '</p>';*/

        $currentCase = $user->getCurrentCase();
        $casename = $currentCase->getAlias();
        $filepath = BASE_PATH . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "reports";

        $html = Unplagged_HtmlLayout::htmlLayout($casename, $fragments);
        $col1 = self::cut_text_into_pages($html[0]);
        $col2 = self::cut_text_into_pages($html[1]);

        $content = self::mix_two_columns($col1, $col2, "possible plagiat", "original");
        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->WriteHTML($content);
        

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
        $filename = $filepath . DIRECTORY_SEPARATOR . $report->getId() . ".pdf";
        $report->setFilePath($filename);
        $report->setState(self::$em->getRepository('Application_Model_State')->findOneByName('report_generated'));

        self::$em->persist($report);
        self::$em->flush();
        $html2pdf->Output($filename, 'F');
        //file_put_contents($filename, $output);
        return $filename;
    }

    /**
     * Creates an html page element, containing a tbale
     * with three columns. The first parameter is set in
     * the first column and the second parameter is set
     * in the third column.
     */
    private static function create_a_page($td1, $td2, $title1, $title2) {
        return '
<page>
<table>
<tr>
<td style="width:350px">' . $title1 . '</td>
<td style="width:10px"></td>
<td style="width:350px">' . $title2 . '</td>
</tr>
<tr>
<td style="width:350px;border:1px solid black;padding:3px;">
                 ' . $td1 . '
</td>
<td style="width:10px"/>
<td style="width:350px;border:1px solid black;padding:3px;">
                 ' . $td2 . '
</td>
</tr>
</table></page>';
    }

    /**
     * Removes the multiple blank spaces from the given parameter.
     */
    private static function remove_spaces($text) {
        while (true) {
            $replaced = str_replace('  ', ' ', $text);
            if ($replaced != $text) {
                $text = $replaced;
            } else {
                break;
            }
        }
        return $text;
    }

    /**
     * Cuts the given text into an array, which each element contains
     * $nbWordsProPage words.
     */
    private static function cut_text_into_pages($text) {
        $text = self::remove_spaces($text);
        $exploded = array_slice(explode(' ', $text), 0);
        $nbWordsProPage = 400;
        $nbPage = 0;
        $pages = array();

        for ($i = 0; $i < sizeof($exploded); $i+=$nbWordsProPage) {
            $pages[$nbPage++] = implode(' ', array_slice($exploded, $i, $nbWordsProPage));
        }

        return $pages;
    }

    /**
     * Returns the $index elements of the array.
     * If this element does not exist, a blank
     * space is returned.
     */
    private static function get_col($array, $index) {
        if (isset($array[$index])) {
            return $array[$index];
        } else {
            return "&nbsp;";
        }
    }

    /**
     * Builds a string from two arrays containing
     * x and y elements.
     */
    private static function mix_two_columns($col1, $col2, $title1, $title2) {
        $html = '<style type="text/css">' .
                'body {text-align: justify}
                .fragmark-0 { background-color: #f5cf9f; }
                .fragmark-1 { background-color: #c2f598; }
                .fragmark-2 { background-color: #a7c6f2; }
                .fragmark-3 { background-color: #f29f9f; }
                .fragmark-4 { background-color: #aff2be; }
                .fragmark-5 { background-color: #e8a3ff; }
                .fragmark-6 { background-color: #e6e181; }
                .fragmark-7 { background-color: #b8b8ff; }
                .fragmark-8 { background-color: #f5cf9f; }
                .fragmark-9 { background-color: #a5e6ed; }
                .text {margin: 3px; padding: 3px; border: 1px solid grey}' .
                '</style>';
        $size1 = sizeof($col1);
        $size2 = sizeof($col2);
        $size = $size1 > $size2 ? $size1 : $size2;
        for ($i = 0; $i < $size; $i++) {
            $c1 = self::get_col($col1, $i);
            $c2 = self::get_col($col2, $i);
            ;
            $html .= self::create_a_page($c1, $c2, $title1, $title2);
        }
        return $html;
    }

}

Cron_Document_Page_Reportcreater::init();
Cron_Document_Page_Reportcreater::start();
?>
