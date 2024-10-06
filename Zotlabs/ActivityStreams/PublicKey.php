<?php

namespace Zotlabs\ActivityStreams;

class PublicKey extends ASObject
{
    public $owner;
    public $signatureAlgorithm;
    public $publicKeyPem;

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     * @return PublicKey
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSignatureAlgorithm()
    {
        return $this->signatureAlgorithm;
    }

    /**
     * @param mixed $signatureAlgorithm
     * @return PublicKey
     */
    public function setSignatureAlgorithm($signatureAlgorithm)
    {
        $this->signatureAlgorithm = $signatureAlgorithm;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublicKeyPem()
    {
        return $this->publicKeyPem;
    }

    /**
     * @param mixed $publicKeyPem
     * @return PublicKey
     */
    public function setPublicKeyPem($publicKeyPem)
    {
        $this->publicKeyPem = $publicKeyPem;
        return $this;
    }




}
