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
 * 
 */
class SimtextController extends Unplagged_Controller_Action{

  public function ajaxAction(){
    $input = new Zend_Filter_Input(array('left'=>'StripTags', 'right' => 'StripTags'), null, $this->_getAllParams());

    $this->view->layout()->disableLayout();
    $this->_helper->viewRenderer->setNoRender();

    echo Unplagged_CompareText::compare($input->left, $input->right, 4);
  }

}

?>
