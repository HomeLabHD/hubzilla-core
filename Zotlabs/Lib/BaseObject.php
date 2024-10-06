<?php

namespace Zotlabs\Lib;

use Zotlabs\ActivityStreams\UnhandledElementException;

class BaseObject
{

    public $string;
    public $ldContext;

    /**
     * @param $input
     * @param $strict
     * @throws UnhandledElementException if $strict
     */

    public function __construct($input = null, $strict = false)
    {
        if (isset($input)) {
            if (is_string($input)) {
                $this->string = $input;
            }
            elseif(is_array($input)) {
                foreach ($input as $key => $value) {
                    $key = ($key === '@context') ? 'ldContext' : $key;
                    if ($strict && !property_exists($this, $key)) {
                        throw new UnhandledElementException("Unhandled element: $key");
                    }
                    $this->{$key} = $value;
                }
            }
        }
        return $this;
    }

    public function getDataType($element, $object = null)
    {
        $object = $object ?? $this;
        $type = gettype($object[$element]);
        if ($type === 'array' && array_is_list($object[$element])) {
            return 'list';
        }
        return $type;
    }

    public function toArray()
    {
        if ($this->string) {
            return $this->string;
        }
        $returnValue = [];
        foreach ((array) $this as $key => $value) {
            if (isset($value)) {
                $key = ($key === 'ldContext') ? '@context' : $key;
                $returnValue[$key] = (($value instanceof BaseObject) ? $value->toArray() : $value);
            }
        }
        return $returnValue;
    }

    /**
     * @return mixed
     */
    public function getLdContext()
    {
        return $this->ldContext;
    }

    /**
     * @param mixed $ldContext
     * @return BaseObject
     */
    public function setLdContext($ldContext)
    {
        $this->ldContext = $ldContext;
        return $this;
    }
}
