<?php

class UploadController extends Zend_Controller_Action
{
        public function init()
        {
        }
    
        public function indexAction() 
        {
                    echo "Hallo";
        }
    
        function uploadAction()
        {
            $uploadform = new Application_Form_UploadForm();            
            if ($this->_request->isPost()) 
            {
                if ($uploadform->isValid(($this->_request->getPost()))) 
                {
                   $adapterupload = new Zend_File_Transfer_Adapter_Http(); 
                   //muss mit der gruppe geklärt werden
                   //Neither APC nor uploadprogress extension installed 
                   /*$adapterprogressbar = new Zend_ProgressBar_Adapter_Console();
                   $adapterupload = Zend_File_Transfer_Adapter_Http::getProgress($adapterprogressbar);
      
                   $adapterupload = null;
                   while (!$adapterupload['done']) 
                   {
                        $adapterupload = Zend_File_Transfer_Adapter_Http::getProgress($adapterupload);
                   }*/
                           
                    $fileDirectory = "./files/";
                    $adapterupload->setDestination($fileDirectory);
                   // $adapterupload->setDestination("./files/");
                    if (!$adapterupload->receive()) 
                    {
                        $messages = $adapterupload->getMessages();
                        echo implode("\n", $messages);
                    }

                    //$uploadedData = $uploadform->getValues();
                    //Zend_Debug::dump($uploadedData, 'Form Data:');

                    $name = $adapterupload->getFileName($file = null, $path = true);//$file = null, $path = true
                    $adapterupload->setOptions(array('useByteString' => false));
                    $size = $adapterupload->getFileSize('filepath');
                    $mime = $adapterupload->getMimeType('filepath');
                    
                    $this->renameFileIfNecessary($name, $fileDirectory);                    
                 } 

             } else 
               {
                // wenn nicht hochgeladen
                $uploadform->populate($this->_request->getPost());
               }
             $this->view->form = $uploadform;
            
        }  
      
        private function renameFileIfNecessary($name, $fileDirectory){
            $newname = $this->_request->getPost('newName');
            if (!empty($newname)) {
                $ext = pathinfo($name, PATHINFO_EXTENSION);                        
                $fullFilePath = $fileDirectory . $newname . "." . $ext;
                $filterFileRename = new Zend_Filter_File_Rename(array('target' => $fullFilePath, 'overwrite' => true));
                $filterFileRename -> filter($name);
            }
        }
        
}
?>
