<?php

namespace Zotlabs\Entity;

use Zotlabs\Lib\BaseObject;

class Item extends BaseObject
{
    // primary index auto-increment
    public $id;
    // id of the thread top-level parent
    public $parent;

    // account id of the owner of this record
    public $aid;
    // channel id of the owner of this record
    public $uid;

    // UUID-V4
    public $uuid;

    // message id
    public $mid;

    // message id of top-level parent
    public $parent_mid;
    // message id of immediate parent
    public $thr_parent;

    // int thread depth
    public $item_level;

    // url pointing to the local copy of this item
    public $llink;

    // url pointing to permalink or this item origin
    public $plink;

    // UTC datetime the item was created
    public $created;
    // UTC datetime the item was changed
    public $edited;
    // UTC datetime the item will self-destruct
    public $expires;
    // UTC datetime last comment
    public $commented;
    // UTC datetime received here
    public $received;
    // UTC datetime this record last modified
    public $changed;
    // UTC datetime comments will be refused
    public $comments_closed;

    // portable id of channel that initiated this conversation thread
    public $owner_xchan;
    // portable id of item creator
    public $author_xchan;
    // portable id of real (upstream) author if relayed
    public $source_xchan;
    // ActivityPub FEP
    public $approved;
    // MIME type of item body
    public $mimetype;
    // replyTo where to direct replies
    public $replyto;

    // ISO language string
    public $lang;
    // Name of publishing  software
    public $app;
    // Title of item
    public $title;
    // Sommary of item
    public $summary;
    // body of item
    public $body;
    // html representation
    public $html;
    // Activity type
    public $verb;
    // Activity object type
    public $obj_type;
    // Activity object content JSON
    public $obj;
    // Activity target type
    public $tgt_type;
    // Activity target content JSON
    public $target;
    // attachments
    public $attach;
    // signature information
    public $sig;

    // version of this item
    public $revision;
    // message id of controlling layout (CMS use)
    public $layout_mid;
    // shared storage for delivery addons
    public $postopts;
    // delivery relay tracking
    public $route;
    // UUID of attached resource
    public $resource_id;
    // table type of attacched resource
    public $resource_type;

    // free-form text location
    public $location;
    // float latitude;
    public float $lat;
    // float longitude;
    public float $lon;
    // deprecated combined coordinates (text)
    public $coord;
    // deprecated
    public $public_policy;
    // string comment policy
    public $comment_policy;
    // string encoded array of portable ids
    public $allow_cid;
    // string encoded array of access group UUIDs
    public $allow_gid;
    // string encoded array blocked portable ids
    public $deny_cid;
    // string encoded array blocked access group UUIDs
    public $deny_gid;
    public $item_restrict;
    public $item_flags;
    public $item_private;
    public $item_origin;
    public $item_unseen;
    public $item_starred;
    public $item_uplink;
    public $item_consensus;
    public $item_wall;
    public $item_thread_top;
    public $item_notshown;
    public $item_nsfw;
    public $item_relay;
    public $item_mentionsme;
    public $item_nocomment;
    public $item_obscured;
    public $item_verified;
    public $item_retained;
    public $item_rss;
    public $item_deleted;
    public $item_type;
    public $item_hidden;
    public $item_unpublished;
    public $item_delayed;
    public $item_pending_remove;
    public $item_blocked;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Item
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMid()
    {
        return $this->mid;
    }

    /**
     * @param mixed $mid
     * @return Item
     */
    public function setMid($mid)
    {
        $this->mid = $mid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param mixed $uuid
     * @return Item
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAid()
    {
        return $this->aid;
    }

    /**
     * @param mixed $aid
     * @return Item
     */
    public function setAid($aid)
    {
        $this->aid = $aid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     * @return Item
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     * @return Item
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentMid()
    {
        return $this->parent_mid;
    }

    /**
     * @param mixed $parent_mid
     * @return Item
     */
    public function setParentMid($parent_mid)
    {
        $this->parent_mid = $parent_mid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getThrParent()
    {
        return $this->thr_parent;
    }

    /**
     * @param mixed $thr_parent
     * @return Item
     */
    public function setThrParent($thr_parent)
    {
        $this->thr_parent = $thr_parent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->item_level;
    }

    /**
     * @param mixed $item_level
     * @return Item
     */
    public function setLevel($item_level)
    {
        $this->item_level = $item_level;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     * @return Item
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEdited()
    {
        return $this->edited;
    }

    /**
     * @param mixed $edited
     * @return Item
     */
    public function setEdited($edited)
    {
        $this->edited = $edited;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @param mixed $expires
     * @return Item
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCommented()
    {
        return $this->commented;
    }

    /**
     * @param mixed $commented
     * @return Item
     */
    public function setCommented($commented)
    {
        $this->commented = $commented;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReceived()
    {
        return $this->received;
    }

    /**
     * @param mixed $received
     * @return Item
     */
    public function setReceived($received)
    {
        $this->received = $received;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChanged()
    {
        return $this->changed;
    }

    /**
     * @param mixed $changed
     * @return Item
     */
    public function setChanged($changed)
    {
        $this->changed = $changed;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCommentsClosed()
    {
        return $this->comments_closed;
    }

    /**
     * @param mixed $comments_closed
     * @return Item
     */
    public function setCommentsClosed($comments_closed)
    {
        $this->comments_closed = $comments_closed;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOwnerXchan()
    {
        return $this->owner_xchan;
    }

    /**
     * @param mixed $owner_xchan
     * @return Item
     */
    public function setOwnerXchan($owner_xchan)
    {
        $this->owner_xchan = $owner_xchan;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthorXchan()
    {
        return $this->author_xchan;
    }

    /**
     * @param mixed $author_xchan
     * @return Item
     */
    public function setAuthorXchan($author_xchan)
    {
        $this->author_xchan = $author_xchan;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSourceXchan()
    {
        return $this->source_xchan;
    }

    /**
     * @param mixed $source_xchan
     * @return Item
     */
    public function setSourceXchan($source_xchan)
    {
        $this->source_xchan = $source_xchan;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * @param mixed $approved
     * @return Item
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * @param mixed $mimetype
     * @return Item
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReplyto()
    {
        return $this->replyto;
    }

    /**
     * @param mixed $replyto
     * @return Item
     */
    public function setReplyto($replyto)
    {
        $this->replyto = $replyto;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Item
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param mixed $summary
     * @return Item
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     * @return Item
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param mixed $html
     * @return Item
     */
    public function setHtml($html)
    {
        $this->html = $html;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param mixed $app
     * @return Item
     */
    public function setApp($app)
    {
        $this->app = $app;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     * @return Item
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * @param mixed $revision
     * @return Item
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * @param mixed $verb
     * @return Item
     */
    public function setVerb($verb)
    {
        $this->verb = $verb;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getObjType()
    {
        return $this->obj_type;
    }

    /**
     * @param mixed $obj_type
     * @return Item
     */
    public function setObjType($obj_type)
    {
        $this->obj_type = $obj_type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getObj()
    {
        return $this->obj;
    }

    /**
     * @param mixed $obj
     * @return Item
     */
    public function setObj($obj)
    {
        $this->obj = $obj;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTgtType()
    {
        return $this->tgt_type;
    }

    /**
     * @param mixed $tgt_type
     * @return Item
     */
    public function setTgtType($tgt_type)
    {
        $this->tgt_type = $tgt_type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param mixed $target
     * @return Item
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLayoutMid()
    {
        return $this->layout_mid;
    }

    /**
     * @param mixed $layout_mid
     * @return Item
     */
    public function setLayoutMid($layout_mid)
    {
        $this->layout_mid = $layout_mid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostopts()
    {
        return $this->postopts;
    }

    /**
     * @param mixed $postopts
     * @return Item
     */
    public function setPostopts($postopts)
    {
        $this->postopts = $postopts;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param mixed $route
     * @return Item
     */
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLlink()
    {
        return $this->llink;
    }

    /**
     * @param mixed $llink
     * @return Item
     */
    public function setLlink($llink)
    {
        $this->llink = $llink;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlink()
    {
        return $this->plink;
    }

    /**
     * @param mixed $plink
     * @return Item
     */
    public function setPlink($plink)
    {
        $this->plink = $plink;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResourceId()
    {
        return $this->resource_id;
    }

    /**
     * @param mixed $resource_id
     * @return Item
     */
    public function setResourceId($resource_id)
    {
        $this->resource_id = $resource_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResourceType()
    {
        return $this->resource_type;
    }

    /**
     * @param mixed $resource_type
     * @return Item
     */
    public function setResourceType($resource_type)
    {
        $this->resource_type = $resource_type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttach()
    {
        return $this->attach;
    }

    /**
     * @param mixed $attach
     * @return Item
     */
    public function setAttach($attach)
    {
        $this->attach = $attach;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSig()
    {
        return $this->sig;
    }

    /**
     * @param mixed $sig
     * @return Item
     */
    public function setSig($sig)
    {
        $this->sig = $sig;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     * @return Item
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCoord()
    {
        return $this->coord;
    }

    /**
     * @param mixed $coord
     * @return Item
     */
    public function setCoord($coord)
    {
        $this->coord = $coord;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublicPolicy()
    {
        return $this->public_policy;
    }

    /**
     * @param mixed $public_policy
     * @return Item
     */
    public function setPublicPolicy($public_policy)
    {
        $this->public_policy = $public_policy;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCommentPolicy()
    {
        return $this->comment_policy;
    }

    /**
     * @param mixed $comment_policy
     * @return Item
     */
    public function setCommentPolicy($comment_policy)
    {
        $this->comment_policy = $comment_policy;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllowCid()
    {
        return $this->allow_cid;
    }

    /**
     * @param mixed $allow_cid
     * @return Item
     */
    public function setAllowCid($allow_cid)
    {
        $this->allow_cid = $allow_cid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllowGid()
    {
        return $this->allow_gid;
    }

    /**
     * @param mixed $allow_gid
     * @return Item
     */
    public function setAllowGid($allow_gid)
    {
        $this->allow_gid = $allow_gid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDenyCid()
    {
        return $this->deny_cid;
    }

    /**
     * @param mixed $deny_cid
     * @return Item
     */
    public function setDenyCid($deny_cid)
    {
        $this->deny_cid = $deny_cid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDenyGid()
    {
        return $this->deny_gid;
    }

    /**
     * @param mixed $deny_gid
     * @return Item
     */
    public function setDenyGid($deny_gid)
    {
        $this->deny_gid = $deny_gid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRestrict()
    {
        return $this->item_restrict;
    }

    /**
     * @param mixed $item_restrict
     * @return Item
     */
    public function setRestrict($item_restrict)
    {
        $this->item_restrict = $item_restrict;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFlags()
    {
        return $this->item_flags;
    }

    /**
     * @param mixed $item_flags
     * @return Item
     */
    public function setFlags($item_flags)
    {
        $this->item_flags = $item_flags;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrivate()
    {
        return $this->item_private;
    }

    /**
     * @param mixed $item_private
     * @return Item
     */
    public function setPrivate($item_private)
    {
        $this->item_private = $item_private;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrigin()
    {
        return $this->item_origin;
    }

    /**
     * @param mixed $item_origin
     * @return Item
     */
    public function setOrigin($item_origin)
    {
        $this->item_origin = $item_origin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnseen()
    {
        return $this->item_unseen;
    }

    /**
     * @param mixed $item_unseen
     * @return Item
     */
    public function setUnseen($item_unseen)
    {
        $this->item_unseen = $item_unseen;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStarred()
    {
        return $this->item_starred;
    }

    /**
     * @param mixed $item_starred
     * @return Item
     */
    public function setStarred($item_starred)
    {
        $this->item_starred = $item_starred;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUplink()
    {
        return $this->item_uplink;
    }

    /**
     * @param mixed $item_uplink
     * @return Item
     */
    public function setUplink($item_uplink)
    {
        $this->item_uplink = $item_uplink;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConsensus()
    {
        return $this->item_consensus;
    }

    /**
     * @param mixed $item_consensus
     * @return Item
     */
    public function setConsensus($item_consensus)
    {
        $this->item_consensus = $item_consensus;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWall()
    {
        return $this->item_wall;
    }

    /**
     * @param mixed $item_wall
     * @return Item
     */
    public function setWall($item_wall)
    {
        $this->item_wall = $item_wall;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getThreadTop()
    {
        return $this->item_thread_top;
    }

    /**
     * @param mixed $item_thread_top
     * @return Item
     */
    public function setThreadTop($item_thread_top)
    {
        $this->item_thread_top = $item_thread_top;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNotshown()
    {
        return $this->item_notshown;
    }

    /**
     * @param mixed $item_notshown
     * @return Item
     */
    public function setNotshown($item_notshown)
    {
        $this->item_notshown = $item_notshown;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNsfw()
    {
        return $this->item_nsfw;
    }

    /**
     * @param mixed $item_nsfw
     * @return Item
     */
    public function setNsfw($item_nsfw)
    {
        $this->item_nsfw = $item_nsfw;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRelay()
    {
        return $this->item_relay;
    }

    /**
     * @param mixed $item_relay
     * @return Item
     */
    public function setRelay($item_relay)
    {
        $this->item_relay = $item_relay;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMentionsme()
    {
        return $this->item_mentionsme;
    }

    /**
     * @param mixed $item_mentionsme
     * @return Item
     */
    public function setMentionsme($item_mentionsme)
    {
        $this->item_mentionsme = $item_mentionsme;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNocomment()
    {
        return $this->item_nocomment;
    }

    /**
     * @param mixed $item_nocomment
     * @return Item
     */
    public function setNocomment($item_nocomment)
    {
        $this->item_nocomment = $item_nocomment;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getObscured()
    {
        return $this->item_obscured;
    }

    /**
     * @param mixed $item_obscured
     * @return Item
     */
    public function setObscured($item_obscured)
    {
        $this->item_obscured = $item_obscured;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVerified()
    {
        return $this->item_verified;
    }

    /**
     * @param mixed $item_verified
     * @return Item
     */
    public function setVerified($item_verified)
    {
        $this->item_verified = $item_verified;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRetained()
    {
        return $this->item_retained;
    }

    /**
     * @param mixed $item_retained
     * @return Item
     */
    public function setRetained($item_retained)
    {
        $this->item_retained = $item_retained;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRss()
    {
        return $this->item_rss;
    }

    /**
     * @param mixed $item_rss
     * @return Item
     */
    public function setRss($item_rss)
    {
        $this->item_rss = $item_rss;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->item_deleted;
    }

    /**
     * @param mixed $item_deleted
     * @return Item
     */
    public function setDeleted($item_deleted)
    {
        $this->item_deleted = $item_deleted;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->item_type;
    }

    /**
     * @param mixed $item_type
     * @return Item
     */
    public function setType($item_type)
    {
        $this->item_type = $item_type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHidden()
    {
        return $this->item_hidden;
    }

    /**
     * @param mixed $item_hidden
     * @return Item
     */
    public function setHidden($item_hidden)
    {
        $this->item_hidden = $item_hidden;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnpublished()
    {
        return $this->item_unpublished;
    }

    /**
     * @param mixed $item_unpublished
     * @return Item
     */
    public function setUnpublished($item_unpublished)
    {
        $this->item_unpublished = $item_unpublished;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDelayed()
    {
        return $this->item_delayed;
    }

    /**
     * @param mixed $item_delayed
     * @return Item
     */
    public function setDelayed($item_delayed)
    {
        $this->item_delayed = $item_delayed;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPendingRemove()
    {
        return $this->item_pending_remove;
    }

    /**
     * @param mixed $item_pending_remove
     * @return Item
     */
    public function setPendingRemove($item_pending_remove)
    {
        $this->item_pending_remove = $item_pending_remove;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBlocked()
    {
        return $this->item_blocked;
    }

    /**
     * @param mixed $item_blocked
     * @return Item
     */
    public function setBlocked($item_blocked)
    {
        $this->item_blocked = $item_blocked;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param float $lat
     * @return Item
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * @param float $lon
     * @return Item
     */
    public function setLon($lon)
    {
        $this->lon = $lon;
        return $this;
    }

}
