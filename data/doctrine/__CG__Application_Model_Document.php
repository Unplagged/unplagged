<?php

namespace Proxies\__CG__;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Application_Model_Document extends \Application_Model_Document implements \Doctrine\ORM\Proxy\Proxy
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

    
    public function getId()
    {
        $this->__load();
        return parent::getId();
    }

    public function getTitle()
    {
        $this->__load();
        return parent::getTitle();
    }

    public function getBibTex()
    {
        $this->__load();
        return parent::getBibTex();
    }

    public function getPages()
    {
        $this->__load();
        return parent::getPages();
    }

    public function addPage(\Application_Model_Document_Page $page)
    {
        $this->__load();
        return parent::addPage($page);
    }

    public function getFragments()
    {
        $this->__load();
        return parent::getFragments();
    }

    public function addFragment(\Application_Model_Document_Fragment $fragment)
    {
        $this->__load();
        return parent::addFragment($fragment);
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

    public function setTitle($title)
    {
        $this->__load();
        return parent::setTitle($title);
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

    public function toArray()
    {
        $this->__load();
        return parent::toArray();
    }

    public function getPlagiarismPercentage()
    {
        $this->__load();
        return parent::getPlagiarismPercentage();
    }

    public function setCase($case)
    {
        $this->__load();
        return parent::setCase($case);
    }

    public function getInitialFile()
    {
        $this->__load();
        return parent::getInitialFile();
    }

    public function setBibTexForm($form)
    {
        $this->__load();
        return parent::setBibTexForm($form);
    }

    public function setBibTexKuerzel($kuerzel)
    {
        $this->__load();
        return parent::setBibTexKuerzel($kuerzel);
    }

    public function setBibTexAutor($autor)
    {
        $this->__load();
        return parent::setBibTexAutor($autor);
    }

    public function setBibTexTitel($titel)
    {
        $this->__load();
        return parent::setBibTexTitel($titel);
    }

    public function setBibTexZeitschrift($zeitschrift)
    {
        $this->__load();
        return parent::setBibTexZeitschrift($zeitschrift);
    }

    public function setBibTexSammlung($sammlung)
    {
        $this->__load();
        return parent::setBibTexSammlung($sammlung);
    }

    public function setBibTexHrsg($hrsg)
    {
        $this->__load();
        return parent::setBibTexHrsg($hrsg);
    }

    public function setBibTexBeteiligte($beteiligte)
    {
        $this->__load();
        return parent::setBibTexBeteiligte($beteiligte);
    }

    public function setBibTexOrt($ort)
    {
        $this->__load();
        return parent::setBibTexOrt($ort);
    }

    public function setBibTexVerlag($verlag)
    {
        $this->__load();
        return parent::setBibTexVerlag($verlag);
    }

    public function setBibTexAusgabe($ausgabe)
    {
        $this->__load();
        return parent::setBibTexAusgabe($ausgabe);
    }

    public function setBibTexJahr($jahr)
    {
        $this->__load();
        return parent::setBibTexJahr($jahr);
    }

    public function setBibTexMonat($monat)
    {
        $this->__load();
        return parent::setBibTexMonat($monat);
    }

    public function setBibTexTag($tag)
    {
        $this->__load();
        return parent::setBibTexTag($tag);
    }

    public function setBibTexNummer($nummer)
    {
        $this->__load();
        return parent::setBibTexNummer($nummer);
    }

    public function setBibTexSeiten($seiten)
    {
        $this->__load();
        return parent::setBibTexSeiten($seiten);
    }

    public function setBibTexUmfang($umfang)
    {
        $this->__load();
        return parent::setBibTexUmfang($umfang);
    }

    public function setBibTexReihe($reihe)
    {
        $this->__load();
        return parent::setBibTexReihe($reihe);
    }

    public function setBibTexAnmerkung($anmerkung)
    {
        $this->__load();
        return parent::setBibTexAnmerkung($anmerkung);
    }

    public function setBibTexIsbn($isbn)
    {
        $this->__load();
        return parent::setBibTexIsbn($isbn);
    }

    public function setBibTexIssn($issn)
    {
        $this->__load();
        return parent::setBibTexIssn($issn);
    }

    public function setBibTexDoi($doi)
    {
        $this->__load();
        return parent::setBibTexDoi($doi);
    }

    public function setBibTexUrl($url)
    {
        $this->__load();
        return parent::setBibTexUrl($url);
    }

    public function setBibTexUrn($urn)
    {
        $this->__load();
        return parent::setBibTexUrn($urn);
    }

    public function setBibTexWp($wp)
    {
        $this->__load();
        return parent::setBibTexWp($wp);
    }

    public function setBibTexInlit($inlit)
    {
        $this->__load();
        return parent::setBibTexInlit($inlit);
    }

    public function setBibTexInfn($infn)
    {
        $this->__load();
        return parent::setBibTexInfn($infn);
    }

    public function setBibTexSchluessel($schluessel)
    {
        $this->__load();
        return parent::setBibTexSchluessel($schluessel);
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
        return array('__isInitialized__', 'id', 'created', 'comments', 'tags', 'ratings', 'notifications', 'title', 'bibTex', 'pages', 'fragments', 'state', 'case', 'initialFile');
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