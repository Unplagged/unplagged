<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Parser
 *
 * @author benjamin
 */
class TemplateParser {

    private $tplDirectory;

    public function __construct($tplDirectory) {
        $this->tplDirectory = $tplDirectory;
    }

    public function parse($template, $data) {
        $response = $template;
        if (!empty($data) || is_array($data)) {
            foreach ($data as $key => $value) {
                $response = str_replace('{$' . $key . '}', $value, $response);
            }
        }
        return $response;
    }

    public function parseFile($filename, $data) {
        return $this->parse(file_get_contents($this->tplDirectory . $filename), $data);
    }

}

?>
