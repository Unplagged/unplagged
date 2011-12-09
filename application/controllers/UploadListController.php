<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class UploadListController extends Zend_Controller_Action{
    
    function uploadlistAction()
        {
            $fileDirectory = "./files/";
            $data = $this->_request->getPost('file');                
            if ($this->_request->isPost() && !empty($data)) {                
                unlink($fileDirectory . $data);
            }
            
            $handle=opendir($fileDirectory);
            $i=0;
            while (($file = readdir($handle))!==false) 
            {
                $array_filenames[$i++]=$file;
            }
            closedir($handle);
            $this->view->listFiles=$array_filenames;                        
        }
}

?>
