<?php

class Unplagged_HtmlLayout {

    public static function htmlLayout($case,$fragments) {

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


            $html .= '<p>Plag Bibtext: ' . $bibTexPlag['autor'] . ' - ' . $bibTexPlag['titel'] . '</p>' .
                    '<p>Source Bibtext' . $bibTexSource['autor'] . ' - ' . $bibTexSource['titel'] . '</p>';

            $html .= '<p> Page from: ' . $pageFromPlag . ' to:' . $pageToPlag . '</p>' .
                    '<p> Page from: ' . $pageFromSource . ' to:' . $pageToSource . '</p>';

            $html .= '<p>Plagiarized Text </p>' .
                    '<p> Line from: ' . $lineFromPlag . ' to:' . $lineToPlag . '</p>' .
                    '<p>Source Text </p>' .
                    '<p> Line from: ' . $lineFromSource . ' to:' . $lineToSource . '</p>';

            // // get fragment content
            $content = $fragment->getContent('array', true);

            //$divLeft = "<div class='text'";
            $divLeft = 'Plag Bibtext: ' . $bibTexPlag['autor'] . ' - ' . $bibTexPlag['titel'] . '<br/>'
						. 'Page from: ' . $pageFromPlag . ' - Line from: ' . $lineFromPlag . '<br/>' 
						. 'Page to: ' . $pageToPlag . ' - Line to: ' . $lineToPlag . '<br/>' ;
                    
            foreach ($content['plag'] as $line) {
                $divLeft .= $line;
            }
            //$divLeft .= "</div>";

            //$divRight = "<div class='text'";
            $divRight = 'Source Bibtext: ' . $bibTexSource['autor'] . ' - ' . $bibTexSource['titel'] . '<br/>'
						. 'Page from: ' . $pageFromSource . ' - Line from: ' . $pageToSource . '<br/>' 
						. 'Page to: ' . $lineFromSource . ' - Line to: ' . $lineToSource . '<br/>' ;
						
            foreach ($content['source'] as $line) {
                $divRight .= $line;
            }
            //$divRight .= "</div>";
            $div = "<div>" . $divLeft . $divRight . "</div>";
            $html .= $div;
        }

        // html foot
        $html .='</body></html>';
        $array = array();
        $array[0]=$divLeft;
        $array[1]=$divRight;
        //var_dump($array);
        return $array;
    }

}

?>