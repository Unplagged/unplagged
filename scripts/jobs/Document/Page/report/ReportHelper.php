<?php
require_once 'templates/Templates.php';

class ReportHelper {
    public function createReport() {
        //var_dump(Templates::getBibliography("[source1, source2, source3]", "page 2/34"));
        //var_dump(Templates::getBarcode("svg-svg-svg-svg"));
        //var_dump(Templates::getFooter("100"));
        //var_dump(Templates::getPage("titl", "title2", "blabla", "bla bla bka", "sexy footer"));
        //var_dump(Templates::getSource("Joseph Shell", "2000", "I like playing with it.", "The best journal ever", "Backfabrik - 10250 BERLIN", "NatureOnline"));
        //var_dump(Templates::getIntro1("krasser footer"));
        var_dump(Templates::getIntro2("cooles feature footer"));
        
    }
}
?>
