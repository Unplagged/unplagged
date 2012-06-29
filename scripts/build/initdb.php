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
if(!($em->getRepository('Application_Model_Action')->findOneByName("case_published"))){
  unset($data);
  $data["name"] = "case_published";
  $data["title"] = "Case %s was published";
  $data["description"] = "The case was published.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("case_unpublished"))){
  unset($data);
  $data["name"] = "case_unpublished";
  $data["title"] = "Case %s was unpublished";
  $data["description"] = "The case was unpublished.";
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
if(!($em->getRepository('Application_Model_Action')->findOneByName("fragment_removed"))){
  unset($data);
  $data["name"] = "fragment_removed";
  $data["title"] = "Fragment %s was removed";
  $data["description"] = "The fragment was removed.";
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
if(!($em->getRepository('Application_Model_Action')->findOneByName("document_removed"))){
  unset($data);
  $data["name"] = "document_removed";
  $data["title"] = "Document %s was removed";
  $data["description"] = "The document was removedt.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("document_updated"))){
  unset($data);
  $data["name"] = "document_updated";
  $data["title"] = "Document %s was updated";
  $data["description"] = "The document was updated.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("simtext_report_created"))){
  unset($data);
  $data["name"] = "simtext_report_created";
  $data["title"] = "Simtext report %s was created";
  $data["description"] = "The chosen files were compared, the report is available now.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("report_created"))){
  unset($data);
  $data["name"] = "report_created";
  $data["title"] = "report %s was created";
  $data["description"] = "The report is available now.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("detection_report_created"))){
  unset($data);
  $data["name"] = "detection_report_created";
  $data["title"] = "Detection report %s was created";
  $data["description"] = "The report for the automatic plagiarism detection on the document is available now.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("comment_created"))){
  unset($data);
  $data["name"] = "comment_created";
  $data["title"] = "New comment on %s created";
  $data["description"] = "A new comment was created on the element.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("rating_created"))){
  unset($data);
  $data["name"] = "rating_created";
  $data["title"] = "New rating for %s provided";
  $data["description"] = "A new rating was provided for the element.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("rating_updated"))){
  unset($data);
  $data["name"] = "rating_updated";
  $data["title"] = "Provided rating for %s was updated";
  $data["description"] = "A provided rating for the element was updated.";
  $notificationAction = new Application_Model_Action($data);
  $em->persist($notificationAction);
}

// states
if(!($em->getRepository('Application_Model_State')->findOneByName('created'))){
  unset($data);
  $data["name"] = "created";
  $data["title"] = "created";
  $data["description"] = "The element was created.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!($em->getRepository('Application_Model_State')->findOneByName('deleted'))){
  unset($data);
  $data["name"] = "deleted";
  $data["title"] = "deleted";
  $data["description"] = "The element is deleted.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!($em->getRepository('Application_Model_State')->findOneByName('approved'))){
  unset($data);
  $data["name"] = "approved";
  $data["title"] = "approved";
  $data["description"] = "The amount of users required to approve the element was reached.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!($em->getRepository('Application_Model_State')->findOneByName('published'))){
  unset($data);
  $data["name"] = "published";
  $data["title"] = "published";
  $data["description"] = "The element is published.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!($em->getRepository('Application_Model_State')->findOneByName("running"))){
  unset($data);
  $data["name"] = "running";
  $data["title"] = "running";
  $data["description"] = "The task is currently being executed.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!$em->getRepository('Application_Model_State')->findOneByName("scheduled")){
  unset($data);
  $data["name"] = "scheduled";
  $data["title"] = "scheduled";
  $data["description"] = "The task is scheduled.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!($em->getRepository('Application_Model_State')->findOneByName("parsed"))){
  unset($data);
  $data["name"] = "parsed";
  $data["title"] = "parsed";
  $data["description"] = "The document was parsed.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!($em->getRepository('Application_Model_State')->findOneByName("error"))){
  unset($data);
  $data["name"] = "error";
  $data["title"] = "error";
  $data["description"] = "There was an error during a process.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!($em->getRepository('Application_Model_State')->findOneByName("completed"))){
  unset($data);
  $data["name"] = "completed";
  $data["title"] = "completed";
  $data["description"] = "The task was finished.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!($em->getRepository('Application_Model_State')->findOneByName("locked"))){
  unset($data);
  $data["name"] = "locked";
  $data["title"] = "locked";
  $data["description"] = "An element was locked.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!($em->getRepository('Application_Model_State')->findOneByName("activated"))){
  unset($data);
  $data["name"] = "activated";
  $data["title"] = "activated";
  $data["description"] = "A user that can actually use the web page.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!($em->getRepository('Application_Model_State')->findOneByName("registered"))){
  unset($data);
  $data["name"] = "registered";
  $data["title"] = "registered";
  $data["description"] = "A user registered on the page and did not finish the verification process yet.";
  $state = new Application_Model_State($data);
  $em->persist($state);
}
if(!($em->getRepository('Application_Model_State')->findOneByName("generated"))){
  unset($data);
  $data["name"] = "generated";
  $data["title"] = "generated";
  $data["description"] = "The report was generated successfully.";
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
if(!($em->getRepository('Application_Model_Action')->findOneByName("page_created"))){
  unset($data);
  $data["name"] = "page_created";
  $data["title"] = "Page %s was created";
  $data["description"] = "A new page in the document was created.";
  $action = new Application_Model_Action($data);
  $em->persist($action);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("page_updated"))){
  unset($data);
  $data["name"] = "page_updated";
  $data["title"] = "Page %s was updated";
  $data["description"] = "The page in a document was updated.";
  $action = new Application_Model_Action($data);
  $em->persist($action);
}
if(!($em->getRepository('Application_Model_Action')->findOneByName("page_removed"))){
  unset($data);
  $data["name"] = "page_removed";
  $data["title"] = "Page %s was removed";
  $data["description"] = "A page in the document was removed.";
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
if(!($em->getRepository('Application_Model_Action')->findOneByName("report_requested"))){
  unset($data);
  $data["name"] = "report_requested";
  $data["title"] = "User requested a report";
  $data["description"] = "A user requested a fragment report.";
  $action = new Application_Model_Action($data);
  $em->persist($action);
}

//default settings
if(!($em->getRepository('Application_Model_Setting')->findOneBySettingKey("language"))){
  unset($data);
  $setting = new Application_Model_Setting();
  $setting->setSettingKey('language');
  $setting->setValue('de_DE');
  $em->persist($setting);
}


$em->flush();
?>