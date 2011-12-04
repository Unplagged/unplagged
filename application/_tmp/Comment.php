<?php
//entities/Comment.php
/** 
  * @Entity @Table(name="comments")
 */
 class Comment
 {
	/**
	  * @Id @GeneratedValue @Column(type="integer")
	  * @var string
	*/
	protected $commentid;
 }
?> 