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

include 'initbase.php';

$actions = array(
    array('name'=>'user_registered', 'title'=>'User %s registered', 'description'=>'A user registered on the plattform.'),
    array('name'=>'user_updated_profile', 'title'=>'User %s updated the own profile.', 'description'=>'A user updated the own account.'),
    array('name'=>'user_verified', 'title'=>'User %s verified', 'description'=>'A user verified the own account.'),
    array('name'=>'user_requested_password', 'title'=>'User %s requested password', 'description'=>'A user requested the own password.'),
    array('name'=>'case_created', 'title'=>'Case %s was created', 'description'=>'A case was created.'),
    array('name'=>'case_updated', 'title'=>'Case %s was updated', 'description'=>'The case was updated.'),
    array('name'=>'case_published', 'title'=>'Case %s was published', 'description'=>'The case was published.'),
    array('name'=>'case_unpublished', 'title'=>'Case %s was unpublished', 'description'=>'The case was unpublished.'),
    array('name'=>'file_uploaded', 'title'=>'File %s was uploaded', 'description'=>'A file was uploaded.'),
    array('name'=>'fragment_created', 'title'=>'Fragment %s was created', 'description'=>'A new fragment was created.'),
    array('name'=>'fragment_updated', 'title'=>'Fragment %s was updated', 'description'=>'The fragment was updated.'),
    array('name'=>'fragment_removed', 'title'=>'Fragment %s was removed', 'description'=>'The fragment was removed.'),
    array('name'=>'document_created', 'title'=>'Document %s was created', 'description'=>'A file was parsed into a document.'),
    array('name'=>'document_removed', 'title'=>'Document %s was removed', 'description'=>'The document was removed.'),
    array('name'=>'document_updated', 'title'=>'Document %s was updated', 'description'=>'The document was updated.'),
    array('name'=>'simtext_report_created', 'title'=>'Simtext report %s was created', 'description'=>'The chosen files were compared, the report is available now.'),
    array('name'=>'report_created', 'title'=>'report %s was created', 'description'=>'The report is available now.'),
    array('name'=>'detection_report_created', 'title'=>'Detection report %s was created', 'description'=>'The report for the automatic plagiarism detection on the document is available now.'),
    array('name'=>'comment_created', 'title'=>'New comment on %s created', 'description'=>'A new comment was created on the element.'),
    array('name'=>'rating_created', 'title'=>'New rating for %s provided', 'description'=>'A new rating was provided for the element.'),
    array('name'=>'rating_updated', 'title'=>'Provided rating for %s was updated', 'description'=>'A provided rating for the element was updated.'),
    array('name'=>'file_parse', 'title'=>'Parse file %s', 'description'=>'Parse a file into a document'),
    array('name'=>'page_created', 'title'=>'Page %s was created', 'description'=>'A new page in the document was created.'),
    array('name'=>'page_updated', 'title'=>'Page %s was updated', 'description'=>'The page in a document was updated.'),
    array('name'=>'page_removed', 'title'=>'Page %s was removed', 'description'=>'A page in the document was removed.'),
    array('name'=>'page_simtext', 'title'=>'Simtext page %s', 'description'=>'Create a simtext report for a single page'),
    array('name'=>'report_requested', 'title'=>'User requested a report', 'description'=>'A user requested a fragment report.'),
);

function setupActions(array $actions, $em){
  foreach($actions as $action){
    if(!($em->getRepository('Application_Model_Action')->findOneByName($action['name']))){
      $actionObject = new Application_Model_Action($action);
      $em->persist($actionObject);
    }
  }
  $em->flush();
}

setupActions($actions, $em);


$states = array(
    array('name'=>'created', 'title'=>'created', 'description'=>'The element was created.'),
    array('name'=>'deleted', 'title'=>'deleted', 'description'=>'The element is deleted.'),
    array('name'=>'approved', 'title'=>'approved', 'description'=>'The amount of users required to approve the element was reached.'),
    array('name'=>'published', 'title'=>'published', 'description'=>'The element is published.'),
    array('name'=>'running', 'title'=>'running', 'description'=>'The task is currently being executed.'),
    array('name'=>'scheduled', 'title'=>'scheduled', 'description'=>'The task is scheduled.'),
    array('name'=>'parsed', 'title'=>'parsed', 'description'=>'The document was parsed.'),
    array('name'=>'error', 'title'=>'error', 'description'=>'There was an error during a process.'),
    array('name'=>'', 'title'=>'', 'description'=>'The task was finished.'),
    array('name'=>'completed', 'title'=>'completed', 'description'=>''),
    array('name'=>'locked', 'title'=>'locked', 'description'=>'An element was locked.'),
    array('name'=>'activated', 'title'=>'activated', 'description'=>'A user that can actually use the web page.'),
    array('name'=>'registered', 'title'=>'registered', 'description'=>'A user registered on the page and did not finish the verification process yet.'),
    array('name'=>'generated', 'title'=>'generated', 'description'=>'The report was generated successfully.')
);

function setupStates(array $states, $em){
  foreach($states as $state){
    if(!($em->getRepository('Application_Model_State')->findOneByName($state['name']))){
      $stateObject = new Application_Model_State($state);
      $em->persist($stateObject);
    }
  }
  $em->flush();
}

setupStates($states, $em);


$fragmentTypes = array(
    array('name'=>'UnbekannteQuelle', 'description'=>'UnbekannteQuelle'),
    array('name'=>'KeinPlagiat', 'description'=>'KeinPlagiat'),
    array('name'=>'Verschleierung', 'description'=>'Verschleierung'),
    array('name'=>'HalbsatzFlickerei', 'description'=>'HalbsatzFlickerei'),
    array('name'=>'ShakeAndPaste', 'description'=>'ShakeAndPaste'),
    array('name'=>'ÜbersetzungsPlagiat', 'description'=>'ÜbersetzungsPlagiat'),
    array('name'=>'StrukturPlagiat', 'description'=>'StrukturPlagiat'),
    array('name'=>'BauernOpfer', 'description'=>'BauernOpfer'),
    array('name'=>'VerschaerftesBauernOpfer', 'description'=>'VerschaerftesBauernOpfer')
);

function setupFragmentTypes(array $fragmentTypes, $em){
  foreach($fragmentTypes as $fragmentType){
    if(!($em->getRepository('Application_Model_Document_Fragment_Type')->findOneByName($fragmentType['name']))){
      $fragmentTypeObject = new Application_Model_Document_Fragment_Type($fragmentType);
      $em->persist($fragmentTypeObject);
    }
  }
  $em->flush();
}

setupFragmentTypes($fragmentTypes, $em);


//default settings
if(!($em->getRepository('Application_Model_Setting')->findOneBySettingKey("language"))){
  unset($data);
  $setting = new Application_Model_Setting('language');
  $setting->setValue('de_DE');
  $em->persist($setting);
}

$em->flush();