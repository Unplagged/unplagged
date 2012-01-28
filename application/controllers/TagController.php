<?php

/*
 * Controller for user management.
 */

/**
 * The controller class handles all the user transactions as rights requests and user management.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 */
class TagController extends Zend_Controller_Action{

  /**
   * Initalizes registry and namespace instance in the controller and allows to display flash messages in the view.
   * @see Zend_Controller_Action::init()
   */
  public function init(){
    $this->_em = Zend_Registry::getInstance()->entitymanager;
    $this->_defaultNamespace = new Zend_Session_Namespace('Default');
    $this->view->flashMessages = $this->_helper->flashMessenger->getMessages();
  }

  public function indexAction(){
    
  }
  
  /**
	 * Selects 5 users based on matching first and lastname with the search string and sends their ids as json string back.
	 * @param String from If defined it selects only users of a specific rank.
	 */
	public function autocompleteTitlesAction()
	{
		$search_string = $this->_getParam('term');
		// ids to skip
		$skipIds = $this->_getParam('skip');
				
		// no self select possible
		if(substr($skipIds, 0, 1) == ",")
		{
			$skipIds = substr($skipIds, 1);
		}
		
		if($skipIds != "")
		{
			$skipIds = " AND t.id NOT IN (" . $skipIds . ")";
		}
		 
		$qb = $this->_em->createQueryBuilder();
		$qb->add('select', 	"t.title as label, t.id AS value")
		->add('from', 	'Application_Model_Tag t')
		->where("t.title LIKE '%" . $search_string . "%' " . $skipIds);
		$qb->setMaxResults(5);

		$dbresults = $qb->getQuery()->getResult();

		foreach ($dbresults as $key => $value)
		{
      $results[] = $value;
    }
		$this->_helper->json($results);
	}

}
