<?php

namespace Root23\JsonCanonicalizer;

trait ArrayHelperTrait
{
    public static function isArrayAssoc(array $array): bool
    {
        return [] !== $array && !array_is_list($array);
    }
}
