<?php

use UnpCommon\Controller\BaseController;

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
namespace UnpApplication\Controller;

use UnpCommon\Controller\BaseController;

/**
 * Provides action to handle
 */
class FileController extends BaseController{

  /**
   * Sends the requested image file with the appropriate content-type, in order to get browsers to display it like
   * a really downloaded image in a publicly accessible folder. The benefit is, that we can check the permissions
   * of the user here, which wouldn't be possible in a public folder.
   */
  public function viewAction(){
    $id = $this->params('id');
    $response = $this->getResponse();

    if(!empty($id)){
      $file = $this->em->getRepository('\UnpCommon\Model\File')->findOneById($id);

      if($file && $file->isImage() && is_readable($file->getFullPath())){
        $response->getHeaders()
                ->addHeaderLine('Expires', '')
                ->addHeaderLine('Cache-Control', 'private')
                ->addHeaderLine('Cache-Control', 'max-age=360000')
                ->addHeaderLine('Pragma', '')
                ->addHeaderLine('Content-type', 'image/' . $file->getExtension());
        $content = file_get_contents($file->getFullPath());
        $response->setContent($content);
        return $response;
      }
    }
    $response->setStatusCode(404);
    return $response;
  }

  /**
   * Send the requested file to the user.
   * 
   * @todo secure the images
   */
  public function downloadAction(){
    $id = $this->params('id');
    $response = $this->getResponse();

    if(!empty($id)){
      $file = $this->em->getRepository('\UnpCommon\Model\File')->findOneById($id);
      if($file && is_readable($file->getFullPath())){

        $response->getHeaders()
                ->addHeaderLine('Cache-Control: no-cache, must-revalidate')
                ->addHeaderLine('Expires: Sat, 26 Jul 1997 05:00:00 GMT')
                ->addHeaderLine('Content-Description: File Transfer')
                ->addHeaderLine('Content-Disposition: attachment; filename=' . $file->getFilename() . '"')
                ->addHeaderLine('Content-type: ' . $file->getMimeType())
                ->addHeaderLine('Content-Transfer-Encoding: binary');

        $content = file_get_contents($file->getFullPath());
        $response->setContent($content);
        return $response;
      }
    }
    $response->setStatusCode(404);
    return $response;
  }

  /**
   * Deletes the file specified by the id parameter. 
   * 
   * @todo port to zf2
   */
  public function deleteAction(){
    $id = $this->params('id');
    $response = $this->getResponse();
    
    if(!empty($id)){
      $file = $this->em->getRepository('\UnpCommon\Model\File')->findOneById($id);
      if($file && is_readable($file->getFullPath())){

        // remove file from file system
        $localPath = $file->getFullPath();
        $deleted = unlink($localPath);
        if($deleted){
        /*
          // set removed state in database record
          $file->remove();

          //@todo could be inefficient, but I got no better solution right now
          //remove file from all users
          $users = $this->em->getRepository('\UnpCommon\Model\User')->findAll();
          foreach($users as $user){
            $user->removeFile($file);
            $this->em->persist($user);
          }

          //remove file from all cases
          $cases = $this->em->getRepository('\UnpCommon\Model\PlagiarismCase')->findAll();
          foreach($cases as $case){
            $case->removeFile($file);
            $this->em->persist($case);
          }

          $user = $registry->user;
          $user->removeFile($file);
          $this->em->persist($user);

          //remove from public files
          $guestId = $registry->entitymanager->getRepository('Application_Model_Setting')->findOneBySettingKey('guest-id');
          $guest = $registry->entitymanager->getRepository('Application_Model_User')->findOneById($guestId->getValue());
          $guest->removeFile($file);
          $this->em->persist($guest);
         */

          $this->em->persist($file);
          $this->em->flush();
          $this->flashMessenger()->setNamespace('success')->addMessage($this->getTranslator()->translate(sprintf('The file %s was deleted successfully.', $file->getFilename())));
        }else{
          $this->flashMessenger()->setNamespace('error')->addMessage($this->getTranslator()->translate('We are sorry, but the file could not be deleted.'));
        }
      }else{
        $this->flashMessenger()->setNamespace('error')->addMessage($this->getTranslator()->translate('The file you specified does not exist.'));
      }
    }

    //$this->redirectToLastPage();
  }

}