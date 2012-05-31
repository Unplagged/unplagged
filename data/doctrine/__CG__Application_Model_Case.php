<?php

namespace Proxies\__CG__;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Application_Model_Case extends \Application_Model_Case implements \Doctrine\ORM\Proxy\Proxy
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

    
    public function updated()
    {
        $this->__load();
        return parent::updated();
    }

    public function getName()
    {
        $this->__load();
        return parent::getName();
    }

    public function getAlias()
    {
        $this->__load();
        return parent::getAlias();
    }

    public function getPublishableName()
    {
        $this->__load();
        return parent::getPublishableName();
    }

    public function getState()
    {
        $this->__load();
        return parent::getState();
    }

    public function getUpdated()
    {
        $this->__load();
        return parent::getUpdated();
    }

    public function addCollaborator(\Application_Model_User $user)
    {
        $this->__load();
        return parent::addCollaborator($user);
    }

    public function removeCollaborator(\Application_Model_User $user)
    {
        $this->__load();
        return parent::removeCollaborator($user);
    }

    public function getCollaborators()
    {
        $this->__load();
        return parent::getCollaborators();
    }

    public function clearCollaborators()
    {
        $this->__load();
        return parent::clearCollaborators();
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

    public function getAbbreviation()
    {
        $this->__load();
        return parent::getAbbreviation();
    }

    public function setName($name)
    {
        $this->__load();
        return parent::setName($name);
    }

    public function setAlias($alias)
    {
        $this->__load();
        return parent::setAlias($alias);
    }

    public function setAbbreviation($abbreviation)
    {
        $this->__load();
        return parent::setAbbreviation($abbreviation);
    }

    public function getRoles()
    {
        $this->__load();
        return parent::getRoles();
    }

    public function getPlagiarismPercentage()
    {
        $this->__load();
        return parent::getPlagiarismPercentage();
    }

    public function addDefaultRole(\Application_Model_User_InheritableRole $role)
    {
        $this->__load();
        return parent::addDefaultRole($role);
    }

    public function getDefaultRoles()
    {
        $this->__load();
        return parent::getDefaultRoles();
    }

    public function getBarcode($width, $height, $barHeight, $showLabels, $widthUnit)
    {
        $this->__load();
        return parent::getBarcode($width, $height, $barHeight, $showLabels, $widthUnit);
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

    public function geTags()
    {
        $this->__load();
        return parent::geTags();
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
        return array('__isInitialized__', 'id', 'created', 'comments', 'tags', 'ratings', 'notifications', 'name', 'alias', 'abbreviation', 'updated', 'documents', 'files', 'collaborators', 'defaultRoles');
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