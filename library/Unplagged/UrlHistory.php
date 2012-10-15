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
 * This class can be registered in the Bootstrap and then stores the current
 * URI in the session to make it possible to redirect the user back to the
 * last page that was visited.
 *
 * This can for example be necessary during the case selection in order to
 * stay on the same page, but with different data.
 *
 * @author Unplagged
 */
class Unplagged_UrlHistory extends Zend_Controller_Plugin_Abstract{

  public function postDispatch(Zend_Controller_Request_Abstract $request){
    if(!$request->isXmlHttpRequest()){
      if(!($request->getControllerName() == 'permission' && $request->getActionName() == 'edit')){
        $historySessionNamespace = new Zend_Session_Namespace('history');
        $historySessionNamespace->last = $request->getRequestUri();
      }
    }
  }

}