<?php
require_once(realpath(dirname(__FILE__)) . "./../../../Base.php");
require_once(BASE_PATH . '/library/html2pdf/html2pdf.class.php');
require_once 'templates/Templates.php';

define("SPAN_OPEN", "<span");
define("SPAN_CLOSE", "</span");
define("ST", "<");
define("GT", ">");

class ReportHelper {

    private $pagenumber;
    
    public function test() {
        //var_dump(Templates::getBibliography("[source1, source2, source3]", "page 2/34"));
        //var_dump(Templates::getBarcode("svg-svg-svg-svg"));
        //var_dump(Templates::getFooter("100"));
        //var_dump(Templates::getPage("titl", "title2", "blabla", "bla bla bka", "sexy footer"));
        //var_dump(Templates::getSource("Joseph Shell", "2000", "I like playing with it.", "The best journal ever", "Backfabrik - 10250 BERLIN", "NatureOnline"));
        //var_dump(Templates::getIntro1("krasser footer"));
        //var_dump(Templates::getIntro2("cooles feature footer"));
    }

    public function createReport($fragments, $report) {
        $this->pagenumber = 3;

        $currentCase = $report->getCase();
        $casename = $currentCase->getAlias();
        $filepath = BASE_PATH . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "reports";

        $array_html = Unplagged_HtmlLayout::htmlLayout($casename, $fragments);
        $plagiat = $array_html[0]["bibtextplag"];

        /*$content = '<div style="margin:auto; width: 500px; text-align:center; margin-top: 300px"><h1>Gemeinschaftlicher Bericht</h1><br/><br/>';
        $content .= "<h2>Dokumentation von Plagiaten in der Dissertation \"" . $plagiat->getContent("title") . "\" von " .
                $plagiat->getContent("author") . ". " . $plagiat->getContent("address") .
                ". " . $plagiat->getContent("year") . "</h2><br/><br/>";
        $content .= "<h2>VroniPlag</h2>";
        $content .= '<h2 style="font-style:italic">' . $casename . '</h2>';
        $content .= "<br/><br/>";
        $content .= "<h3>" . date("d M Y") . "</h3></div>";*/
        $content = Templates::getFirstPage($plagiat->getContent("title"), $plagiat->getContent("author"), 
                $plagiat->getContent("address"), $plagiat->getContent("year"), $casename, date("d M Y"));
        $content .= $this->getBarCode($currentCase);
        $content .= Templates::getIntro1($this->getFooter(2));
        $content .= Templates::getIntro2($this->getFooter(3));
        foreach ($array_html as $fragment) {
            $col1 = $this->cut_text_into_pages($fragment["left"]);
            $col2 = $this->cut_text_into_pages($fragment["right"]);
            $content .= $this->mix_two_columns($col1, $col2, "plagiat", "source");
        }

        $content .= $this->addSources($array_html);
        $content = str_replace('%pagenumber%', $this->pagenumber, $content);

        /* $fp = fopen('test.html','w');
          fwrite($fp, $content);
          fclose($fp); */

        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->WriteHTML($content);

        // after the flush, we can access the id and put a unique identifier in the report name
        $filename = $filepath . DIRECTORY_SEPARATOR . $report->getId() . ".pdf";

        $html2pdf->Output($filename, 'F');

        $report->setFilePath($filename);
        
        return $report;
    }

    private function addSources($array_html) {
        $this->pagenumber++;
        $sources = "<page><h2>Quellenverzeichnis</h2>";
        foreach ($array_html as $fragment) {//array_expression as $value
            $sources .= $this->writeSource($fragment["bibtextsource"]);
        }
        //$sources .=$this->getFooter($this->pagenumber) . "</page>";
        $sources .= Templates::getFooter($this->pagenumber) . "</page>";
        return $sources;
    }

    private function writeSource($sce) {
        $source = "";
        if (isset($sce)) {
            /*$source = "<div class='source'>"
                    . "[" . $sce->getContent("author") . " " . $sce->getContent("year") . "]&nbsp; " . $sce->getContent("author") . ": "
                    . $sce->getContent("title") . ". " . $sce->getContent("journal") . " " . $sce->getContent("address") . " "
                    . $sce->getContent("year") . " " . $sce->getContent("publisher")
                    . "</div>";*/
            $source = Templates::getSource(
                    $sce->getContent("author"), 
                    $sce->getContent("year"), 
                    $sce->getContent("title"), 
                    $sce->getContent("journal"), 
                    $sce->getContent("address"), 
                    $sce->getContent("publisher")
            );
        }

        return $source;
    }

    private function getBarCode($case) {
        $str_svg = $case->getBarcode(80, 150, 100, false, '%')->render();

        $str_svg = str_replace('svg', 'draw', $str_svg);
        $str_svg = str_replace('width=', 'w=', $str_svg);
        $str_svg = str_replace('height=', 'h=', $str_svg);

        return Templates::getBarcode($str_svg);
        //return "<h2>Barcode</h2>" . $str_svg;
    }

    private function getFooter($pagenumber) {
        return Templates::getFooter($pagenumber);
        //return "<page_footer>" . $pagenumber . "/%pagenumber%</page_footer> ";
    }

    /**
     * Creates an html page element, containing a table
     * with three columns. The first parameter is set in
     * the first column and the second parameter is set
     * in the third column.
     */
    private function create_a_page($td1, $td2, $title1, $title2) {
        $this->pagenumber++;
        return Templates::getPage($title1, $title2, $td1, $td2, $this->getFooter($this->pagenumber));
        /*return '
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
</table>' . $this->getFooter($this->pagenumber) . '</page>';*/
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
        /*$html = '<style type="text/css">' .
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
                '</style>';*/
        $html = Templates::getStyle();
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

?>
