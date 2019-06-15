<?php

namespace DoctrineProxies\__CG__\App\Entities;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Conflict extends \App\Entities\Conflict implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = [];



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', 'id', 'latitude', 'longitude', 'company_name', 'date_from', 'date_to', 'conflictReason', 'conflictResult', 'industry', 'region', 'events', 'parentEvent', 'title_ru', 'title_en', 'title_es', 'createdAt', 'updatedAt'];
        }

        return ['__isInitialized__', 'id', 'latitude', 'longitude', 'company_name', 'date_from', 'date_to', 'conflictReason', 'conflictResult', 'industry', 'region', 'events', 'parentEvent', 'title_ru', 'title_en', 'title_es', 'createdAt', 'updatedAt'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Conflict $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', []);

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', [$id]);

        parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getLatitude()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLatitude', []);

        return parent::getLatitude();
    }

    /**
     * {@inheritDoc}
     */
    public function setLatitude(float $latitude): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLatitude', [$latitude]);

        parent::setLatitude($latitude);
    }

    /**
     * {@inheritDoc}
     */
    public function getLongitude()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLongitude', []);

        return parent::getLongitude();
    }

    /**
     * {@inheritDoc}
     */
    public function setLongitude(float $longitude): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLongitude', [$longitude]);

        parent::setLongitude($longitude);
    }

    /**
     * {@inheritDoc}
     */
    public function getCompanyName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCompanyName', []);

        return parent::getCompanyName();
    }

    /**
     * {@inheritDoc}
     */
    public function setCompanyName($company_name): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCompanyName', [$company_name]);

        parent::setCompanyName($company_name);
    }

    /**
     * {@inheritDoc}
     */
    public function getDateFrom()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDateFrom', []);

        return parent::getDateFrom();
    }

    /**
     * {@inheritDoc}
     */
    public function setDateFrom($date_from): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDateFrom', [$date_from]);

        parent::setDateFrom($date_from);
    }

    /**
     * {@inheritDoc}
     */
    public function getDateTo()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDateTo', []);

        return parent::getDateTo();
    }

    /**
     * {@inheritDoc}
     */
    public function setDateTo($date_to): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDateTo', [$date_to]);

        parent::setDateTo($date_to);
    }

    /**
     * {@inheritDoc}
     */
    public function getConflictReason(): ?\App\Entities\References\ConflictReason
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getConflictReason', []);

        return parent::getConflictReason();
    }

    /**
     * {@inheritDoc}
     */
    public function setConflictReason(?\App\Entities\References\ConflictReason $conflictReason): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setConflictReason', [$conflictReason]);

        parent::setConflictReason($conflictReason);
    }

    /**
     * {@inheritDoc}
     */
    public function getConflictResult(): ?\App\Entities\References\ConflictResult
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getConflictResult', []);

        return parent::getConflictResult();
    }

    /**
     * {@inheritDoc}
     */
    public function setConflictResult(?\App\Entities\References\ConflictResult $conflictResult): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setConflictResult', [$conflictResult]);

        parent::setConflictResult($conflictResult);
    }

    /**
     * {@inheritDoc}
     */
    public function getIndustry(): ?\App\Entities\References\Industry
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIndustry', []);

        return parent::getIndustry();
    }

    /**
     * {@inheritDoc}
     */
    public function setIndustry(?\App\Entities\References\Industry $industry): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIndustry', [$industry]);

        parent::setIndustry($industry);
    }

    /**
     * {@inheritDoc}
     */
    public function getRegion(): ?\App\Entities\References\Region
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRegion', []);

        return parent::getRegion();
    }

    /**
     * {@inheritDoc}
     */
    public function setRegion(?\App\Entities\References\Region $region): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRegion', [$region]);

        parent::setRegion($region);
    }

    /**
     * {@inheritDoc}
     */
    public function getEvents()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEvents', []);

        return parent::getEvents();
    }

    /**
     * {@inheritDoc}
     */
    public function setEvents($events): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEvents', [$events]);

        parent::setEvents($events);
    }

    /**
     * {@inheritDoc}
     */
    public function getParentEvent(): ?\App\Entities\Event
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getParentEvent', []);

        return parent::getParentEvent();
    }

    /**
     * {@inheritDoc}
     */
    public function setParentEvent(?\App\Entities\Event $parentEvent): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setParentEvent', [$parentEvent]);

        parent::setParentEvent($parentEvent);
    }

    /**
     * {@inheritDoc}
     */
    public function getTitleByLocale(string $locale): ?string
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTitleByLocale', [$locale]);

        return parent::getTitleByLocale($locale);
    }

    /**
     * {@inheritDoc}
     */
    public function getTitleRu()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTitleRu', []);

        return parent::getTitleRu();
    }

    /**
     * {@inheritDoc}
     */
    public function setTitleRu($title_ru): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTitleRu', [$title_ru]);

        parent::setTitleRu($title_ru);
    }

    /**
     * {@inheritDoc}
     */
    public function getTitleEn()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTitleEn', []);

        return parent::getTitleEn();
    }

    /**
     * {@inheritDoc}
     */
    public function setTitleEn($title_en): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTitleEn', [$title_en]);

        parent::setTitleEn($title_en);
    }

    /**
     * {@inheritDoc}
     */
    public function getTitleEs()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTitleEs', []);

        return parent::getTitleEs();
    }

    /**
     * {@inheritDoc}
     */
    public function setTitleEs($title_es): void
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTitleEs', [$title_es]);

        parent::setTitleEs($title_es);
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedAt()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCreatedAt', []);

        return parent::getCreatedAt();
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdatedAt()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getUpdatedAt', []);

        return parent::getUpdatedAt();
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCreatedAt', [$createdAt]);

        return parent::setCreatedAt($createdAt);
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setUpdatedAt', [$updatedAt]);

        return parent::setUpdatedAt($updatedAt);
    }

}
