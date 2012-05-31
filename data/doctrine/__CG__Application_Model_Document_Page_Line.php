<?php

namespace Proxies\__CG__;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Application_Model_Document_Page_Line extends \Application_Model_Document_Page_Line implements \Doctrine\ORM\Proxy\Proxy
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

    
    public function toArray()
    {
        $this->__load();
        return parent::toArray();
    }

    public function getId()
    {
        $this->__load();
        return parent::getId();
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

    public function getIconClass()
    {
        $this->__load();
        return parent::getIconClass();
    }

    public function getLineNumber()
    {
        $this->__load();
        return parent::getLineNumber();
    }

    public function setLineNumber($lineNumber)
    {
        $this->__load();
        return parent::setLineNumber($lineNumber);
    }

    public function getContent()
    {
        $this->__load();
        return parent::getContent();
    }

    public function setContent($content)
    {
        $this->__load();
        return parent::setContent($content);
    }

    public function setPage($page)
    {
        $this->__load();
        return parent::setPage($page);
    }

    public function getPage()
    {
        $this->__load();
        return parent::getPage();
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

<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 9eb8f683e6c9d3134ce7765b3d130fbd50020a83
    public function geTags()
    {
        $this->__load();
        return parent::geTags();
<<<<<<< HEAD
=======
=======
=======
>>>>>>> 772c05a181f258229757fd41f98debe87fda193b
    public function getTags()
    {
        $this->__load();
        return parent::getTags();
<<<<<<< HEAD
>>>>>>> tien
>>>>>>> 9eb8f683e6c9d3134ce7765b3d130fbd50020a83
=======
>>>>>>> 772c05a181f258229757fd41f98debe87fda193b
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

<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 9eb8f683e6c9d3134ce7765b3d130fbd50020a83
    public function getTags()
    {
        $this->__load();
        return parent::getTags();
<<<<<<< HEAD
=======
=======
    public function geTags()
    {
        $this->__load();
        return parent::geTags();
>>>>>>> tien
>>>>>>> 9eb8f683e6c9d3134ce7765b3d130fbd50020a83
    }

=======
>>>>>>> 772c05a181f258229757fd41f98debe87fda193b
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
        return array('__isInitialized__', 'id', 'created', 'comments', 'tags', 'ratings', 'notifications', 'lineNumber', 'content', 'page');
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