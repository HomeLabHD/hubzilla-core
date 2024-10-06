<?php

namespace Zotlabs\Entity;

use Zotlabs\Lib\BaseObject;

class Account extends BaseObject
{
    public $account_id;
    public $account_parent;
    public $account_default_channel;
    public $account_salt;
    public $account_password;
    public $account_email;
    public $account_external;
    public $account_language;
    public $account_created;
    public $account_lastlog;
    public $account_flags;
    public $account_roles;
    public $account_reset;
    public $account_expires;
    public $account_expire_notified;
    public $account_service_class;
    public $account_level;
    public $account_password_change;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->account_id;
    }

    /**
     * @param mixed $account_id
     * @return Account
     */
    public function setId($account_id)
    {
        $this->account_id = $account_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->account_parent;
    }

    /**
     * @param mixed $account_parent
     * @return Account
     */
    public function setParent($account_parent)
    {
        $this->account_parent = $account_parent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultChannel()
    {
        return $this->account_default_channel;
    }

    /**
     * @param mixed $account_default_channel
     * @return Account
     */
    public function setDefaultChannel($account_default_channel)
    {
        $this->account_default_channel = $account_default_channel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->account_salt;
    }

    /**
     * @param mixed $account_salt
     * @return Account
     */
    public function setSalt($account_salt)
    {
        $this->account_salt = $account_salt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->account_password;
    }

    /**
     * @param mixed $account_password
     * @return Account
     */
    public function setPassword($account_password)
    {
        $this->account_password = $account_password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->account_email;
    }

    /**
     * @param mixed $account_email
     * @return Account
     */
    public function setEmail($account_email)
    {
        $this->account_email = $account_email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExternal()
    {
        return $this->account_external;
    }

    /**
     * @param mixed $account_external
     * @return Account
     */
    public function setExternal($account_external)
    {
        $this->account_external = $account_external;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->account_language;
    }

    /**
     * @param mixed $account_language
     * @return Account
     */
    public function setLanguage($account_language)
    {
        $this->account_language = $account_language;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->account_created;
    }

    /**
     * @param mixed $account_created
     * @return Account
     */
    public function setCreated($account_created)
    {
        $this->account_created = $account_created;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastlog()
    {
        return $this->account_lastlog;
    }

    /**
     * @param mixed $account_lastlog
     * @return Account
     */
    public function setLastlog($account_lastlog)
    {
        $this->account_lastlog = $account_lastlog;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFlags()
    {
        return $this->account_flags;
    }

    /**
     * @param mixed $account_flags
     * @return Account
     */
    public function setFlags($account_flags)
    {
        $this->account_flags = $account_flags;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->account_roles;
    }

    /**
     * @param mixed $account_roles
     * @return Account
     */
    public function setRoles($account_roles)
    {
        $this->account_roles = $account_roles;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReset()
    {
        return $this->account_reset;
    }

    /**
     * @param mixed $account_reset
     * @return Account
     */
    public function setReset($account_reset)
    {
        $this->account_reset = $account_reset;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpires()
    {
        return $this->account_expires;
    }

    /**
     * @param mixed $account_expires
     * @return Account
     */
    public function setExpires($account_expires)
    {
        $this->account_expires = $account_expires;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpireNotified()
    {
        return $this->account_expire_notified;
    }

    /**
     * @param mixed $account_expire_notified
     * @return Account
     */
    public function setExpireNotified($account_expire_notified)
    {
        $this->account_expire_notified = $account_expire_notified;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getServiceClass()
    {
        return $this->account_service_class;
    }

    /**
     * @param mixed $account_service_class
     * @return Account
     */
    public function setServiceClass($account_service_class)
    {
        $this->account_service_class = $account_service_class;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->account_level;
    }

    /**
     * @param mixed $account_level
     * @return Account
     */
    public function setLevel($account_level)
    {
        $this->account_level = $account_level;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPasswordChange()
    {
        return $this->account_password_change;
    }

    /**
     * @param mixed $account_password_change
     * @return Account
     */
    public function setPasswordChange($account_password_change)
    {
        $this->account_password_change = $account_password_change;
        return $this;
    }

}
