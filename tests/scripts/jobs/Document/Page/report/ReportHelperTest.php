<?php

require_once './../../../../../../scripts/jobs/Document/Page/report/ReportHelper.php';

$helper = new ReportHelper();
$report = new Application_Model_Report();
$case = new Application_Model_Case();
$case->setAlias("alias");
$doc = new Application_Model_Document();
$doc->addPage(getPage(getBibTex("Die große Arbeit")));
$case->setTarget($doc);
$case->updateBarcodeData();
$report->setCase($case);
$fragments = array(
    getFragment(),
    getFragment()
);

$helper->createReport($fragments, $report);

function getFragment() {
    $fragment = new Application_Model_Document_Fragment();
    $plag = new Application_Model_Document_Fragment_Partial();
    $lineFrom = new Application_Model_Document_Page_Line();
    $lineFrom->setPage(getPage(getBibTex("The big thesis.")));
    $plag->setLineFrom($lineFrom);
    $plag->setLineTo($lineFrom);
    $fragment->setPlag($plag);
    $fragment->setSource($plag);
    return $fragment;
} 

function getPage($bibtex){
    $page = new Application_Model_Document_Page();
    $document = new Application_Model_Document();
    $document->setBibTex($bibtex);
    
    $page->setDocument($document);
    $page->setPageNumber(1);
    return $page;
}

function getBibTex($title){
    $bibtex = new Application_Model_BibTex();
    $bibtex->setContent($title, "title");
    $bibtex->setContent("Karl-Heinz Müller", "author");
    $bibtex->setContent("Hauptstraße 10, 10256 BERLIN", "address");
    $bibtex->setContent(1998, "year");
    return $bibtex;
}
?>
