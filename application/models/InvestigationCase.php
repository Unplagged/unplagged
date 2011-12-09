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
   *
   * @var string  
   */
  private $state = '';

  /**
   * @Column(type="datetime")
   */
  private $created;

  /**
   * @Column(type="datetime")
   */
  private $updated;

  public function __construct($name, $alias){
    $this->name = $name;
    $this->alias = $alias;
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
