<?php
/*
 * Copyright (c) 2016-2017 Hubzilla
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Zotlabs\Tests\Unit\Lib;

use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Math\BigInteger;

use Zotlabs\Tests\Unit\UnitTestCase;
use Zotlabs\Lib\Keyutils;

/**
 * @brief Unit Test case for Keyutils class.
 *
 * @covers Zotlabs\Lib\Keyutils
 */

class KeyutilsTest extends UnitTestCase {

	protected function getPubPKCS1() {
		$key = '-----BEGIN RSA PUBLIC KEY-----
MIIBCgKCAQEAsSBBV5khOWvMAOIV2AhxQukBgWc1pfYqUM/9OIFfL+KaRDOFAT0y
jMbtTCbHEjxZbasS3CNnHtAgJ+4BWjtFnZl7zY4pXb2RDe4IWNnK8BjqsWDVuPks
sc+yRBTepR/50FG/xB4HfI4GRc/8EDmynyAdLDD/r6oPxmz1gMICqSGDX8yXYG4o
DliNT3bWDH+uf1+6pWsN6IFQYmUoRLly1xsfc8AV4H1GmSFrbAvQpHA95GOlxnPY
IwVth1m8O+D9SH2+0e8ourdXH3/9ccJnh6FVvFvkyeC1e2vY+J+uC0uwu+6IvHFV
pSnS8lTmwG/BBh2dA5eqV3s+REdLdS/tsQIDAQAB
-----END RSA PUBLIC KEY-----';
		return str_replace(["\r", "\n"], "\r\n", $key);
	}

	protected function getPubPKCS8() {
		$key = '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsSBBV5khOWvMAOIV2Ahx
QukBgWc1pfYqUM/9OIFfL+KaRDOFAT0yjMbtTCbHEjxZbasS3CNnHtAgJ+4BWjtF
nZl7zY4pXb2RDe4IWNnK8BjqsWDVuPkssc+yRBTepR/50FG/xB4HfI4GRc/8EDmy
nyAdLDD/r6oPxmz1gMICqSGDX8yXYG4oDliNT3bWDH+uf1+6pWsN6IFQYmUoRLly
1xsfc8AV4H1GmSFrbAvQpHA95GOlxnPYIwVth1m8O+D9SH2+0e8ourdXH3/9ccJn
h6FVvFvkyeC1e2vY+J+uC0uwu+6IvHFVpSnS8lTmwG/BBh2dA5eqV3s+REdLdS/t
sQIDAQAB
-----END PUBLIC KEY-----';
		return str_replace(["\r", "\n"], "\r\n", $key);
	}

	public function testMeToPem() {
		[$m, $e] = Keyutils::pemToMe($this->getPubPKCS8());
		$gen_key = Keyutils::meToPem($m, $e);
		self::assertEquals($this->getPubPKCS8(), $gen_key);
	}

	public function testRsaToPem() {
		$gen_key = Keyutils::rsaToPem($this->getPubPKCS1());
		self::assertEquals($gen_key, $this->getPubPKCS8());
	}

	public function testPemToRsa() {
		$gen_key = Keyutils::pemToRsa($this->getPubPKCS8());
		self::assertEquals($gen_key, $this->getPubPKCS1());
	}

	public function testPemToMe() {
		[$m, $e] = Keyutils::pemToMe($this->getPubPKCS8());

		$parsedKey = PublicKeyLoader::load([
			'e' => new BigInteger($e, 256),
			'n' => new BigInteger($m, 256)
		]);

		self::assertEquals($parsedKey->toString('PKCS8'), $this->getPubPKCS8());
	}

}
