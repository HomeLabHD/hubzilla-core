<?php

namespace Zotlabs\Tests\Unit\Lib;

use Zotlabs\Lib\Multibase;
use Zotlabs\Tests\Unit\UnitTestCase;

class MultibaseTest extends UnitTestCase {

	public function testRsaPublicKey() {
		$publicKeyPem = '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwvnkkLAnX7SThHEC9tOX
y6Y4N5DjFH4hs1q3VuRJH6NIZbIX3g7EX2yta3WXpJFrvRvfx/39+aYfxGBEP6qr
0hoa7rYDVWMs/tgsd98Zc4K6dEAlTljvATlGEuW6MDaF9qBM9SIFs0ZFXH90wStD
hW9PBFcmLpqQ8ZTa+busK0hP/k5PxrmQz18DQpRzdHK0cFK81STAmK/Rrx1uWQRs
WJvKZHWmhAVZcdDIRLciERx+W4XYFXpl57LkyP6QGpeD+6dGMzt8KR2O9kNBSuAh
jXPQMvzlsK7jLP97+780vyQvTYOCapBzCTOjuPANH0OA7XVR2iv/tVDKSeqC/+p7
dwIDAQAB
-----END PUBLIC KEY-----';

		$multibaseKey = 'z4MXj1wBzi9jUstyPkUpxMpTJw8gwWSku9DQGbjZDXXjBf1HpRQGkxugs1aRS6Zx5TvKNj1GrssZSf2Mi855Xq3b5YYSySXNcaiUFC4W6FJbseASFFJpVGpab5toa9q51A37T5zQiDaVZcgi3bsAd27ZpQGkzJn78gh2jJt3ucrYPQ4Mg7ufWkUdPAsko9MTk7hdRUteythAEZFFHb5LmsQJ5dR2a8yfsNH98LvN9iVpmF7cy53SX61gCB8kvk5AdQdzm3BgDWUsjus5BKCwLhPveiRq1cvQmisTrz4Zzc5iHWdp2vtf9qiEcKAqfGhNSsf4ZHP9rdT4p2gV8XM7RbsaTKoo3rmjn9u4QTs3ubdZRr5CMyqkc';

		$transformedKey = (new Multibase())->rsaPublicKey($publicKeyPem);

		$this->assertEquals($multibaseKey, $transformedKey);
	}

}
