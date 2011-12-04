<?php
/** 
 * The class represents a log entry.
 * It defines also the structure of the database table for the ORM.
 *
 * @author Benjamin Oertel <mail@benjaminoertel.com>
 * @version 1.0
 */
 class Unplagged_Dao_Log { 
    /**
     * Create a new entry in the log table.
     * 
     * @string $module The module.
     * @string $title The title.
     * @integer $userId The users id.
     * 
     */
    public static function log($module, $title, &$user, $comment = null) {
        $em = Zend_Registry::getInstance()->entitymanager;
        
        $log = new Application_Model_Log();
        $action = $em->getRepository('Application_Model_Log_Action')->findOneBy(array('title' => $title, 'module' => $module));
        $log->setAction($action);
        $log->setUser($user);
        $log->setComment($comment);
        
        $em->persist($log);
        $em->flush();
    }



}