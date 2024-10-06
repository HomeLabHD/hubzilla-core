<?php

namespace Zotlabs\ActivityStreams;


use Zotlabs\Lib\BaseObject;

class ASObject extends BaseObject
{
    public $id;
    public $type;
    public $attachment;
    public $attributedTo;
    public $audience;
    public $content;
    public $context;
    public $name;
    public $endTime;
    public $generator;
    public $icon;
    public $image;
    public $inReplyTo;
    public $location;
    public $preview;
    public $published;
    public $replies;
    public $startTime;
    public $summary;
    public $tag;
    public $updated;
    public $url;
    public $to;
    public $bto;
    public $cc;
    public $bcc;
    public $mediaType;
    public $duration;
    public $source;


    // Extension properties

    public $signature;
    public $proof;
    public $sensitive;
    public $replyTo;
    public $wall;
    public $isContainedConversation;
    public $expires;
    public $canReply;
    public $canSearch;
    public $directMessage;
    public $commentPolicy;

    /**
     * @return mixed
     */
    public function getDirectMessage()
    {
        return $this->directMessage;
    }

    /**
     * @param mixed $directMessage
     * @return ASObject
     */
    public function setDirectMessage($directMessage)
    {
        $this->directMessage = $directMessage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param mixed $signature
     * @return ASObject
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProof()
    {
        return $this->proof;
    }

    /**
     * @param mixed $proof
     * @return ASObject
     */
    public function setProof($proof)
    {
        $this->proof = $proof;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getSensitive()
    {
        return $this->sensitive;
    }

    /**
     * @param mixed $sensitive
     * @return ASObject
     */
    public function setSensitive($sensitive)
    {
        $this->sensitive = $sensitive;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * @param mixed $replyTo
     * @return ASObject
     */
    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWall()
    {
        return $this->wall;
    }

    /**
     * @param mixed $wall
     * @return ASObject
     */
    public function setWall($wall)
    {
        $this->wall = $wall;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsContainedConversation()
    {
        return $this->isContainedConversation;
    }

    /**
     * @param mixed $isContainedConversation
     * @return ASObject
     */
    public function setIsContainedConversation($isContainedConversation)
    {
        $this->isContainedConversation = $isContainedConversation;
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
     * @return ASObject
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCanReply()
    {
        return $this->canReply;
    }

    /**
     * @param mixed $canReply
     * @return ASObject
     */
    public function setCanReply($canReply)
    {
        $this->canReply = $canReply;
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
     * @return ASObject
     */
    public function setCanSearch($canSearch)
    {
        $this->canSearch = $canSearch;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCommentPolicy()
    {
        return $this->commentPolicy;
    }

    /**
     * @param mixed $commentPolicy
     * @return ASObject
     */
    public function setCommentPolicy($commentPolicy)
    {
        $this->commentPolicy = $commentPolicy;
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
     * @return ASObject
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
     * @return ASObject
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @param mixed $attachment
     * @return ASObject
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttributedTo()
    {
        return $this->attributedTo;
    }

    /**
     * @param mixed $attributedTo
     * @return ASObject
     */
    public function setAttributedTo($attributedTo)
    {
        $this->attributedTo = $attributedTo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAudience()
    {
        return $this->audience;
    }

    /**
     * @param mixed $audience
     * @return ASObject
     */
    public function setAudience($audience)
    {
        $this->audience = $audience;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     * @return ASObject
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param mixed $context
     * @return ASObject
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return ASObject
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $endTime
     * @return ASObject
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * @param mixed $generator
     * @return ASObject
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     * @return ASObject
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     * @return ASObject
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInReplyTo()
    {
        return $this->inReplyTo;
    }

    /**
     * @param mixed $inReplyTo
     * @return ASObject
     */
    public function setInReplyTo($inReplyTo)
    {
        $this->inReplyTo = $inReplyTo;
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
     * @return ASObject
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * @param mixed $preview
     * @return ASObject
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param mixed $published
     * @return ASObject
     */
    public function setPublished($published)
    {
        $this->published = $published;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReplies()
    {
        return $this->replies;
    }

    /**
     * @param mixed $replies
     * @return ASObject
     */
    public function setReplies($replies)
    {
        $this->replies = $replies;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $startTime
     * @return ASObject
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
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
     * @return ASObject
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     * @return ASObject
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     * @return ASObject
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return ASObject
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param mixed $to
     * @return ASObject
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBto()
    {
        return $this->bto;
    }

    /**
     * @param mixed $bto
     * @return ASObject
     */
    public function setBto($bto)
    {
        $this->bto = $bto;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param mixed $cc
     * @return ASObject
     */
    public function setCc($cc)
    {
        $this->cc = $cc;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @param mixed $bcc
     * @return ASObject
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMediaType()
    {
        return $this->mediaType;
    }

    /**
     * @param mixed $mediaType
     * @return ASObject
     */
    public function setMediaType($mediaType)
    {
        $this->mediaType = $mediaType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param mixed $duration
     * @return ASObject
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     * @return ASObject
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

}
