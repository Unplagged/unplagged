<?php

/**
 * The class represents a single tag used to categorize an article.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <benjamin.oertel@me.com>
 * @version 1.0
 * 
 * @Entity
 * @Table(name="tags")
 */
class Application_Model_Tag
{
	/**
	 * The tag id that is a unique identifier for the tag.
	 * @var integer The tag id.
	 * @access private
	 * 
	 * @Id @Column(type="integer")
	 * @GeneratedValue
	 */
    private $id;
	
	/** 
	 * The title.
	 * @var string The title.
	 * @access private
	 * 
	 * @Column(type="string", length=45)
	 */
	private $title;

	/**
	 * Constructor.
	 */
    public function __construct() {
    }
    
	/**
	 * Gets the tag id.
	 * @return int Returns the tag id.
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Gets the title.
	 * @return int Returns the title.
	 */
	public function getTitle() {
		return $this->title;
	}
	
	
	/**
	 * Sets the tag title.
	 * @param string $title The tag title.
	 */
	public function setTitle($title) {
		$this->title = $title;
	}
}
