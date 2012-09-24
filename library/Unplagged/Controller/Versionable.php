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
 * This class bundles common functions used by controllers having a changelog action.
 * 
 * @author Benjamin Oertel
 */
abstract class Unplagged_Controller_Versionable extends Unplagged_Controller_Action{

  public function init(){
    parent::init();
  }

  public function changelogAction(){
    $input = new Zend_Filter_Input(array('id'=>'Digits'), null, $this->_getAllParams());

    $query = $this->_em->createQuery("SELECT v FROM Application_Model_Versionable_Version v WHERE v.versionable = :versionable");
    $query->setParameter("versionable", $input->id);
    $versions = $query->getResult();

    $params["versions"] = array();
    foreach($versions as $version){
      $params["versions"][$version->getId()] = "Version " . $version->getVersion();
    }
    $params["action"] = "/" . $this->getRequest()->getControllerName() . "/changelog/id/" . $input->id;

    // create the form
    $diffVersionsForm = new Application_Form_Versionable_Diff($params);

    // form has been submitted through post request
    if($this->_request->isPost()){
      $formData = $this->_request->getPost();

      // if the form doesn't validate, pass to view and return
      if($diffVersionsForm->isValid($formData)){
        $firstVersionId = $this->getRequest()->getParam('firstVersion');
        $secondVersionId = $this->getRequest()->getParam('secondVersion');

        $firstVersion = $this->_em->getRepository('Application_Model_Versionable_Version')->findOneById($firstVersionId);
        $secondVersion = $this->_em->getRepository('Application_Model_Versionable_Version')->findOneById($secondVersionId);

        // @todo, just to have some data for now
        $firstData = $firstVersion->getData();
        $secondData = $secondVersion->getData();

        if(!empty($firstData) && !empty($secondData)){
          // @todo remove, jsut for now to have something
          $a = str_split(json_encode($firstData), 20);
          $b = str_split(json_encode($secondData), 20);

          // options for generating the diff
          $options = array(
            'ignoreWhitespace'=>true,
            'ignoreCase'=>true,
            'context'=>1000
          );

          $diff = new Diff($a, $b, $options);
          $renderer = new Diff_Renderer_Html_Array();
        }

        if(!empty($diff)){
          $this->view->diff = Unplagged_Helper::formatDiff($diff->Render($renderer), $firstVersion->getVersion(), $secondVersion->getVersion());
        }
      }
    }

    $this->view->diffVersionsForm = $diffVersionsForm;
    $this->_helper->viewRenderer->renderBySpec('changelog', array('controller'=>'versionable'));
  }

}

?>
