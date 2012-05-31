<?php

namespace Proxies\__CG__;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Application_Model_User extends \Application_Model_User implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function getUpdated()
    {
        $this->__load();
        return parent::getUpdated();
    }

    public function updated()
    {
        $this->__load();
        return parent::updated();
    }

    public function getUsername()
    {
        $this->__load();
        return parent::getUsername();
    }

    public function getFirstname()
    {
        $this->__load();
        return parent::getFirstname();
    }

    public function setFirstname($firstname)
    {
        $this->__load();
        return parent::setFirstname($firstname);
    }

    public function getLastname()
    {
        $this->__load();
        return parent::getLastname();
    }

    public function setLastname($lastname)
    {
        $this->__load();
        return parent::setLastname($lastname);
    }

    public function getPassword()
    {
        $this->__load();
        return parent::getPassword();
    }

    public function setPassword($password)
    {
        $this->__load();
        return parent::setPassword($password);
    }

    public function getEmail()
    {
        $this->__load();
        return parent::getEmail();
    }

    public function getVerificationHash()
    {
        $this->__load();
        return parent::getVerificationHash();
    }

    public function setVerificationHash($verificationHash)
    {
        $this->__load();
        return parent::setVerificationHash($verificationHash);
    }

    public function getState()
    {
        $this->__load();
        return parent::getState();
    }

    public function setState($state)
    {
        $this->__load();
        return parent::setState($state);
    }

    public function getCurrentCase()
    {
        $this->__load();
        return parent::getCurrentCase();
    }

    public function setCurrentCase(\Application_Model_Case $currentCase = NULL)
    {
        $this->__load();
        return parent::setCurrentCase($currentCase);
    }

    public function addFile(\Application_Model_File $file)
    {
        $this->__load();
        return parent::addFile($file);
    }

    public function removeFile(\Application_Model_File $file)
    {
        $this->__load();
        return parent::removeFile($file);
    }

    public function getFiles()
    {
        $this->__load();
        return parent::getFiles();
    }

    public function clearFiles()
    {
        $this->__load();
        return parent::clearFiles();
    }

    public function hasFiles()
    {
        $this->__load();
        return parent::hasFiles();
    }

    public function hasFile(\Application_Model_File $file)
    {
        $this->__load();
        return parent::hasFile($file);
    }

    public function getAvatar()
    {
        $this->__load();
        return parent::getAvatar();
    }

    public function getDirectName()
    {
        $this->__load();
        return parent::getDirectName();
    }

    public function getDirectLink()
    {
        $this->__load();
        return parent::getDirectLink();
    }

    public function toArray()
    {
        $this->__load();
        return parent::toArray();
    }

    public function getRole()
    {
        $this->__load();
        return parent::getRole();
    }

    public function getSettings()
    {
        $this->__load();
        return parent::getSettings();
    }

    public function getId()
    {
        $this->__load();
        return parent::getId();
    }

    public function setId($id)
    {
        $this->__load();
        return parent::setId($id);
    }

    public function created()
    {
        $this->__load();
        return parent::created();
    }

    public function getIconClass()
    {
        $this->__load();
        return parent::getIconClass();
    }

    public function getComments()
    {
        $this->__load();
        return parent::getComments();
    }

    public function getRatings()
    {
        $this->__load();
        return parent::getRatings();
    }

    public function getTags()
    {
        $this->__load();
        return parent::getTags();
    }

    public function getCreated()
    {
        $this->__load();
        return parent::getCreated();
    }

    public function getConversationTypes()
    {
        $this->__load();
        return parent::getConversationTypes();
    }

    public function isRatedByUser($user)
    {
        $this->__load();
        return parent::isRatedByUser($user);
    }

    public function getTagIds()
    {
        $this->__load();
        return parent::getTagIds();
    }

    public function addTag(\Application_Model_Tag $tag)
    {
        $this->__load();
        return parent::addTag($tag);
    }

    public function removeTag(\Application_Model_Tag $tag)
    {
        $this->__load();
        return parent::removeTag($tag);
    }

    public function setTags($tagIds = array (
))
    {
        $this->__load();
        return parent::setTags($tagIds);
    }

    public function clearTags()
    {
        $this->__load();
        return parent::clearTags();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'created', 'comments', 'tags', 'ratings', 'notifications', 'updated', 'username', 'encryptedPassword', 'email', 'firstname', 'lastname', 'verificationHash', 'state', 'role', 'avatar', 'currentCase', 'files', 'settings');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields AS $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}