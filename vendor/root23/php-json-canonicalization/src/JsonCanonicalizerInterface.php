<?php

declare(strict_types=1);

namespace Root23\JsonCanonicalizer;

interface JsonCanonicalizerInterface
{
    public function canonicalize($data): string;
    public function canonicalizeAsHex($data): string;
}
