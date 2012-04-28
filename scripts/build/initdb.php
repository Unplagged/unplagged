<?php

/*
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

include 'initbase.php';

// 1) init notification actions
if(!($em->getRepository('Application_Model_Action')->findOneByName("user_registered"))){
  unset($data);
  $data["name"] = "user_registered";
  $data["title"] = "User %s registered";
  $data["description"] = "A user registerd on the plattform.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("user_updated_profile"))){
  unset($data);
  $data["name"] = "user_updated_profile";
  $data["title"] = "User %s updated the own profile.";
  $data["description"] = "A user updated the own account.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("user_verified"))){
  unset($data);
  $data["name"] = "user_verified";
  $data["title"] = "User %s verified";
  $data["description"] = "A user verified the own account.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("user_requested_password"))){
  unset($data);
  $data["name"] = "user_requested_password";
  $data["title"] = "User %s requested password";
  $data["description"] = "A user requested the own password.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("case_created"))){
  unset($data);
  $data["name"] = "case_created";
  $data["title"] = "Case %s was created";
  $data["description"] = "A case was created.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("case_updated"))){
  unset($data);
  $data["name"] = "case_updated";
  $data["title"] = "Case %s was updated";
  $data["description"] = "The case was updated.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("file_uploaded"))){
  unset($data);
  $data["name"] = "file_uploaded";
  $data["title"] = "File %s was uploaded";
  $data["description"] = "A file was uploaded.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("fragment_created"))){
  unset($data);
  $data["name"] = "fragment_created";
  $data["title"] = "Fragment %s was created";
  $data["description"] = "A new fragment was created.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("fragment_updated"))){
  unset($data);
  $data["name"] = "fragment_updated";
  $data["title"] = "Fragment %s was updated";
  $data["description"] = "The fragment was updated.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("document_created"))){
  unset($data);
  $data["name"] = "document_created";
  $data["title"] = "Document %s was created";
  $data["description"] = "A file was parsed into a document.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}

// 2) init user states
if(!($em->getRepository('Application_Model_State')->findOneByName("user_registered"))){
  unset($data);
  $data["name"] = "user_registered";
  $data["title"] = "registered";
  $data["description"] = "A user registered on the page and did not finish the verification process yet.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
$element = $em->getRepository('Application_Model_State')->findOneByName("user_activated");
if(empty($element)){
  unset($data);
  $data["name"] = "user_activated";
  $data["title"] = "activated";
  $data["description"] = "A user that can actually use the web page.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!($em->getRepository('Application_Model_State')->findOneByName("user_locked"))){
  unset($data);
  $data["name"] = "user_locked";
  $data["title"] = "locked";
  $data["description"] = "A user that was locked.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}

// 3) report states
if(!$em->getRepository('Application_Model_State')->findOneByName("report_running")){
  unset($data);
  $data["name"] = "report_running";
  $data["title"] = "running";
  $data["description"] = "The report is currently being generated.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!($em->getRepository('Application_Model_State')->findOneByName("task_scheduled"))){
  unset($data);
  $data["name"] = "task_scheduled";
  $data["title"] = "scheduled";
  $data["description"] = "A is being scheduled, and will be generated asap.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!($em->getRepository('Application_Model_State')->findOneByName("task_finished"))){
  unset($data);
  $data["name"] = "task_finished";
  $data["title"] = "finished";
  $data["description"] = "The task was finished.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!($em->getRepository('Application_Model_State')->findOneByName("report_generated"))){
  unset($data);
  $data["name"] = "report_generated";
  $data["title"] = "generated";
  $data["description"] = "The report was generated successfully.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!($em->getRepository('Application_Model_State')->findOneByName("report_error"))){
  unset($data);
  $data["name"] = "report_error";
  $data["title"] = "error";
  $data["description"] = "There was an error, the report could not be generated.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}

// 4) fragment types
if(!($em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("UnbekannteQuelle"))){
  unset($data);
  $data["name"] = "UnbekannteQuelle";
  $data["description"] = "UnbekannteQuelle";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}
if(!($em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("KeinPlagiat"))){
  unset($data);
  $data["name"] = "KeinPlagiat";
  $data["description"] = "KeinPlagiat";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}
if(!($em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("Verschleierung"))){
  unset($data);
  $data["name"] = "Verschleierung";
  $data["description"] = "Verschleierung";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}
if(!($em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("HalbsatzFlickerei"))){
  unset($data);
  $data["name"] = "HalbsatzFlickerei";
  $data["description"] = "HalbsatzFlickerei";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}
if(!($em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("ShakeAndPaste"))){
  unset($data);
  $data["name"] = "ShakeAndPaste";
  $data["description"] = "ShakeAndPaste";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}
if(!($em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("ÜbersetzungsPlagiat"))){
  unset($data);
  $data["name"] = "ÜbersetzungsPlagiat";
  $data["description"] = "ÜbersetzungsPlagiat";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}
if(!($em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("StrukturPlagiat"))){
  unset($data);
  $data["name"] = "StrukturPlagiat";
  $data["description"] = "StrukturPlagiat";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}
if(!($em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("BauernOpfer"))){
  unset($data);
  $data["name"] = "BauernOpfer";
  $data["description"] = "BauernOpfer";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}
if(!($em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName("VerschaerftesBauernOpfer"))){
  unset($data);
  $data["name"] = "VerschaerftesBauernOpfer";
  $data["description"] = "VerschärftesBauernOpfer";
  $fragmentType = new Application_Model_Document_Fragment_Type($data);
  $em->persist($fragmentType);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("file_parse"))){
  unset($data);
  $data["name"] = "file_parse";
  $data["title"] = "Parse file %s";
  $data["description"] = "Parse a file into a document";
  $action = new Application_Model_Action($data);
  $em->persist($action);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("page_simtext"))){
  unset($data);
  $data["name"] = "page_simtext";
  $data["title"] = "Simtext page %s";
  $data["description"] = "Create a simtext report for a single page";
  $action = new Application_Model_Action($data);
  $em->persist($action);
}
if(!($em->getRepository('Application_Model_State')->findOneByName("task_running"))){
  unset($data);
  $data["name"] = "task_running";
  $data["title"] = "running";
  $data["description"] = "The task is currently being executed.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}

$em->flush();
?>