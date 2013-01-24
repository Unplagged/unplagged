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
return array(
    array('name'=>'created', 'title'=>'created', 'description'=>'The element was created.'),
    array('name'=>'deleted', 'title'=>'deleted', 'description'=>'The element is deleted.'),
    array('name'=>'approved', 'title'=>'approved', 'description'=>'The amount of users required to approve the element was reached.'),
    array('name'=>'published', 'title'=>'published', 'description'=>'The element is published.'),
    array('name'=>'running', 'title'=>'running', 'description'=>'The task is currently being executed.'),
    array('name'=>'scheduled', 'title'=>'scheduled', 'description'=>'The task is scheduled.'),
    array('name'=>'parsed', 'title'=>'parsed', 'description'=>'The document was parsed.'),
    array('name'=>'error', 'title'=>'error', 'description'=>'There was an error during a process.'),
    array('name'=>'finished', 'title'=>'finished', 'description'=>'The task was finished.'),
    array('name'=>'completed', 'title'=>'completed', 'description'=>''),
    array('name'=>'locked', 'title'=>'locked', 'description'=>'An element was locked.'),
    array('name'=>'activated', 'title'=>'activated', 'description'=>'A user that can actually use the web page.'),
    array('name'=>'registered', 'title'=>'registered', 'description'=>'A user registered on the page and did not finish the verification process yet.'),
    array('name'=>'generated', 'title'=>'generated', 'description'=>'The report was generated successfully.')
);
