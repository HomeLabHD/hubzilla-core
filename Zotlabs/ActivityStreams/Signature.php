<?php

namespace Zotlabs\ActivityStreams;

class Signature extends ASObject
{
    public $nonce;
    public $creator;
    public $signatureValue;

    /**
     * @return mixed
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param mixed $creator
     * @return Signature
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSignatureValue()
    {
        return $this->signatureValue;
    }

    /**
     * @param mixed $signatureValue
     * @return Signature
     */
    public function setSignatureValue($signatureValue)
    {
        $this->signatureValue = $signatureValue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * @param mixed $nonce
     * @return Signature
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
        return $this;
    }

}
