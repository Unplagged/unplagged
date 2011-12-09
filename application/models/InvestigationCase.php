<?php
/**
 * File for class {@link InvestigationCase}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 * 
 * @Entity @Table(name="investigation_case")
 */
class Application_Model_InvestigationCase{

  /** @Id @GeneratedValue @Column(type="integer")  */
  private $id;

  /**
   * The "real" name of the case, under which it will get published later on.
   * 
   * @var string
   * 
   * @Column(type="string") 
   */
  private $name = '';

  /**
   * The alias is used to show everyone who doesn't have the permission to see the real case name.
   * 
   * @var string
   * 
   * @Column(type="string") 
   */
  private $alias = '';

  /**
   * @var string  
   */
  private $state = '';


  private $created;


  private $updated;

  public function __construct($name, $alias){
    $this->name = $name;
    $this->alias = $alias;
  }

  /**
   * Method auto-called when object is persisted to database for the first time.
   * 
   * @PrePersist
   */
  public function created(){
    $this->created = new DateTime("now");
  }

  /**
   * Method auto-called when object is updated in database.
   * 
   * @PrePersist
   */
  public function updated(){
    $this->updated = new DateTime("now");
  }
  
  /**
   * @return string 
   */
  public function getName(){
    return $this->name;
  }

  /**
   * @return string
   */
  public function getAlias(){
    return $this->alias;
  }

  /**
   * @return string
   */
  public function getState(){
    return $this->state;
  }

}

?>
