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

/**
 * 
 */
class Unplagged_HtmlLayout {

    public static function htmlLayout($case, $fragments) {
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
                         
            // get fragment content
            $content = $fragment->getContent('list', true);

            /*$divLeft = 'Plag Bibtext: ' . $bibTexPlag['autor'] . ' - ' . $bibTexPlag['titel'] . '<br/>'
                    . 'Page from: ' . $pageFromPlag . ' - Line from: ' . $lineFromPlag . '<br/>'
                    . 'Page to: ' . $pageToPlag . ' - Line to: ' . $lineToPlag . '<br/><br/>';*/
            
             $divLeft = 'Plag Bibtextkürzel: ' . "[" . $bibTexPlag["autor"] ." ". $bibTexPlag["jahr"]. "] " .'<br/>'
                    . 'Page from: ' . $pageFromPlag . ' - Line from: ' . $lineFromPlag . '<br/>'
                    . 'Page to: ' . $pageToPlag . ' - Line to: ' . $lineToPlag . '<br/><br/>';

            $divLeft .= $content['plag'];

            $divRight = 'Source Bibtextkürzel: ' . "[" . $bibTexSource["autor"] ." ". $bibTexSource["jahr"]. "]". '<br/>'
                    . 'Page from: ' . $pageFromSource . ' - Line from: ' . $pageToSource . '<br/>'
                    . 'Page to: ' . $lineFromSource . ' - Line to: ' . $lineToSource . '<br/><br/>';

            $divRight .= $content['source'];

            $array[$i]["left"] = $divLeft;
            $array[$i]["right"] = $divRight;
            $array[$i]["bibtextplag"] = $bibTexPlag;
            $array[$i]["bibtextsource"] = $bibTexSource;
            $i++;
        }

        return $array;
    }

}
?>