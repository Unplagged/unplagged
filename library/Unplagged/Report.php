<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Report
 *
 * @author Gast
 */
class Unplagged_Report {

    public static function createReport($casename, $note, $fragments) {

        require_once(BASE_PATH . '/library/dompdf/dompdf_config.inc.php');
        spl_autoload_register('DOMPDF_autoload');


        $html = Unplagged_HtmlLayout::htmlLayout($casename, $note, $fragments);

        $dompdf = new DOMPDF();
        $dompdf->set_paper('a4', 'portrait');
        $dompdf->load_html($html);
        $dompdf->render();
        $filepath = BASE_PATH . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "reports";
        $filename = $filepath . DIRECTORY_SEPARATOR . "Report_".$casename.".pdf";
        //$dompdf->stream($filename);
        $output = $dompdf->output();
        file_put_contents($filename, $output);

        return $filename;
    }

}

?>
