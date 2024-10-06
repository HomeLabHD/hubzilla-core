<?php

namespace Zotlabs\Entity;

use Zotlabs\Lib\BaseObject;

class Channel extends BaseObject
{
    public $channel_id;
    public $channel_account_id;
    public $channel_primary;
    public $channel_name;
    public $channel_parent;
    public $channel_address;
    public $channel_guid;
    public $channel_guid_sig;
    public $channel_hash;
    public $channel_timezone;
    public $channel_location;
    public $channel_theme;
    public $channel_startpage;
    public $channel_pubkey;
    public $channel_prvkey;
    public $channel_epubkey;
    public $channel_eprvkey;
    public $channel_notifyflags;
    public $channel_pageflags;
    public $channel_dirdate;
    public $channel_lastpost;
    public $channel_deleted;
    public $channel_active;
    public $channel_max_anon_mail;
    public $channel_max_friend_req;
    public $channel_expire_days;
    public $channel_passwd_reset;
    public $channel_default_group;
    public $channel_allow_cid;
    public $channel_allow_gid;
    public $channel_deny_cid;
    public $channel_deny_gid;
    public $channel_removed;
    public $channel_system;
    public $channel_moved;
    public $channel_password;
    public $channel_salt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->channel_id;
    }

    /**
     * @param mixed $channel_id
     * @return Channel
     */
    public function setId($channel_id)
    {
        $this->channel_id = $channel_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAccountId()
    {
        return $this->channel_account_id;
    }

    /**
     * @param mixed $channel_account_id
     * @return Channel
     */
    public function setAccountId($channel_account_id)
    {
        $this->channel_account_id = $channel_account_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrimary()
    {
        return $this->channel_primary;
    }

    /**
     * @param mixed $channel_primary
     * @return Channel
     */
    public function setPrimary($channel_primary)
    {
        $this->channel_primary = $channel_primary;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->channel_name;
    }

    /**
     * @param mixed $channel_name
     * @return Channel
     */
    public function setName($channel_name)
    {
        $this->channel_name = $channel_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->channel_parent;
    }

    /**
     * @param mixed $channel_parent
     * @return Channel
     */
    public function setParent($channel_parent)
    {
        $this->channel_parent = $channel_parent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->channel_address;
    }

    /**
     * @param mixed $channel_address
     * @return Channel
     */
    public function setAddress($channel_address)
    {
        $this->channel_address = $channel_address;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGuid()
    {
        return $this->channel_guid;
    }

    /**
     * @param mixed $channel_guid
     * @return Channel
     */
    public function setGuid($channel_guid)
    {
        $this->channel_guid = $channel_guid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGuidSig()
    {
        return $this->channel_guid_sig;
    }

    /**
     * @param mixed $channel_guid_sig
     * @return Channel
     */
    public function setGuidSig($channel_guid_sig)
    {
        $this->channel_guid_sig = $channel_guid_sig;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->channel_hash;
    }

    /**
     * @param mixed $channel_hash
     * @return Channel
     */
    public function setHash($channel_hash)
    {
        $this->channel_hash = $channel_hash;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->channel_timezone;
    }

    /**
     * @param mixed $channel_timezone
     * @return Channel
     */
    public function setTimezone($channel_timezone)
    {
        $this->channel_timezone = $channel_timezone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->channel_location;
    }

    /**
     * @param mixed $channel_location
     * @return Channel
     */
    public function setLocation($channel_location)
    {
        $this->channel_location = $channel_location;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTheme()
    {
        return $this->channel_theme;
    }

    /**
     * @param mixed $channel_theme
     * @return Channel
     */
    public function setTheme($channel_theme)
    {
        $this->channel_theme = $channel_theme;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartpage()
    {
        return $this->channel_startpage;
    }

    /**
     * @param mixed $channel_startpage
     * @return Channel
     */
    public function setStartpage($channel_startpage)
    {
        $this->channel_startpage = $channel_startpage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPubkey()
    {
        return $this->channel_pubkey;
    }

    /**
     * @param mixed $channel_pubkey
     * @return Channel
     */
    public function setPubkey($channel_pubkey)
    {
        $this->channel_pubkey = $channel_pubkey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrvkey()
    {
        return $this->channel_prvkey;
    }

    /**
     * @param mixed $channel_prvkey
     * @return Channel
     */
    public function setPrvkey($channel_prvkey)
    {
        $this->channel_prvkey = $channel_prvkey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEpubkey()
    {
        return $this->channel_epubkey;
    }

    /**
     * @param mixed $channel_epubkey
     * @return Channel
     */
    public function setEpubkey($channel_epubkey)
    {
        $this->channel_epubkey = $channel_epubkey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEprvkey()
    {
        return $this->channel_eprvkey;
    }

    /**
     * @param mixed $channel_eprvkey
     * @return Channel
     */
    public function setEprvkey($channel_eprvkey)
    {
        $this->channel_eprvkey = $channel_eprvkey;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getNotifyflags()
    {
        return $this->channel_notifyflags;
    }

    /**
     * @param mixed $channel_notifyflags
     * @return Channel
     */
    public function setNotifyflags($channel_notifyflags)
    {
        $this->channel_notifyflags = $channel_notifyflags;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPageflags()
    {
        return $this->channel_pageflags;
    }

    /**
     * @param mixed $channel_pageflags
     * @return Channel
     */
    public function setPageflags($channel_pageflags)
    {
        $this->channel_pageflags = $channel_pageflags;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDirdate()
    {
        return $this->channel_dirdate;
    }

    /**
     * @param mixed $channel_dirdate
     * @return Channel
     */
    public function setDirdate($channel_dirdate)
    {
        $this->channel_dirdate = $channel_dirdate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastpost()
    {
        return $this->channel_lastpost;
    }

    /**
     * @param mixed $channel_lastpost
     * @return Channel
     */
    public function setLastpost($channel_lastpost)
    {
        $this->channel_lastpost = $channel_lastpost;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->channel_deleted;
    }

    /**
     * @param mixed $channel_deleted
     * @return Channel
     */
    public function setDeleted($channel_deleted)
    {
        $this->channel_deleted = $channel_deleted;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->channel_active;
    }

    /**
     * @param mixed $channel_active
     * @return Channel
     */
    public function setActive($channel_active)
    {
        $this->channel_active = $channel_active;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxAnonMail()
    {
        return $this->channel_max_anon_mail;
    }

    /**
     * @param mixed $channel_max_anon_mail
     * @return Channel
     */
    public function setMaxAnonMail($channel_max_anon_mail)
    {
        $this->channel_max_anon_mail = $channel_max_anon_mail;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxFriendReq()
    {
        return $this->channel_max_friend_req;
    }

    /**
     * @param mixed $channel_max_friend_req
     * @return Channel
     */
    public function setMaxFriendReq($channel_max_friend_req)
    {
        $this->channel_max_friend_req = $channel_max_friend_req;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpireDays()
    {
        return $this->channel_expire_days;
    }

    /**
     * @param mixed $channel_expire_days
     * @return Channel
     */
    public function setExpireDays($channel_expire_days)
    {
        $this->channel_expire_days = $channel_expire_days;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPasswdReset()
    {
        return $this->channel_passwd_reset;
    }

    /**
     * @param mixed $channel_passwd_reset
     * @return Channel
     */
    public function setPasswdReset($channel_passwd_reset)
    {
        $this->channel_passwd_reset = $channel_passwd_reset;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultGroup()
    {
        return $this->channel_default_group;
    }

    /**
     * @param mixed $channel_default_group
     * @return Channel
     */
    public function setDefaultGroup($channel_default_group)
    {
        $this->channel_default_group = $channel_default_group;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllowCid()
    {
        return $this->channel_allow_cid;
    }

    /**
     * @param mixed $channel_allow_cid
     * @return Channel
     */
    public function setAllowCid($channel_allow_cid)
    {
        $this->channel_allow_cid = $channel_allow_cid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllowGid()
    {
        return $this->channel_allow_gid;
    }

    /**
     * @param mixed $channel_allow_gid
     * @return Channel
     */
    public function setAllowGid($channel_allow_gid)
    {
        $this->channel_allow_gid = $channel_allow_gid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDenyCid()
    {
        return $this->channel_deny_cid;
    }

    /**
     * @param mixed $channel_deny_cid
     * @return Channel
     */
    public function setDenyCid($channel_deny_cid)
    {
        $this->channel_deny_cid = $channel_deny_cid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDenyGid()
    {
        return $this->channel_deny_gid;
    }

    /**
     * @param mixed $channel_deny_gid
     * @return Channel
     */
    public function setDenyGid($channel_deny_gid)
    {
        $this->channel_deny_gid = $channel_deny_gid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRemoved()
    {
        return $this->channel_removed;
    }

    /**
     * @param mixed $channel_removed
     * @return Channel
     */
    public function setRemoved($channel_removed)
    {
        $this->channel_removed = $channel_removed;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSystem()
    {
        return $this->channel_system;
    }

    /**
     * @param mixed $channel_system
     * @return Channel
     */
    public function setSystem($channel_system)
    {
        $this->channel_system = $channel_system;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMoved()
    {
        return $this->channel_moved;
    }

    /**
     * @param mixed $channel_moved
     * @return Channel
     */
    public function setMoved($channel_moved)
    {
        $this->channel_moved = $channel_moved;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->channel_password;
    }

    /**
     * @param mixed $channel_password
     * @return Channel
     */
    public function setPassword($channel_password)
    {
        $this->channel_password = $channel_password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->channel_salt;
    }

    /**
     * @param mixed $channel_salt
     * @return Channel
     */
    public function setSalt($channel_salt)
    {
        $this->channel_salt = $channel_salt;
        return $this;
    }

}
