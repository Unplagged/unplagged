<?php

/**
 * The class represents a user with the rank of an author.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 * 
 * @Entity
 * @Table(name="users")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="rank", type="string")
 * @DiscriminatorMap({"author" = "Application_Model_User", "reviewer" = "Application_Model_Reviewer", "superadmin" = "Application_Model_Superadmin"})
 */
class Application_Model_User
{
	/**
	 * The user id that is a unique identifier for the user.
	 * @var integer The user id.
	 * @access private
	 * 
	 * @Id @Column(type="integer")
	 * @GeneratedValue
	 */
	private $id;
	/** 
	 * The login service the user uses, e.g. facebook.
	 * @var string The users login service.
	 * @access private
	 * 
	 * @Column(type="string", length=45)
	 */
	private $log_service;
	/** 
	 * The userid at login service the user uses.
	 * @var string The userid at login service the user uses.
	 * @access private
	 * 
	 * @Column(type="string", length=100)
	 */
	private $log_uuid;
	/**
	 * The users email address.
	 * @var string The users users email address.
	 * @access private
	 * 
	 * @Column(type="string", length=128)
	 */
	private $email;
	/**
	 * The users first name.
	 * @var string The users first name.
	 * @access private
	 * 
	 * @Column(type="string", length=45)
	 */
	private $first_name;
	/** 
	 * The users last name.
	 * @var string The users last name.
	 * @access private
	 * 
	 * @Column(type="string", length=45)
	 */
	private $last_name;
	/** 
	 * The first login date of the user, date of registration.
	 * @var DateTime The users first login date.
	 * @access private
	 * 
	 * @Column(type="datetime", nullable=true)
	 */
	private $first_login;
	/**
	 * The last login date of the user.
	 * @var DateTime The users last login date.
	 * @access private
	 * 
	 * @Column(type="datetime", nullable=true)
	 */
	private $last_login;
	/** 
	 * The currently user state, can be activated or locked,.
	 * @var string The users current state.
	 * @access private
	 * 
	 * @Column(type="string", length=25) 
	 */
	private $state;
	/**
	 * The university department the user is working at.
	 * @var string The users university department.
	 * @access private
	 * 
	 * @Column(type="string", length=45)
	 */
	private $edu_university;
	/**
	 * The university department the user is working at.
	 * @var string The users university department.
	 * @access private
	 * 
	 * @Column(type="string", length=45)
	 */
	private $edu_department;
	/** 
	 * The position of the user at the department.
	 * @var string User position in his department.
	 * @access private
	 * 
	 * @Column(type="string", length=45)
	 */
	private $edu_position;
	/**
	 * Defining if the user can currently request rights, after a started requests, this flag is disabled.
	 * @var boolean If the user can currently request rights.
	 * @access private
	 * 
	 * @Column(type="boolean")
	 */
	private $can_request_rights;
	/** 
	 * A collection of all articles the user ever was a collaborator of.
	 * @var collection The articles the user worked on.
	 * @access private
	 * 
	 * @OneToMany(targetEntity="Application_Model_Article", mappedBy="collaborators")
	 */
    private $articles;

	/**
	 * Constructor.
	 */
	public function __construct()
    {
    	$this->articles = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
	/**
	 * Gets the users id.
	 * @return int Returns the users id.
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Gets the users id in the login service used, e.g. users facebook id.
	 * @return string Returns the users login service id.
	 */
	public function getLogUuid() {
		return $this->log_uuid;
	}
	
	/**
	 * Gets the login service the user uses.
	 * @return string Returns the users login service.
	 */
    public function getLogService() {
		return $this->log_service;
	}
	
	/**
	 * Gets the email address.
	 * @return string Returns the email address.
	 */
    public function getEmail() {
		return $this->email;
	}
	
	/**
	 * Gets the firstname.
	 * @return string Returns the firstname.
	 */	
	public function getFirstName() {
		return $this->first_name;
	}

	/**
	 * Gets the lastname.
	 * @return string Returns the lastname.
	 */
	public function getLastName() {
		return $this->last_name;
	}
	
	/**
	 * Gets the firstname and lastname.
	 * @return string Returns the full name.
	 */
	public function getFullName() {
		return $this->first_name . " " . $this->last_name;
	}

	/**
	 * Gets the first login date.
	 * @return DateTime Returns the first login date.
	 */
	public function getDateFirstLogin() {
		return $this->date_first_login;
	}
	
	/**
	 * Gets the last login date.
	 * @return DateTime Returns the last login date.
	 */
	public function getDateLastLogin() {
        return $this->date_last_login;
	}
	
	/**
	 * Gets the current user state. Can be locked or activated.
	 * @return string Returns the current user state.
	 */
	public function getState() {
		return $this->state;
	}

	/**
	 * Gets the the university the user is working at.
	 * @return string Returns the current university the user is working at.
	 */
	public function getEduUniversity() {
		return $this->edu_university;
	}
	
	/**
	 * Gets the the department the user is working at.
	 * @return string Returns the department at the university.
	 */	
	public function getEduDepartment() {
		return $this->edu_department;
	}
	
	/**
	 * Gets the the position of the user department he is working at.
	 * @return string Returns the position at the department.
	 */
	public function getEduPosition() {
		return $this->edu_position;
	}

	/**
	 * Gets the state if the user cann currently request rights or not.
	 * @return boolean Returns if the user is allowed to request rights or not.
	 */	
	public function getCanRequestRights() {
		return $this->can_request_rights;
	}

	
	/**
	 * Sets the users id used in the login service , e.g. users facebook id.
	 * @param string $log_uuid The users id in the login service.
	 */
    public function setLogUuid($log_uuid) {
		$this->log_uuid = $log_uuid;
	}

	/**
	 * Sets the users login service.
	 * @param string $log_service The login service the user uses.
	 */
    public function setLogService($log_service) {
		$this->log_service = $log_service;
	}

	/**
	 * Sets the users email address.
	 * @param string $email The users email address.
	 * @return void
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * Sets the users firstname.
	 * @param string $first_name The users firstname.
	 */
	public function setFirstName($first_name) {
		$this->first_name = $first_name;
	}

	/**
	 * Sets the users lastname.
	 * @param string $last_name The users lastname.
	 */
	public function setLastName($last_name) {
		$this->last_name = $last_name;
	}

	/**
	 * Sets the date the user was logged in for the first time.
	 * @param DateTime $date_first_login The users first login date.
	 */
	public function setDateFirstLogin($date_first_login) {
		$this->date_first_login = $date_first_login;
	}

	/**
	 * Sets the date the user was logged in for the last time.
	 * @param DateTime $date_last_login The users last login date.
	 */
	public function setDateLastLogin($date_last_login) {
		$this->date_last_login = $date_last_login;
	}

	/**
	 * Sets the users state.
	 * @param string $state The current state.
	 */
	public function setState($state) {
		$this->state = $state;
	}

	/**
	 * Sets the users university he is working at.
	 * @param string $edu_university The university user is working at.
	 */
	public function setEduUniversity($edu_university) {
		$this->edu_university = $edu_university;
	}
	
	/**
	 * Sets the users department at the university he is working at.
	 * @param string $edu_department The department at the university.
	 */	
	public function setEduDepartment($edu_department) {
		$this->edu_department = $edu_department;
	}

	/**
	 * Sets the users position at the department he is working at.
	 * @param string $edu_postion The users position at the department.
	 */
	public function setEduPosition($edu_postion) {
		$this->edu_position = $edu_postion;
	}	
	
	/**
	 * Sets the users of requesting rights.
	 * @param boolean $can_request_rights The user can request rights or not.
	 */
	public function setCanRequestRights($can_request_rights) {
		$this->can_request_rights = $can_request_rights;
	}	
    
	
    /**
     * Adds an article to the user where the user is a collaborator of.
     * @param Application_Model_Article $article The article the user is a collaborator of.
     */
	public function addArticle(Application_Model_Article $article)
    {
        $this->articles[] = $article;
    }
}

