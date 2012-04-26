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
 * 
 * @author Unplagged
 * 
 * @Entity
 */
class Application_Model_User_GuestRole extends Application_Model_User_Role{

  public function getRoleId(){
    return 'guest';
  }

  public function getPermissions(){
    return array(
      'index',
      'error',
      'case',
      'activity_stream_public',
      'files_view_public',
      'user_register'
    );  
  }
}
?>
