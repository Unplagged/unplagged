<?php

/**
 * File for class {@link ImageController}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class ImageController extends Zend_Controller_Action{

  public function init(){
    $this->_helper->viewRenderer->setNoRender(true);
    $this->_helper->layout()->disableLayout();
  }

  /**
   * Displays an image specified by the name parameter.
   */
  public function indexAction(){
    $imageName = $this->_getParam('name');

    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
    $fullPath = $path . $imageName;
    
    if(!empty($imageName) && file_exists($fullPath)){
      $imageInfo = getimagesize($fullPath);
      $this->getResponse()->setHeader('Content-Type', $imageInfo['mime']);
      readfile($fullPath);
    }else{
      $this->getResponse()->setHttpResponseCode(404);
    }
  }

}
?>
