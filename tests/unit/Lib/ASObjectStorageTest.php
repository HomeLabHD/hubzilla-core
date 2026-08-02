<?php

namespace Zotlabs\Tests\Unit\Lib;

use Zotlabs\Lib\ASObjectStorage;
use Zotlabs\Tests\Unit\UnitTestCase;

class ASObjectStorageTest extends UnitTestCase
{
    public function testStorageEncoding()
    {
        $object = 12345;
        $object = (new ASObjectStorage($object))->encode();
        $this->assertSame(12345, $object);
        $object = "a string";
        $object = (new ASObjectStorage($object))->encode();
        $this->assertSame('a string', $object);
        $object = ["a" => "string"];
        $object = (new ASObjectStorage($object))->encode();
        $this->assertSame('{"a":"string"}', $object);
        $object = '';
        $object = (new ASObjectStorage($object))->encode();
        $this->assertSame('', $object);
        $this->assertNotSame('""', $object);
        $object = null;
        $object = (new ASObjectStorage($object))->encode();
        $this->assertNull($object);
    }

    public function testStorageDecoding()
    {
        $object = 12345;
        $object = (new ASObjectStorage($object))->decode();
        $this->assertSame(12345, $object);
        $object = "a string";
        $object = (new ASObjectStorage($object))->decode();
        $this->assertSame('a string', $object);
        $object = '{"a":"string"}';
        $object = (new ASObjectStorage($object))->decode();
        $this->assertSame(['a' => 'string'],$object);
        $object = '';
        $object = (new ASObjectStorage($object))->decode();
        $this->assertSame('', $object);
        // This next one was produced by earlier versions of these functions. The double quotes were encoded into json
        // and a string containing two double quotes was stored. Both functions should return an empty string in
        // this case instead of a string containing two quotes - e,g, a json-encoded empty string.
        $object = '""';
        $object = (new ASObjectStorage($object))->decode();
        $this->assertSame('', $object);
        $object = null;
        $object = (new ASObjectStorage($object))->decode();
        $this->assertNull($object);
    }
}
