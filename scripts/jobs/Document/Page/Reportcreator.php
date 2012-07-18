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
require_once(BASE_PATH . '/library/html2pdf/html2pdf.class.php');
define("SPAN_OPEN", "<span");
define("SPAN_CLOSE", "</span");
define("ST", "<");
define("GT", ">");

/**
 * This class represents a cronjob for creating reports including fragments.
 *
 * @author elsa
 */
class Cron_Document_Page_Reportcreator extends Cron_Base {

    private $pagenumber;
    private $nbSources;
    private $defaultText = "<div class='introduction'><h2>1. Einleitung</h2>
{#PLACE_1} Die dokumentierten Fragmente erlauben es der akademischen und allgemeinen
Öffentlichkeit, sich ein eigenes Bild des Falls zu machen. Eine detaillierte, kontinuierlich
erweiterte Dokumentation der Projektergebnisse ist unter <a href=\"http://http://www.unplagged.com/\">
http://www.unplagged.com</a> zu finden.
<h2>2. Vorgehensweise</h2>
Die Analyse der Dissertation fand in mehreren Schritten statt. Im ersten Schritt wurden
vermutete Plagiate der Dissertation in Form von Fragmenten dokumentiert, welche
den direkten Vergleich mit den Originalen ermöglichen. Wie auch in der Wikipedia ist
diese Dokumentation anonym möglich. Nach anschließender Verifizierung wurden die
betroffenen Stellen nach dem „Vier-Augen-Prinzip“ in gesichtete Fragmente überführt.
<h2>3. Definition von Plagiatkategorien</h2>
Die hier verwendeten Plagiatkategorien basieren auf den Ausarbeitungen von Wohnsdorf
/ Weber-Wulff: Strategien der Plagiatsbekämpfung, 2006. Eine vollständige Beschreibung
der Kategorien findet sich im VroniPlag-Wiki. Die Plagiatkategorien sind im
Einzelnen:
<h3>3.1. Komplettplagiat</h3>
Text, der wörtlich aus einer Quelle ohne Quellenangabe übernommen wurde.
<h3>3.2. Verschleierung</h3>
Text, der erkennbar aus fremder Quelle stammt, jedoch umformuliert und weder als
Paraphrase noch als Zitat gekennzeichnet wurde.
<h3>3.3. Bauernopfer</h3>
Text, dessen Quelle ausgewiesen ist, der jedoch ohne Kenntlichmachung einer wörtlichen
oder sinngemäßen Übernahme kopiert wurde.</div>
<div class='introduction'><h2>4. Vorläufige Bewertung</h2>
{#PLACE_2}</div>";

/*<h2>4. Vorläufige Ergebnisse</h2>
Bis zum jetzigen Zeitpunkt wurden auf %plagnumber% von %pagenumber% Textseiten Plagiatstellen nachgewiesen.
Dokumentiert sind Textübernahmen aus insgesamt %sourcesnumber% verschiedenen Quellen.
</div>";*/
    /*private $defaultText2 = "<div class='introduction'><h2>4. Vorläufige Bewertung</h2>
Bezüglich der in diesem Bericht dokumentierten Plagiate lässt sich zusammenfassend
feststellen:
{#PLACE_2}</div>";*/

/*<h2>6. Weiterführende Links</h2>
<ul>
<li>Übersicht über die Dissertation</li>
<li>Übersicht über die plagiierten Quellen</li></ul></div>";*/

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


            $query = $this->em->createQuery("SELECT f 
            FROM Application_Model_Document_Fragment f, Application_Model_State s 
            WHERE f.document = :document AND s.name = :state AND f.state=s.id");
            //$query = $this->em->createQuery("SELECT f FROM Application_Model_Document_Fragment f JOIN f.state s WHERE f.document = :document AND s.name = :state");
            $query->setParameter("document", $task->getRessource()->getTarget()->getId());
            $query->setParameter("state", "approved");

            $fragments = $query->getResult();
            echo "LINE: ".count($fragments)."\n";
            if (count($fragments) > 0) {
                $report = $this->createReport($fragments, $task->getRessource());
                                         echo "report START\n";
                // update task
                $task->setState($this->em->getRepository('Application_Model_State')->findOneByName("completed"));
                $task->setProgressPercentage(100);
            } else {
                $task->setState($this->em->getRepository('Application_Model_State')->findOneByName("error"));
                $task->setProgressPercentage(100);
            }
            $this->em->persist($task);
            $this->em->flush();

            if (count($fragments) > 0) {
                // notification
                Unplagged_Helper::notify("report_created", $report, $task->getInitiator());
            }
        }
        else {echo "\nWARNING: there is no task to proceed. Maybe no fragments exists for this case.\n";}
    }

    private function createReport($fragments, $report) {
        $this->pagenumber = 2;
        $this->nbSources = 0;
        
        $currentCase = $report->getCase();
        $casename = $currentCase->getAlias();
        $filepath = BASE_PATH . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "reports";

        $array_html = Unplagged_HtmlLayout::htmlLayout($casename, $fragments);

        //$plagiat = $array_html[0]["bibtextplag"];
        
        $content = '<div style="margin:auto; width: 500px; text-align:center; margin-top: 300px"><h1>Gemeinschaftlicher Bericht</h1><br/><br/>';
        /*$content .= "<h2>Dokumentation von Plagiaten in der Dissertation \"" . $plagiat->getContent("title") . "\" von " .
                $plagiat->getContent("author") . ". " . $plagiat->getContent("address") .
                ". " . $plagiat->getContent("year") . "</h2><br/><br/>";*/
        $content .= "<h2>".$report->getReportTitle()."</h2><br/><br/>";
        //$content .= "<h2>VroniPlag</h2>";
        $content .= "<h2>".$report->getReportGroupName()."</h2>";
        $content .= '<h2 style="font-style:italic">' . $casename . '</h2>';
        $content .= "<br/><br/>";
        $content .= "<h3>" . date("d M Y") . "</h3></div>";
        $content .= $this->getBarCode($currentCase);
        
        $intro1 = str_replace('{#PLACE_1}', $report->getReportIntroduction(), $this->defaultText);
        $intro1 = str_replace('{#PLACE_2}', $report->getReportEvaluation(), $intro1);
        $content .= "<page>" . $intro1 . $this->getFooter(2) . "</page>";
        //$intro2 = str_replace('{#PLACE_2}', $report->getReportEvaluation(), $this->defaultText2);
        //$content .= "<page>" . $intro2 . $this->getFooter(3) . "</page>";
        foreach ($array_html as $fragment) {
            $col1 = $this->cut_text_into_pages($fragment["left"]);
            $col2 = $this->cut_text_into_pages($fragment["right"]);
            $content .= $this->mix_two_columns($col1, $col2, "plagiat", "source");
        }

        $content .= $this->addSources($array_html);
        $content = str_replace('%pagenumber%', $this->pagenumber, $content);
        $content = str_replace('%sourcesnumber%', $this->nbSources, $content);
        $content = str_replace('%plagnumber%', $this->pagenumber, $content);
        
        /* $fp = fopen('test.html','w');
          fwrite($fp, $content);
          fclose($fp); */

        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->WriteHTML($content);

        // after the flush, we can access the id and put a unique identifier in the report name
        $filename = $filepath . DIRECTORY_SEPARATOR . $report->getId() . ".pdf";

        $html2pdf->Output($filename, 'F');

        $report->setFilePath($filename);
        $report->setState($this->em->getRepository('Application_Model_State')->findOneByName('generated'));

        $this->em->persist($report);
        $this->em->flush();

        return $report;
    }

    private function addSources($array_html) {
        $this->pagenumber++;
        $sources = "<page><h2>Quellenverzeichnis</h2>";
        foreach ($array_html as $fragment) {//array_expression as $value
            $sources .= $this->writeSource($fragment["bibtextsource"]);
            $this->nbSources++;
        }
        $sources .=$this->getFooter($this->pagenumber) . "</page>";
        return $sources;
    }

    private function writeSource($sce) {
        $source = "";
        if (isset($sce)) {
            $source = "<div class='source'>"
                    . "[" . $sce->getContent("author") . " " . $sce->getContent("year") . "]&nbsp; " . $sce->getContent("author") . ": "
                    . $sce->getContent("title") . ". " . $sce->getContent("journal") . " " . $sce->getContent("address") . " "
                    . $sce->getContent("year") . " " . $sce->getContent("publisher")
                    . "</div>";
        }

        return $source;
    }

    private function getBarCode($case) {
        $str_svg = $case->getBarcode(80, 150, 100, false, '%')->render();

        $str_svg = str_replace('svg', 'draw', $str_svg);
        $str_svg = str_replace('width=', 'w=', $str_svg);
        $str_svg = str_replace('height=', 'h=', $str_svg);

        return "<h2>Barcode</h2>" . $str_svg;
    }

    private function getFooter($pagenumber) {
        return "<page_footer>" . $pagenumber . "/%pagenumber%</page_footer> ";
    }

    /**
     * Creates an html page element, containing a table
     * with three columns. The first parameter is set in
     * the first column and the second parameter is set
     * in the third column.
     */
    private function create_a_page($td1, $td2, $title1, $title2) {
        $this->pagenumber++;
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
</table>' . $this->getFooter($this->pagenumber) . '</page>';
    }

    /**
     * Removes the multiple blank spaces from the given parameter.
     */
    private function remove_spaces($text) {
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
    private function cut_text_into_pages($text) {
        $text = $this->remove_spaces($text);
        $exploded = array_slice(explode(' ', $text), 0);
        $nbWordsProPage = 400;
        $nbPage = 0;
        $pages = array();
        $rest = "";
        $nbRest = 0;

        for ($i = 0; $i < sizeof($exploded); $i+=$nbWordsProPage) {
            $page = $rest . implode(' ', array_slice($exploded, $i, $nbWordsProPage - $nbRest));
            $result = $this->check($page);
            $rest = $result["toRetrieve"] . " ";
            $nbRest = str_word_count($rest);
            $pages[$nbPage++] = $result["s"];
        }

        return $pages;
    }

    private function result($length, $s) {
        $array = array();
        $array["toRetrieve"] = substr($s, $length, strlen($s) - $length);
        $array["s"] = substr($s, 0, $length);
        $array["original"] = $s;
        return $array;
    }

    private function check($s) {
        $nbST = substr_count($s, ST);
        $nbBT = substr_count($s, GT);
        if ($nbST == $nbBT) {
            $nbSpanOpen = substr_count($s, SPAN_OPEN);
            $nbSpanClose = substr_count($s, SPAN_CLOSE);
            if ($nbSpanOpen == $nbSpanClose) {
                return $this->result(strlen($s), $s);
            } else {
                // 'halli hallo <span>'
                return $this->result(strrpos($s, SPAN_OPEN), $s);
            }
        } else {
            // one tag is not closed
            //'halli hallofff <span></span'
            //'halli hallofff <span'
            $nbSpanOpen = substr_count($s, SPAN_OPEN);
            $nbSpanClose = substr_count($s, SPAN_CLOSE);

            return $this->result(strrpos($s, SPAN_OPEN), $s);
        }
    }

    /**
     * Returns the $index elements of the array.
     * If this element does not exist, a blank
     * space is returned.
     */
    private function get_col($array, $index) {
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
    private function mix_two_columns($col1, $col2, $title1, $title2) {
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
                ' 
             ol li{
                list-style: none;
                background-color: white;
                padding-left: 0px;
                border-left: 1px solid black;                
             }
             ol {
                background-color: #bcbcbc;
                border-top: 1px solid black;
                margin-left:0px;
             }
             .number {
             }
             tr {
                vertical-align: top;
             }
             .introduction{
                margin-left: 25px;
             }' .
                '</style>';
        $size1 = sizeof($col1);
        $size2 = sizeof($col2);
        $size = $size1 > $size2 ? $size1 : $size2;

        for ($i = 0; $i < $size; $i++) {
            $c1 = $this->get_col($col1, $i);
            $c2 = $this->get_col($col2, $i);

            $html .= $this->create_a_page($c1, $c2, $title1, $title2);
        }
        return $html;
    }

}

$reportCreator = new Cron_Document_Page_Reportcreator();
$reportCreator->start();
?>
