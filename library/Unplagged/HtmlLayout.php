<?php

class Unplagged_HtmlLayout {

    public static function htmlLayout($case,$fragments) {
        // html body
        $array = array();
        $i = 0;
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

            // // get fragment content
            $content = $fragment->getContent('array', true);

            $divLeft = 'Plag Bibtext: ' . $bibTexPlag['autor'] . ' - ' . $bibTexPlag['titel'] . '<br/>'
						. 'Page from: ' . $pageFromPlag . ' - Line from: ' . $lineFromPlag . '<br/>' 
						. 'Page to: ' . $pageToPlag . ' - Line to: ' . $lineToPlag . '<br/><br/>' ;
                    
            foreach ($content['plag'] as $line) {
                $divLeft .= $line;
            }

            $divRight = 'Source Bibtext: ' . $bibTexSource['autor'] . ' - ' . $bibTexSource['titel'] . '<br/>'
						. 'Page from: ' . $pageFromSource . ' - Line from: ' . $pageToSource . '<br/>' 
						. 'Page to: ' . $lineFromSource . ' - Line to: ' . $lineToSource . '<br/><br/>' ;
						
            foreach ($content['source'] as $line) {
                $divRight .= $line;
            }

            $array[$i]["left"]=$divLeft;
            $array[$i]["right"]=$divRight;
        }

        return $array;
    }

}

?>