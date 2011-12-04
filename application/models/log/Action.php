<?php
/** 
 * The class represents a log action.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 * 
 * @Entity 
 * @Table(name="log_actions")
 */
 class Application_Model_Log_Action {
    /**
     * The logActionId is an unique identifier for each log action.
     * @var string The log action id.
     * $access protected
     * 
     * @Id @GeneratedValue @Column(type="integer")
     */
    protected $id;
    /** 
     * The module for the log acction.
     * @var string The module.
     * @access protected
     * 
     * @Column(type="string", length=32)
     */
    protected $module;
    /** 
     * A title for the log acction.
     * @var string The usernamee.
     * @access protected
     * 
     * @Column(type="string", unique="true", length=32)
     */
    protected $title;
    /** 
     * A description for the log action.
     * @var string The description.
     * @access protected
     * 
     * @Column(type="string", length=256)
     */
    protected $description;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }
    
    public function getModule() {
        return $this->module;
    }

    public function setModule($module) {
        $this->module = $module;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }


}