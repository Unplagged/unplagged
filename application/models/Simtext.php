<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Application_Model_Simtext 
{
	$protected $sim_id;
	$protected $first_doc_id;
	$protected $sec_doc_id;
	$protected $created_date;
	$protected $sim_name;
	
	
	public function __set($name,$value);
	
	
	public function __get($name);
	
	
    
}
class Application_Model_Simtext_Mapper
{
	public function save(Application_Model_Simtext $simtext);
	public function find($id);
	public function fetchAll();
}
?>
