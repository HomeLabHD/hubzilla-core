<?php

namespace Zotlabs\Lib;

/*
 * ASObjectStorage
 * General purpose function to encode/decode ActivityStreams objects for storage as json encoded strings, while
 * also storing contents that are URIs as a string value; which normally cause json_decode() to return null.
 * May be called recursively or repeatedly without first checking type or existence; if you wish to ensure that
 * any data you were passed is or has already been unconditionally converted to the expected format.
 */

class ASObjectStorage
{

    protected $object;

    public function __construct(mixed $object)
    {
        $this->object = $object;
    }

    public function decode()
    {
        if ($this->object) {
            if (is_string($this->object)) {
                $tmp = json_decode($this->object, true);
                if ($tmp !== null) {
                    $this->object = $tmp;
                }
            }
        }
        return $this->object;
    }

    public function encode($options = JSON_UNESCAPED_SLASHES)
    {
        if (is_string($this->object)) {
            return $this->object;
        }
        if (is_array($this->object)) {
            $this->object = json_encode($this->object, $options);
        }
        return $this->object;
    }
}
