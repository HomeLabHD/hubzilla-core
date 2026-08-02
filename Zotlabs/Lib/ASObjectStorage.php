<?php

namespace Zotlabs\Lib;

/*
 * ASObjectStorage
 * General purpose function to encode/decode ActivityStreams objects.
 * This safely stores and retrieves values that might either be a URI, object, or array of
 * objects. Other types (i.e. numeric values) are passed through. `encode()` only converts array to string
 * and `decode()` only converts string to array. Either may be called repeatedly/recursively on the same content
 * with the same results. This means you can call decode() at any time without checking first to see if the object
 * has already been decoded or if it is in fact a URI.
 *
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

    public function encode()
    {
        if (is_string($this->object)) {
            return $this->object;
        }
        if (is_array($this->object)) {
            $this->object = json_encode($this->object, JSON_UNESCAPED_SLASHES);
        }
        return $this->object;
    }
}
