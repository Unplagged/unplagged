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
 * PlagAware webservice client.
 *
 * @author benjamin
 */
class Unplagged_Detector_PlagAware{

  private $_em;
  private $paUserCode;
  private $paDryRun;
  private $paResultUrl;
  private $serviceName = "PlagAware";

  public function __construct(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;

    // user code will be set in ini file 
    $this->paUserCode = "57947ed4d4130c7ff0a057c8654dd1a3";
    $this->paDryRun = false;
    $this->paResultUrl = "http://unplagged:Guttenberg@preview.unplagged.com/document/response-plagiarism/detector/plagaware/report/";
  }

  /**
   * Starts the detection on a specific page.
   * 
   * @param Application_Model_Document_Page $page 
   */
  public function detect(Application_Model_Document_Page_DetectionReport &$report){
    $url = "http://www.plagaware.de/service/submittext";
    $fields = array(
      'UserCode'=>urlencode($this->paUserCode),
      'ResultUrl'=>urlencode($this->paResultUrl . $report->getId()),
      'TestText'=>urlencode($report->getPage()->getContent()),
      'DryRun'=>urlencode($this->paDryRun)
    );

    // url-ify the data for the POST
    $fields_string = "";
    foreach($fields as $key=>$value){
      $fields_string .= $key . '=' . $value . '&';
    }
    rtrim($fields_string, '&');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch,CURLOPT_POST,count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);

    $output = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    
    if($output == "Success: Text submitted") {
      return true;
    } else {
      return false;
    }
  }
  
  /**
   * Handle the response, when the detection is finished.
   */
  public function handleResult(&$result) {
    $report = $this->_em->getRepository('Application_Model_Document_Page_DetectionReport')->findOneById($result["report"]);
    
    $percentage = !empty($result["result"]) ? $result["result"] : 0;
    $content = !empty($result["status"]) ? "Status: " . $result["status"] : null;

    $report->setPercentage($percentage);
    $report->setContent($content);
    $report->setState("finished");
    
    return $report;
  }
  
  public function getServiceName(){
    return $this->serviceName;
  }

}

?>
