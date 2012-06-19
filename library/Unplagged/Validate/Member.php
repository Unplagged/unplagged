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
 * The class validates a form element that auto-completes usernames.
 * It allows to add collaborators and reviewers to an article.
 *
 * @author Benjamin Oertel <benjamin.oertel@student.htw-berlin.de>
 * @version 1.0
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

class Zend_Validate_Member extends Zend_Validate_Abstract
{

	const AT_LEAST = 'atLeast';

  
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::AT_LEAST => "Geben Sie mindestens %min% Personen an."
    );
    
    protected $_messageVariables = array(
        'min' => '_min'
    );
    
    /**
     * Minimum users needed.
     *
     * @var integer
     */
    protected $_min;
    
    /**
     * The name of the element in the form.
     *
     * @var string The element name
     */
    protected $_name;
    
    /**
     * Names of other elements that selected users are not allowed in this element.
     * For example users already selected as collaborators can't be reviewers. In this
     * case $_skipUsersFrom would contain reviewers
     *
     * @var array All elements that users are not allowed to be selected in this element.
     */
    protected $_skipUsersFrom;

    /**
	 * Sets the number of minimum required entries.
	 * @param integer $min The minimum amount of entries.
	 */
    public function setMin($min)
    {
    	$this->_min = $min;
    	return $this;
    }
    
	/**
	 * Sets the element name.
	 * @param string $name The name.
	 */
    public function setName($name)
    {
    	$this->_name = $name;
    	return $this;
    }

    /**
	 * Sets the element thats users have to be forbidden in here.
	 * @param array $skipUsersFrom The names of all forbidden elements.
	 */
    public function setSkipUsersFrom($skipUsersFrom)
    {
    	$this->_skipUsersFrom = $skipUsersFrom;
    	return $this;
    }

    /**
     * Constructor
     */
    public function __construct($options = array())
    {
    	if(!empty($options["min"]))
    	{
    		$this->setMin($options["min"]);
    	}

    	if(!empty($options["name"]))
    	{
    		$this->setName($options["name"]);	
    	}
        
    	if(!empty($options["skipUsersFrom"]))
    	{
    		$this->setSkipUsersFrom($options["skipUsersFrom"]);
    	}
    }
    
    /**
     * Check if the input in the element is valid, when form is submitted
     * @see Zend_Validate_Interface::isValid()
     */
    public function isValid($value)
    {
    	// check all users in the arrays that are forbidden for this element to be selected
    	$forbiddenUserIds = array();
    	if(!empty($this->_skipUsersFrom))
    	{
    		foreach($this->_skipUsersFrom as $value)
    		{    			
    			foreach($_POST[$value] as $key => $uid)
    			{
					$forbiddenUserIds[] = preg_replace('/[^0-9]/', '', $uid);
    			}
    		}
    		$forbiddenUserIds = array_unique($forbiddenUserIds);
    	}

    	// remove duplicate values
    	$_POST[$this->_name] = array_unique($_POST[$this->_name]);
    	foreach($_POST[$this->_name] as $key => $uid)
    	{
    		$uid = preg_replace('/[^0-9]/', '', $uid);
    		if(in_array($uid, $forbiddenUserIds))
    		{
    			unset($_POST[$this->_name][$key]);
    		}
    	}
    	
    	$length = count($_POST[$this->_name]);
    	if ($length < $this->_min) {
        	$this->_error(self::AT_LEAST);
        	return false;
    	}
       return true;
    }

}
