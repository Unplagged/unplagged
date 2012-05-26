<?php

class Unplagged_HtmlLayout {

    public static function htmlLayout($case, $note, $fragments) {

        $casename = 'Eine kritische Auseinandersetzung mit der Dissertation von Prof. Dr. : ' . $case;

        // html head
        $html = '<html>' .
                '<style type="text/css">' .
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
                '</style>'
        ;

        // html body
        $html .= '<body><p>Case name: ' . $casename . '</p>';

        // note
        $html .= '<p>Note: ' . $note . '</p>';

        // iteration of fragments
        foreach ($fragments as $fragment) {

            // get bibtex infos
            $bibTexPlag = $fragment->getPlag()->getLineFrom()->getPage()->getDocument()->getBibTex();
            $bibTexSource = $fragment->getSource()->getLineFrom()->getPage()->getDocument()->getBibTex();

            // get page infos
            $pageFromPlag = $fragment->getPlag()->getLineFrom()->getPage()->getPageNumber();
            $pageToPlag = $fragment->getPlag()->getLineTo()->getPage()->getPageNumber();

            $pageFromSource = $fragment->getSource()->getLineFrom()->getPage()->getPageNumber();
            $pageToSource = $fragment->getSource()->getLineTo()->getPage()->getPageNumber();

            // get line infos
            $lineFromPlag = $fragment->getPlag()->getLineFrom()->getLineNumber();
            $lineToPlag = $fragment->getPlag()->getLineTo()->getLineNumber();

            $lineFromSource = $fragment->getSource()->getLineFrom()->getLineNumber();
            $lineToSource = $fragment->getSource()->getLineTo()->getLineNumber();


            $html .= '<p>Plag Bibtext: ' . $bibTexPlag . '</p>' .
                    '<p>Source Bibtext' . $bibTexSource . '</p>';

            $html .= '<p> Page from: ' . $pageFromPlag . ' to:' . $pageToPlag . '</p>' .
                    '<p> Page from: ' . $pageFromSource . ' to:' . $pageToSource . '</p>';


            $html .= '<p>Plagiarized Text </p>' .
                    '<p> Line from: ' . $lineFromPlag . ' to:' . $lineToPlag . '</p>' .
                    '<p>Source Text </p>' .
                    '<p> Line from: ' . $lineFromSource . ' to:' . $lineToSource . '</p>';

            // // get fragment content
            $content = $fragment->getContent('array', true);
//                var_dump($content['plag']);
//		$plagText = $content['plag'];
//		$sourceText = $content['source'];
//               $minlength = 4;
//                $plagText = utf8_decode($plagText);
//                $sourceText = utf8_decode($sourceText);
            //$html .= Unplagged_CompareText::compare($plagText, $sourceText, $minlength);

            // Solution with two divs: the left one has an absolute position and
            // a fixed width. It does not work: because of the absolute position
            // the content of the left div won't be displayed on the second page.
//            $divLeft = "<div class='text' style='position:relative; left:0pt; width:50%;'>";
//            foreach ($content['plag'] as $line) {
//                $divLeft .= $line;
//            }
//            $divLeft .= "</div>";
//
////            $divRight = "<div style='margin-left:51%;'>";
//            $divRight = "<div class='text' style='margin-left:51%;'>";
//            foreach ($content['source'] as $line) {
//                $divRight .= $line;
//            }
//            $divRight .= "</div>";
//            $div = "<div style='clear:both; position:relative;'>" . $divLeft . $divRight . "</div>";
//            $html .= $div;

            // Solution with table does not work if the content of a tr contains
            //  a pagebreak;
//            $table = "<table border='1'><tr>";
//
//            $thRight = "<td>";
//            foreach ($content['source'] as $line) {
//                $thRight .= $line;
//            }
//            $thRight .= "</td>";
//            $thLeft = "<td>";
//            foreach ($content['source'] as $line) {
//                $thLeft .= $line;
//            }
//            $thLeft .= "</td>";
//
//            $table .= $thRight . $thLeft . "</tr>";
//            $html .= $table;
            
            $divLeft = "<div class='text'";
            foreach ($content['plag'] as $line) {
                $divLeft .= $line;
            }
            $divLeft .= "</div>";

            $divRight = "<div class='text'";
            foreach ($content['source'] as $line) {
                $divRight .= $line;
            }
            $divRight .= "</div>";
            $div = "<div>" . $divLeft . $divRight . "</div>";
            $html .= $div;
        }

        // html foot
        $html .='</body></html>';
        // @todo: can this be removed?
       // $filepath = BASE_PATH . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "reports";
       // $filename = $filepath . DIRECTORY_SEPARATOR . "div.html";
      //  file_put_contents($filename, $html);

        return $html;
    }

}

?>