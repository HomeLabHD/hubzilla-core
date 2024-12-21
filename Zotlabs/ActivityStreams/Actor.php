<?php

namespace Zotlabs\ActivityStreams;

class Actor extends ASObject
{
    public $inbox;
    public $outbox;
    public $followers;
    public $following;
    public $permissions; /* extension property */
    public $endpoints;
    public $publicKey;
    public $preferredUsername;
    public $alsoKnownAs;

    // Extension properties

    public $movedTo;
    public $copiedTo;
    public $discoverable;
    public $manuallyApprovesFollowers;
    public $webfinger;
    public $canSearch;
    public $indexable;
    public $assertionMethod;
    public $gateways;
    public $openwebauth;
    public $authredirect;

    /**
     * @return mixed
     */
    public function getAlsoKnownAs()
    {
        return $this->alsoKnownAs;
    }

    /**
     * @param mixed $alsoKnownAs
     * @return Actor
     */
    public function setAlsoKnownAs($alsoKnownAs)
    {
        $this->alsoKnownAs = $alsoKnownAs;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMovedTo()
    {
        return $this->movedTo;
    }

    /**
     * @return mixed
     */
    public function getCopiedTo()
    {
        return $this->copiedTo;
    }

    /**
     * @param mixed $copiedTo
     * @return Actor
     */
    public function setCopiedTo($copiedTo)
    {
        $this->copiedTo = $copiedTo;
        return $this;
    }

    /**
     * @param mixed $movedTo
     * @return Actor
     */
    public function setMovedTo($movedTo)
    {
        $this->movedTo = $movedTo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDiscoverable()
    {
        return $this->discoverable;
    }

    /**
     * @param mixed $discoverable
     * @return Actor
     */
    public function setDiscoverable($discoverable)
    {
        $this->discoverable = $discoverable;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getManuallyApprovesFollowers()
    {
        return $this->manuallyApprovesFollowers;
    }

    /**
     * @param mixed $manuallyApprovesFollowers
     * @return Actor
     */
    public function setManuallyApprovesFollowers($manuallyApprovesFollowers)
    {
        $this->manuallyApprovesFollowers = $manuallyApprovesFollowers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPreferredUsername()
    {
        return $this->preferredUsername;
    }

    /**
     * @param mixed $preferredUsername
     * @return Actor
     */
    public function setPreferredUsername($preferredUsername)
    {
        $this->preferredUsername = $preferredUsername;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Actor
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
     * @return Actor
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInbox()
    {
        return $this->inbox;
    }

    /**
     * @param mixed $inbox
     * @return Actor
     */
    public function setInbox($inbox)
    {
        $this->inbox = $inbox;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOutbox()
    {
        return $this->outbox;
    }

    /**
     * @param mixed $outbox
     * @return Actor
     */
    public function setOutbox($outbox)
    {
        $this->outbox = $outbox;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * @param mixed $followers
     * @return Actor
     */
    public function setFollowers($followers)
    {
        $this->followers = $followers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFollowing()
    {
        return $this->following;
    }

    /**
     * @param mixed $following
     * @return Actor
     */
    public function setFollowing($following)
    {
        $this->following = $following;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndpoints()
    {
        return $this->endpoints;
    }

    /**
     * @param mixed $endpoints
     * @return Actor
     */
    public function setEndpoints($endpoints)
    {
        $this->endpoints = $endpoints;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @param mixed $publicKey
     * @return Actor
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWebfinger()
    {
        return $this->webfinger;
    }

    /**
     * @param mixed $webfinger
     * @return Actor
     */
    public function setWebfinger($webfinger)
    {
        $this->webfinger = $webfinger;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCanSearch()
    {
        return $this->canSearch;
    }

    /**
     * @param mixed $canSearch
     * @return Actor
     */
    public function setCanSearch($canSearch)
    {
        $this->canSearch = $canSearch;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIndexable()
    {
        return $this->indexable;
    }

    /**
     * @param mixed $indexable
     * @return Actor
     */
    public function setIndexable($indexable)
    {
        $this->indexable = $indexable;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAssertionMethod()
    {
        return $this->assertionMethod;
    }

    /**
     * @param mixed $assertionMethod
     * @return Actor
     */
    public function setAssertionMethod($assertionMethod)
    {
        $this->assertionMethod = $assertionMethod;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGateways()
    {
        return $this->gateways;
    }

    /**
     * @param mixed $gateways
     * @return Actor
     */
    public function setGateways($gateways)
    {
        $this->gateways = $gateways;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param mixed $permissions
     * @return Actor
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOpenwebauth()
    {
        return $this->openwebauth;
    }

    /**
     * @param mixed $openwebauth
     * @return Actor
     */
    public function setOpenwebauth($openwebauth)
    {
        $this->openwebauth = $openwebauth;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthredirect()
    {
        return $this->authredirect;
    }

    /**
     * @param mixed $authredirect
     * @return Actor
     */
    public function setAuthredirect($authredirect)
    {
        $this->authredirect = $authredirect;
        return $this;
    }

}
