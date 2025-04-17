<?php
namespace Zotlabs\Tests\Unit\Lib;

error_reporting(E_ALL);

use Zotlabs\Tests\Unit\UnitTestCase;
use Zotlabs\Lib\Activity;
use Zotlabs\Lib\ActivityStreams;
use phpmock\phpunit\PHPMock;

class ActivityTest extends UnitTestCase {
	// Import PHPMock methods into this class
	use PHPMock;

	/**
	 * Test get a textfield from an activitystreams object
	 *
	 * @dataProvider get_textfield_provider
	 */
	public function test_get_textfield(array $src, null|string|array $expected): void {
		$this->assertEquals($expected, Activity::get_textfield($src, 'content'));
	}

	/**
	 * Dataprovider for test_get_textfield.
	 */
	public static function get_textfield_provider(): array {
		return [
			'get content field' => [
				['content' => 'Some content'],
				'Some content'
			],
			'get content from map' => [
				['contentMap' => ['en' => 'Some content']],
				['en' => 'Some content']
			],
			'get not available content' => [
				['some_field' => 'Some content'],
				null
			]
		];
	}


	/**
	 *
	 * @dataProvider get_mid_and_uuid_provider
	 */
	public function test_get_mid_and_uuid(string $payload, string $mid, string $uuid): void {


		//
		// Mock z_fetch_url to prevent us from spamming real servers during test runs
		//
		// We just create some sample ActivityStreams objects to return for the various
		// URL's to make it a somewhat realistic test. Each object will have it's URL as
		// it's id and only specify the object type as laid out in the $urlmap below.

		$item_json = '{"@context":["https://www.w3.org/ns/activitystreams","https://w3id.org/security/v1","https://purl.archive.org/socialweb/webfinger",{"zot":"https://hub.somaton.com/apschema#","contextHistory":"https://w3id.org/fep/171b/contextHistory","schema":"http://schema.org#","ostatus":"http://ostatus.org#","diaspora":"https://diasporafoundation.org/ns/","litepub":"http://litepub.social/ns#","toot":"http://joinmastodon.org/ns#","commentPolicy":"zot:commentPolicy","Bookmark":"zot:Bookmark","Category":"zot:Category","Emoji":"toot:Emoji","directMessage":"litepub:directMessage","PropertyValue":"schema:PropertyValue","value":"schema:value","uuid":"schema:identifier","conversation":"ostatus:conversation","guid":"diaspora:guid","manuallyApprovesFollowers":"as:manuallyApprovesFollowers","Hashtag":"as:Hashtag"}],"type":"Note","id":"https://hub.somaton.com/item/7bb5da01-f97b-408f-853a-eb4d09079e5a","diaspora:guid":"7bb5da01-f97b-408f-853a-eb4d09079e5a","published":"2025-04-14T05:43:00Z","attributedTo":"https://hub.somaton.com/channel/mario","inReplyTo":"https://social.wake.st/users/liaizon/statuses/114332208953644534","context":"https://social.wake.st/users/liaizon/statuses/114332208953644534","content":"@<a href=\"https://social.wake.st/users/liaizon\"  target=\"_blank\"  rel=\"nofollow noopener\" >wakest \u2042</a>,<br />@<a class=\"zrl\" href=\"https://hubzilla.org/channel/info\"  target=\"_blank\"  rel=\"nofollow noopener\" >Hubzilla</a> has had <span style=\"color: magenta;\">colors</span> <span style=\"color: lightgreen;\">since</span> <span style=\"color: blue;\">10+</span> <span style=\"color: orange;\">years</span> :grinning_face_with_smiling_eyes:","source":{"content":"@[url=https://social.wake.st/users/liaizon]wakest \u2042[/url],\r\n@[zrl=https://hubzilla.org/channel/info]Hubzilla[/zrl] has had [color=magenta]colors[/color] [color=lightgreen]since[/color] [color=blue]10+[/color] [color=orange]years[/color] :grinning_face_with_smiling_eyes:","mediaType":"text/bbcode"},"actor":"https://hub.somaton.com/channel/mario","tag":[{"type":"Mention","href":"https://social.wake.st/users/liaizon","name":"@wakest \u2042"},{"type":"Mention","href":"https://hubzilla.org/channel/info","name":"@Hubzilla"},{"type":"Emoji","id":"https://hub.somaton.com/emoji/grinning_face_with_smiling_eyes","name":":grinning_face_with_smiling_eyes:","icon":{"type":"Image","url":"https://hub.somaton.com/addon/emoji/emojitwo/1f604.png"}}],"to":["https://www.w3.org/ns/activitystreams#Public","https://social.wake.st/users/liaizon","https://hubzilla.org/channel/info"],"cc":["https://social.wake.st/users/liaizon/followers","https://app.wafrn.net/fediverse/blog/wafrn"],"proof":{"type":"DataIntegrityProof","cryptosuite":"eddsa-jcs-2022","created":"2025-04-14T15:18:46Z","verificationMethod":"https://hub.somaton.com/channel/mario#z6MkfHv7DiVBDs7qZJVfbLUHLbKFYBxdhDBeqHRmhpWq8Pj9","proofPurpose":"assertionMethod","proofValue":"z5bxnZPhccvxtKZAMPxPexeqHgfzmL6U9jX6mSDbUCi3xmtLgsDbjMtAvMS2f8qw9rHBsFHyo2999xWmfPDZsCZ8U"},"signature":{"@context":["https://www.w3.org/ns/activitystreams","https://w3id.org/security/v1"],"type":"RsaSignature2017","nonce":"2fd1920c5b8906ce4d3bfbdd9ff967c10e5e37202e227fb40dc0a2a14e887093","creator":"https://hub.somaton.com/channel/mario","created":"2025-04-14T15:18:46Z","signatureValue":"Xlg0UISgRoeTfB2Qpy/UqZ4jQeNeGUVx0Gztp7uhqtj8lbQNmWyZqUzXdXf7jGjiVcy87rmdakSfcQG9Zvbak/illLePj8pkXixLWdquoyJ5v/MhDtfgEoKikGSP3mkunabNNL1yFm5uZ6dYjS4ea0lB/1YPIyWjP7NhLbv0+HD/02a9P87Nlwufh1PUFoL9Y4XPIJpparz5Wax5fIfqzmVSMa0QLN+d/zQb+/jdOszhdiTZdUgpRK4Yb8xKeRBO1kOngtOfD0I7szUdRlTmFIpi83HKRNTAJrGyFsCwTnZmIy0dHhxmyvarHyz2kuEcf8nz3z5BV8amo7edAx9wWizsRiYaiMQ65mgl6wfZapHzkUqGH7mT9Kp3YmTOTgCy9OyP7yXyUUx5MRnyqQnjzoEw6stQwNb+IuhfwRcoLwJ/sIY5db29FK3QrbMCKNvxxJUjBqq+rdUjXnpvpdm9i8X1oJ1dHtkqSNoOBleykNudxyDRjvy+uI9z6OLt3LyNorOQ+3RUxxSxONoAsb+DVuLldMfD8ASVZWMzPr2CnyAuf8EFHccCoHibiNbMRuovk+kcLd+47B+v/tOq+rk6bPQ+np323nyUYZDGrH7KYgkQuXA83E2bLd3pOFfVQjDGEJlwrSx3U7wj+DDQohN1DqIkoK7WBpU1cFI6nn0r6ak="}}';

		$urlmap = [
			'https://hub.somaton.com/channel/mario' => [ 'type' => 'Person' ],
			'https://mitra.social/users/silverpill' => [ 'type' => 'Person' ],
			'https://hub.somaton.com/item/7bb5da01-f97b-408f-853a-eb4d09079e5a' => json_decode($item_json, true)
		];

		$z_fetch_url_stub = $this->getFunctionMock('Zotlabs\Lib', 'z_fetch_url');
		$z_fetch_url_stub
			->expects($this->any())
			->willReturnCallback(function ($url) use ($urlmap) {
				if (isset($urlmap[$url])) {
					$body = json_encode(
						array_merge([ 'id' => $url ], $urlmap[$url]),
						JSON_FORCE_OBJECT,
					);

					return [
						'success' => true,
						'body' => $body,
					];
				} else {
					// We should perhaps throw an error here to fail the test,
					// as we're receiving an unexpected URL.
					return [
						'success' => false,
					];
				}
			});

		// Make sure we have a sys channel before we start
		create_sys_channel();

		$as = new ActivityStreams($payload);

		$this->assertEquals($mid, Activity::getMessageID($as));
		$this->assertEquals($uuid, Activity::getUUID($as));
	}

	/**
	 * Dataprovider for test_get_mid_and_uuid.
	 */
	public static function get_mid_and_uuid_provider(): array {
		return [
			'Note from hubzilla with diaspora:guid' => [
				'{
				  "@context":[
					"https://www.w3.org/ns/activitystreams",
					"https://w3id.org/security/v1",
					"https://purl.archive.org/socialweb/webfinger",
					{
					  "zot":"https://hub.somaton.com/apschema#",
					  "contextHistory":"https://w3id.org/fep/171b/contextHistory",
					  "schema":"http://schema.org#",
					  "ostatus":"http://ostatus.org#",
					  "diaspora":"https://diasporafoundation.org/ns/",
					  "litepub":"http://litepub.social/ns#",
					  "toot":"http://joinmastodon.org/ns#",
					  "commentPolicy":"zot:commentPolicy",
					  "Bookmark":"zot:Bookmark",
					  "Category":"zot:Category",
					  "Emoji":"toot:Emoji",
					  "directMessage":"litepub:directMessage",
					  "PropertyValue":"schema:PropertyValue",
					  "value":"schema:value",
					  "uuid":"schema:identifier",
					  "conversation":"ostatus:conversation",
					  "guid":"diaspora:guid",
					  "manuallyApprovesFollowers":"as:manuallyApprovesFollowers",
					  "Hashtag":"as:Hashtag"
					}
				  ],
				  "type":"Note",
				  "id":"https://hub.somaton.com/item/2e4b2cfa-7c20-49c2-b192-ae54f43a211a",
				  "diaspora:guid":"2e4b2cfa-7c20-49c2-b192-ae54f43a211a",
				  "published":"2025-04-03T17:45:41Z",
				  "commentPolicy":"contacts",
				  "attributedTo":"https://hub.somaton.com/channel/mario",
				  "contextHistory":"https://hub.somaton.com/conversation/2e4b2cfa-7c20-49c2-b192-ae54f43a211a",
				  "context":"https://hub.somaton.com/conversation/2e4b2cfa-7c20-49c2-b192-ae54f43a211a",
				  "content":"Looks like we have a :hubzilla: emoji now :slightly_smiling_face:",
				  "source":{
					"content":"Looks like we have a :hubzilla: emoji now :slightly_smiling_face:",
					"mediaType":"text/bbcode"
				  },
				  "actor":"https://hub.somaton.com/channel/mario",
				  "tag":[
					{
					  "type":"Emoji",
					  "id":"https://hub.somaton.com/emoji/hubzilla",
					  "name":":hubzilla:",
					  "icon":{
						"type":"Image",
						"url":"https://hub.somaton.com/images/hubzilla.svg"
					  }
					},
					{
					  "type":"Emoji",
					  "id":"https://hub.somaton.com/emoji/slightly_smiling_face",
					  "name":":slightly_smiling_face:",
					  "icon":{
						"type":"Image",
						"url":"https://hub.somaton.com/images/emoji/slightly_smiling_face.png"
					  }
					}
				  ],
				  "to":[
					"https://www.w3.org/ns/activitystreams#Public"
				  ],
				  "cc":[
					"https://hub.somaton.com/followers/mario"
				  ],
				  "proof":{
					"type":"DataIntegrityProof",
					"cryptosuite":"eddsa-jcs-2022",
					"created":"2025-04-14T15:25:17Z",
					"verificationMethod":"https://hub.somaton.com/channel/mario#z6MkfHv7DiVBDs7qZJVfbLUHLbKFYBxdhDBeqHRmhpWq8Pj9",
					"proofPurpose":"assertionMethod",
					"proofValue":"zJf7xXBtD6ZTG27171X7X1BSkw7kijw4MCvzhowL7giB5s3mUKbo9yF1wq29E3bvHc3Q7HbDzUdbFE8cpCYYH9uJ"
				  },
				  "signature":{
					"@context":[
					  "https://www.w3.org/ns/activitystreams",
					  "https://w3id.org/security/v1"
					],
					"type":"RsaSignature2017",
					"nonce":"3cda8c90fdbe708d625a017d1c946c8144fa288c9a04124b40b27f2f6a429e94",
					"creator":"https://hub.somaton.com/channel/mario",
					"created":"2025-04-14T15:25:17Z",
					"signatureValue":"d01N0pMca7I9/dCdYbwuY3/SUe0xCZfwRSPxA7w9Pj4fFYDwhCNVYLKWz66K7RP7KfDD7DQ3oS8wLxn4qSX7wjFDJhwy7PkbGUzawBc9eti+8wHbiMGD2JgZCbzGaXmR/k5zyOykKhqglUOr0BcvAfqM1g3+7UxtYdxMNFlJ9nAJTObmd5jR8RyPe9b7Tbgi5XJDJ4U4qLsb8tAK54Sr2208fuCs7T+baErMPNj4eVprWoJObnr6sQX4YJH3404eJpExMLSu+y9taWLXxg6qDv+EY/RjgbKh/cdYNB5ERDFVK4WxgrrJCTv5t7mdVxjDN3sHUsfeT1aF2JYbS5ISSdtdHZnEMNIw3uwXLm5zG76fk17nUdDfXm1Pyu2uAuwRYIBOMQWeFZvdvo1Lf457kCQQN0DgUv3t89JD7Y5fZAzOlKiqXb52cxlsNUQFw8vQnWLGZNdqpDU0np6IhABrsMo+QoYrQepwKzxnmy8cwB6KKyD8W75H49l79DslDvg71nue3MuLtIfaI+d1GhYIul9o0ttJnzTbvg6L+pLLtzwgsDCqVhkXgQmk7J8RUuux9gmqYMe0pCoDlrVcR0Jhte57JqgqYZck3BPupLuu+Pg4n5/SIAogsCCWOu4ygV7jwAOcPmze7XozBuP7CVFVpgfooo9rU3kPKeEETkcljKg="
				  }
				}',
				'https://hub.somaton.com/item/2e4b2cfa-7c20-49c2-b192-ae54f43a211a',
				'2e4b2cfa-7c20-49c2-b192-ae54f43a211a'
			],

			'Like(Note) from mitra without uuid' => [
				'{
				  "@context":[
					"https://www.w3.org/ns/activitystreams",
					"https://w3id.org/security/v1",
					"https://w3id.org/security/data-integrity/v1",
					{
					  "Emoji":"toot:Emoji",
					  "EmojiReact":"litepub:EmojiReact",
					  "Hashtag":"as:Hashtag",
					  "litepub":"http://litepub.social/ns#",
					  "sensitive":"as:sensitive",
					  "toot":"http://joinmastodon.org/ns#"
					}
				  ],
				  "actor":"https://mitra.social/users/silverpill",
				  "cc":[

				  ],
				  "id":"https://mitra.social/activities/like/01963430-e998-4bef-c903-50903dde06dc",
				  "object":"https://hub.somaton.com/item/7bb5da01-f97b-408f-853a-eb4d09079e5a",
				  "proof":{
					"created":"2025-04-14T12:05:42.945286724Z",
					"cryptosuite":"eddsa-jcs-2022",
					"proofPurpose":"assertionMethod",
					"proofValue":"z4XrZRkUBoxmAn1xYXwmhaJTXb9Mog9C3cjrPenRhqWQbfXv6QJmMyGydsQ3LqN61uVfRvis8RoTymyUPqy76k9mg",
					"type":"DataIntegrityProof",
					"verificationMethod":"https://mitra.social/users/silverpill#ed25519-key"
				  },
				  "to":[
					"https://hub.somaton.com/channel/mario",
					"https://www.w3.org/ns/activitystreams#Public"
				  ],
				  "type":"Like"
				}',
				'https://mitra.social/activities/like/01963430-e998-4bef-c903-50903dde06dc',
				''
			],

			'Like(Note) from mitra with manually added uuid' => [
				'{
				  "@context":[
					"https://www.w3.org/ns/activitystreams",
					"https://w3id.org/security/v1",
					"https://w3id.org/security/data-integrity/v1",
					{
					  "Emoji":"toot:Emoji",
					  "EmojiReact":"litepub:EmojiReact",
					  "Hashtag":"as:Hashtag",
					  "litepub":"http://litepub.social/ns#",
					  "sensitive":"as:sensitive",
					  "toot":"http://joinmastodon.org/ns#"
					}
				  ],
				  "actor":"https://mitra.social/users/silverpill",
				  "cc":[

				  ],
				  "id":"https://mitra.social/activities/like/01963430-e998-4bef-c903-50903dde06dc",
				  "uuid":"01963430-e998-4bef-c903-50903dde06dc",
				  "object":"https://hub.somaton.com/item/7bb5da01-f97b-408f-853a-eb4d09079e5a",
				  "proof":{
					"created":"2025-04-14T12:05:42.945286724Z",
					"cryptosuite":"eddsa-jcs-2022",
					"proofPurpose":"assertionMethod",
					"proofValue":"z4XrZRkUBoxmAn1xYXwmhaJTXb9Mog9C3cjrPenRhqWQbfXv6QJmMyGydsQ3LqN61uVfRvis8RoTymyUPqy76k9mg",
					"type":"DataIntegrityProof",
					"verificationMethod":"https://mitra.social/users/silverpill#ed25519-key"
				  },
				  "to":[
					"https://hub.somaton.com/channel/mario",
					"https://www.w3.org/ns/activitystreams#Public"
				  ],
				  "type":"Like"
				}',
				'https://mitra.social/activities/like/01963430-e998-4bef-c903-50903dde06dc',
				'01963430-e998-4bef-c903-50903dde06dc'
			]
		];
	}

}
