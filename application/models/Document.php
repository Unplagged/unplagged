<?php
/**
 * File for class {@link Document}.
 */

include_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'models/Document.php';

/**
 * @Entity @Table(name="documents")
 */
class Document{

  /** @Id @GeneratedValue @Column(type="integer")  */
  private $id;

  public function getId(){
    return $this->id;
  }
}
?> 