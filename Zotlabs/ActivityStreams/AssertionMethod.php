<?php

namespace Zotlabs\ActivityStreams;

class AssertionMethod extends ASObject
{
    public $id;
    public $type;
    public $controller;
    public $publicKeyMultibase;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return AssertionMethod
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return AssertionMethod
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param mixed $controller
     * @return AssertionMethod
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublicKeyMultibase()
    {
        return $this->publicKeyMultibase;
    }

    /**
     * @param mixed $publicKeyMultibase
     * @return AssertionMethod
     */
    public function setPublicKeyMultibase($publicKeyMultibase)
    {
        $this->publicKeyMultibase = $publicKeyMultibase;
        return $this;
    }




}
