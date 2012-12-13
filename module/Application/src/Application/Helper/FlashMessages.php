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
namespace Application\Helper;

use Zend\View\Helper\AbstractHelper;
use \Zend\Mvc\Controller\Plugin\FlashMessenger;

/**
 * View helper that can be used to print the collected messages to the user.
 * 
 * @see http://stackoverflow.com/questions/12524817/flash-messanger-in-zf2
 */
class FlashMessages extends AbstractHelper{

  private $flashMessenger;

  public function setFlashMessenger(FlashMessenger $flashMessenger){
    $this->flashMessenger = $flashMessenger;
  }

  public function __invoke(){
    //levels based on bootstrap styles
    $namespaces = array('default'=>'info', 'error'=>'error', 'success'=>'success', 'info'=>'info', 'warning'=>'warning');

    $messageString = '';

    foreach($namespaces as $namespace => $class){
      $this->flashMessenger->setNamespace($namespace);

      $messages = array_merge($this->flashMessenger->getMessages(), $this->flashMessenger->getCurrentMessages());
      $uniqueMessages = array_unique($messages);
      
      foreach($uniqueMessages as $message){
        $messageString .= '<div class="alert fade in alert-' . $class . '">' . $message . '</div>';
      }
    }

    return $messageString;
  }
}