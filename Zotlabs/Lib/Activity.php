<?php

namespace Zotlabs\Lib;

use App;
use Zotlabs\Access\PermissionLimits;
use Zotlabs\Access\PermissionRoles;
use Zotlabs\Access\Permissions;
use Zotlabs\Daemon\Master;
use Zotlabs\Web\HTTPSig;
use Zotlabs\Entity\Item;

require_once('include/event.php');
require_once('include/html2plain.php');
require_once('include/items.php');
require_once('include/markdown.php');

class Activity {

	static function encode_object($x) {

		if (($x) && (!is_array($x)) && (substr(trim($x), 0, 1)) === '{') {
			$x = json_decode($x, true);
		}

		if (is_array($x)) {

			if (array_key_exists('asld', $x)) {
				return $x['asld'];
			}

			if (in_array($x['type'], ['Person', ACTIVITY_OBJ_PERSON])) {
				return self::fetch_person($x);
			}

			if (in_array($x['type'], ['Profile', ACTIVITY_OBJ_PROFILE])) {
				return self::fetch_profile($x);
			}

			if (in_array($x['type'], ['Note', 'Article', ACTIVITY_OBJ_NOTE, ACTIVITY_OBJ_ARTICLE])) {
				return self::fetch_item($x);
			}

			if ($x['type'] === ACTIVITY_OBJ_THING) {
				return self::fetch_thing($x);
			}

			if (in_array($x['type'], ['Event', ACTIVITY_OBJ_EVENT])) {
				return self::fetch_event($x);
			}

			call_hooks('encode_object', $x);
		}

		return $x;

	}

	public static function fetch_local($url, $portable_id) {
		$sql_extra = item_permissions_sql(0, $portable_id);
		$item_normal = item_normal();

		// Find the original object
		$j = q(
			"select *, id as item_id from item where mid = '%s' and item_wall = 1 $item_normal $sql_extra",
			dbesc($url)
		);

		if ($j) {
			xchan_query($j, true);
			$items = fetch_post_tags($j);

			if ($items) {
				return self::encode_item(array_shift($items));
			}
		}

		return null;
	}

	static function fetch($url, $channel = null) {

		$redirects = 0;
		if (!check_siteallowed($url)) {
			logger('blacklisted: ' . $url);
			return null;
		}
		if (!$channel) {
			$channel = get_sys_channel();
		}

		logger('fetch: ' . $url, LOGGER_DEBUG);

		if (strpos($url, 'x-zot:') === 0) {
			$x = ZotURL::fetch($url, $channel);
		}
		else {
			$m = parse_url($url);

			if (!$m) {
				return null;
			}

			// handle bearcaps
			if ($m['scheme'] === 'bear') {
				$params = explode('&', $m['query']);
				if ($params) {
					foreach ($params as $p) {
						if (substr($p, 0, 2) === 'u=') {
							$url = substr($p, 2);
						}
						if (substr($p, 0, 2) === 't=') {
							$token = substr($p, 2);
						}
					}
					$m = parse_url($url);
				}
			}

			$headers = [
				'Accept'           => ActivityStreams::get_accept_header_string($channel),
				'Host'             => $m['host'],
				'Date'             => datetime_convert('UTC', 'UTC', 'now', 'D, d M Y H:i:s \\G\\M\\T'),
				'(request-target)' => 'get ' . get_request_string($url)
			];

			if (isset($token)) {
				$headers['Authorization'] = 'Bearer ' . $token;
			}

			$h = HTTPSig::create_sig($headers, $channel['channel_prvkey'], channel_url($channel), false);
			$start_timestamp = microtime(true);
			$x = z_fetch_url($url, true, $redirects, ['headers' => $h]);
		}

		if ($x['success']) {
			$m = parse_url($url);
			if ($m) {
				$y = ['scheme' => $m['scheme'], 'host' => $m['host']];
				if (array_key_exists('port', $m))
					$y['port'] = $m['port'];
				$site_url = unparse_url($y);
				q("UPDATE site SET site_update = '%s', site_dead = 0 WHERE site_url = '%s' AND site_update < %s - INTERVAL %s",
					dbesc(datetime_convert()),
					dbesc($site_url),
					db_utcnow(),
					db_quoteinterval('1 DAY')
				);
			}

			$y = json_decode($x['body'], true);
			logger('returned: ' . json_encode($y, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOGGER_DEBUG);

			if (isset($y['type']) && ActivityStreams::is_an_actor($y['type'])) {
				logger('logger_stats_data cmd:Actor_fetch' . ' start:' . $start_timestamp . ' ' . 'end:' . microtime(true) . ' meta:' . $url . '#' . random_string(16));
				btlogger('actor fetch');
				$y['actor_cache_date'] = datetime_convert();
				XConfig::Set($y['id'], 'system', 'actor_record', $y);
			}
			else {
				logger('logger_stats_data cmd:Activity_fetch' . ' start:' . $start_timestamp . ' ' . 'end:' . microtime(true) . ' meta:' . $url . '#' . random_string(16));
				btlogger('activity fetch');
			}

			return json_decode($x['body'], true);
		}
		else {
			logger('fetch failed: ' . $url);
			logger(print_r($x, true), LOGGER_DEBUG);
		}


		return null;
	}

	static function fetch_person($x) {
		$r = q("select * from xchan where xchan_url = '%s' limit 1",
			dbesc($x['id'])
		);
		if (!$r) {
			$r = q("select * from xchan where xchan_hash = '%s' limit 1",
				dbesc($x['id'])
			);

		}
		if (!$r)
			return [];

		return self::encode_person($r[0]);
	}

	static function fetch_profile($x) {
		if (isset($x['describes'])) {
			return $x;
		}

		return [];
	}

	static function fetch_thing($x) {

		$r = q("select * from obj where obj_type = %d and obj_obj = '%s' limit 1",
			intval(TERM_OBJ_THING),
			dbesc($x['id'])
		);

		if (!$r)
			return [];

		$channel = channelx_by_n($r[0]['obj_channel']);

		$x = [
			'type' => 'Page',
			'id'   => z_root() . '/thing/' . $r[0]['obj_obj'],
			'name' => $channel['channel_name'] . ' ' . $r[0]['obj_verb'] . ' ' . $r[0]['obj_term'],
			'content' => $r[0]['obj_url'],
			'url' => $r[0]['obj_url']
		];

		if ($r[0]['obj_imgurl']) {
			$x['content'] = '<a href="' . $r[0]['obj_url'] . '"><img src="' . $r[0]['obj_imgurl'] . '" alt="' . $r[0]['obj_term'] . '"></a>';
			$x['icon'] = [
				'type' => 'Image',
				'url' => $r[0]['obj_imgurl']
			];
		}

		return $x;

	}

	static function fetch_item($x) {

		if (array_key_exists('source', $x)) {
			// This item is already processed and encoded
			return $x;
		}

		$r = q("select * from item where mid = '%s' limit 1",
			dbesc($x['id'])
		);
		if ($r) {
			xchan_query($r, true);
			$r = fetch_post_tags($r);
			if (in_array($r[0]['verb'], ['Create', 'Invite']) && in_array($r[0]['obj_type'], ['Event', ACTIVITY_OBJ_EVENT])) {
				$r[0]['verb'] = 'Invite';
				return self::encode_activity($r[0]);
			}
			return self::encode_item($r[0]);
		}
	}

	static function fetch_image($x) {

		$ret = [
			'type'      => 'Image',
			'id'        => $x['id'],
			'name'      => $x['title'],
			'content'   => bbcode($x['body'], ['cache' => true]),
			'source'    => ['mediaType' => 'text/bbcode', 'content' => $x['body']],
			'published' => datetime_convert('UTC', 'UTC', $x['created'], ATOM_TIME),
			'updated'   => datetime_convert('UTC', 'UTC', $x['edited'], ATOM_TIME),
			'url'       => [
				'type'      => 'Link',
				'mediaType' => $x['link'][0]['type'],
				'href'      => $x['link'][0]['href'],
				'width'     => $x['link'][0]['width'],
				'height'    => $x['link'][0]['height']
			]
		];
		return $ret;
	}

	static function fetch_event($x) {

		// convert old Zot event objects to ActivityStreams Event objects

		if (array_key_exists('content', $x) && array_key_exists('dtstart', $x)) {
			$ev = bbtoevent($x['content']);
			if ($ev) {


				if (!$ev['timezone']) {
					$ev['timezone'] = 'UTC';
				}

				$actor = null;
				if (array_key_exists('author', $x) && array_key_exists('link', $x['author'])) {
					$actor = $x['author']['link'][0]['href'];
				}
				$y = [
					'type'      => 'Event',
					'id'        => z_root() . '/event/' . $ev['event_hash'],
					'name'      => $ev['summary'],
					// 'summary'   => bbcode($ev['summary'], [ 'cache' => true ]),
					// RFC3339 Section 4.3
					'startTime' => (($ev['adjust']) ? datetime_convert($ev['timezone'], 'UTC', $ev['dtstart'], ATOM_TIME) : datetime_convert('UTC', 'UTC', $ev['dtstart'], 'Y-m-d\\TH:i:s-00:00')),
					'content'   => bbcode($ev['description'], ['cache' => true]),
					'location'  => ['type' => 'Place', 'content' => bbcode($ev['location'], ['cache' => true])],
					'source'    => ['content' => format_event_bbcode($ev, true), 'mediaType' => 'text/bbcode'],
					'actor'     => $actor,
				];
				if (!$ev['nofinish']) {
					$y['endTime'] = (($ev['adjust']) ? datetime_convert($ev['timezone'], 'UTC', $ev['dtend'], ATOM_TIME) : datetime_convert('UTC', 'UTC', $ev['dtend'], 'Y-m-d\\TH:i:s-00:00'));
				}

				// copy attachments from the passed object - these are already formatted for ActivityStreams

				if ($x['attachment']) {
					$y['attachment'] = $x['attachment'];
				}

				if ($actor) {
					return $y;
				}
			}
		}

		return $x;

	}

	static function paged_collection_init($total, $id, $type = 'OrderedCollection') {

		$ret = [
			'id'         => z_root() . '/' . $id,
			'type'       => $type,
			'totalItems' => $total,
		];

		$numpages = $total / App::$pager['itemspage'];
		$lastpage = (($numpages > intval($numpages)) ? intval($numpages) + 1 : $numpages);

		$ret['first'] = z_root() . '/' . App::$query_string . '?page=1';
		$ret['last']  = z_root() . '/' . App::$query_string . '?page=' . $lastpage;

		return $ret;

	}

	static function encode_item_collection($items, $id, $type, $total = 0) {

		if ($total > App::$pager['itemspage']) {
			$ret = [
				'id'   => z_root() . '/' . $id,
				'type' => $type . 'Page',
			];

			$numpages  = $total / App::$pager['itemspage'];
			$lastpage  = (($numpages > intval($numpages)) ? intval($numpages) + 1 : $numpages);
			$url_parts = parse_url($id);

			$ret['partOf'] = z_root() . '/' . $url_parts['path'];

			$extra_query_args = '';
			$query_args       = null;
			if (isset($url_parts['query'])) {
				parse_str($url_parts['query'], $query_args);
			}

			if (is_array($query_args)) {
				unset($query_args['page']);
				foreach ($query_args as $k => $v)
					$extra_query_args .= '&' . urlencode($k) . '=' . urlencode($v);
			}

			if (App::$pager['page'] < $lastpage) {
				$ret['next'] = z_root() . '/' . $url_parts['path'] . '?page=' . (intval(App::$pager['page']) + 1) . $extra_query_args;
			}
			if (App::$pager['page'] > 1) {
				$ret['prev'] = z_root() . '/' . $url_parts['path'] . '?page=' . (intval(App::$pager['page']) - 1) . $extra_query_args;
			}
		}
		else {
			$ret = [
				'id'         => z_root() . '/' . $id,
				'type'       => $type,
				'totalItems' => $total,
			];
		}

		if ($items) {
			$x = [];
			foreach ($items as $i) {
				$m = IConfig::Get($i['id'], 'activitypub', 'rawmsg');
				if ($m) {
					if (is_string($m))
						$t = json_decode($m, true);
					else
						$t = $m;
				}
				else {
					$t = self::encode_activity($i);
				}
				if ($t) {
					$x[] = $t;
				}
			}
			if ($type === 'OrderedCollection') {
				$ret['orderedItems'] = $x;
			}
			else {
				$ret['items'] = $x;
			}
		}

		return $ret;
	}

	static function encode_follow_collection($items, $id, $type, $extra = null) {

		$ret = [
			'id'         => z_root() . '/' . $id,
			'type'       => $type,
			'totalItems' => count($items),
		];
		if ($extra)
			$ret = array_merge($ret, $extra);

		if ($items) {
			$x = [];
			foreach ($items as $i) {
				if ($i['xchan_url']) {
					$x[] = $i['xchan_url'];
				}
			}

			if ($type === 'OrderedCollection')
				$ret['orderedItems'] = $x;
			else
				$ret['items'] = $x;
		}

		return $ret;
	}

	static function encode_simple_collection($items, $id, $type, $total = 0, $extra = null) {

		$ret = [
			'id'         => z_root() . '/' . $id,
			'type'       => $type,
			'totalItems' => $total,
		];

		if ($extra) {
			$ret = array_merge($ret, $extra);
		}

		if ($items) {
			if ($type === 'OrderedCollection') {
				$ret['orderedItems'] = $items;
			}
			else {
				$ret['items'] = $items;
			}
		}

		return $ret;
	}

	static function encode_item($i) {

		$ret = [];

		$objtype = self::activity_obj_mapper($i['obj_type']);

		if (intval($i['item_deleted'])) {
			$ret['type']       = 'Tombstone';
			$ret['formerType'] = $objtype;
			$ret['id']         = $i['mid'];
			if ($i['id'] != $i['parent'])
				$ret['inReplyTo'] = $i['thr_parent'];

			$ret['to'] = [ACTIVITY_PUBLIC_INBOX];
			return $ret;
		}

		if (isset($i['obj']) && $i['obj']) {
			if (is_array($i['obj'])) {
				$ret = $i['obj'];
			}
			else {
				$ret = json_decode($i['obj'], true);
			}
		}

		$ret['type'] = $objtype;

		if ($objtype === 'Question') {
			if ($i['obj']) {
				if (is_array($i['obj'])) {
					$ret = $i['obj'];
				}
				else {
					$ret = json_decode($i['obj'], true);
				}

				if (array_path_exists('actor/id', $ret)) {
					$ret['actor'] = $ret['actor']['id'];
				}
			}
		}

		$ret['id']            = ((strpos($i['mid'], 'http') === 0) ? $i['mid'] : z_root() . '/item/' . urlencode($i['mid']));
		$ret['diaspora:guid'] = $i['uuid'];

		$images = [];
		$audios = [];
		$videos = [];

		$has_images = preg_match_all('/\[[zi]mg(.*?)](.*?)\[/ism', $i['body'], $images, PREG_SET_ORDER);
		$has_audios = preg_match_all('/\[zaudio](.*?)\[/ism', $i['body'], $audios, PREG_SET_ORDER);
		$has_videos = preg_match_all('/\[zvideo](.*?)\[/ism', $i['body'], $videos, PREG_SET_ORDER);

		// provide ocap access token for private media.
		// set this for descendants even if the current item is not private
		// because it may have been relayed from a private item.

		$token = IConfig::Get($i, 'ocap', 'relay');
		$matches_processed = [];

		if ($token && $has_images) {
			for ($n = 0; $n < count($images); $n++) {
				$match = $images[$n];
				if (str_starts_with($match[1], '=http') && str_contains($match[1], z_root() . '/photo/') && !in_array($match[1], $matches_processed)) {
					$i['body'] = str_replace($match[1], $match[1] . '?token=' . $token, $i['body']);
					$images[$n][2] = substr($match[1], 1) . '?token=' . $token;
					$matches_processed[] = $match[1];
				} elseif (str_contains($match[2], z_root() . '/photo/') && !in_array($match[2], $matches_processed)) {
					$i['body'] = str_replace($match[2], $match[2] . '?token=' . $token, $i['body']);
					$images[$n][2] = $match[2] . '?token=' . $token;
					$matches_processed[] = $match[2];
				}
			}
		}

		if ($token && $has_audios) {
			for ($n = 0; $n < count($audios); $n++) {
				$match = $audios[$n];
				if (str_contains($match[1], z_root() . '/attach/') && !in_array($match[1], $matches_processed)) {
					$i['body'] = str_replace($match[1], $match[1] . '?token=' . $token, $i['body']);
					$audios[$n][1] = $match[1] . '?token=' . $token;
					$matches_processed[] = $match[1];
				}
			}
		}

		if ($token && $has_videos) {
			for ($n = 0; $n < count($videos); $n++) {
				$match = $videos[$n];
				if (str_contains($match[1], z_root() . '/attach/') && !in_array($match[1], $matches_processed)) {
					$i['body'] = str_replace($match[1], $match[1] . '?token=' . $token, $i['body']);
					$videos[$n][1] = $match[1] . '?token=' . $token;
					$matches_processed[] = $match[1];
				}
			}
		}

		if ($i['title'])
			$ret['name'] = unescape_tags($i['title']);

		$ret['published'] = datetime_convert('UTC', 'UTC', $i['created'], ATOM_TIME);
		if ($i['created'] !== $i['edited'])
			$ret['updated'] = datetime_convert('UTC', 'UTC', $i['edited'], ATOM_TIME);
		if ($i['expires'] > NULL_DATE) {
			$ret['expires'] = datetime_convert('UTC', 'UTC', $i['expires'], ATOM_TIME);
		}

		if ($i['app']) {
			$ret['generator'] = ['type' => 'Application', 'name' => $i['app']];
		}
		if ($i['location'] || $i['coord']) {
			$ret['location'] = ['type' => 'Place'];
			if ($i['location']) {
				$ret['location']['name'] = $i['location'];
			}
			if ($i['coord']) {
				$l                            = explode(' ', $i['coord']);
				$ret['location']['latitude']  = $l[0];
				$ret['location']['longitude'] = $l[1];
			}
		}

		if (intval($i['item_private']) === 2) {
			$ret['directMessage'] = true;
		}

		$ret['commentPolicy'] = (($i['item_wall']) ? map_scope(PermissionLimits::Get($i['uid'], 'post_comments')) : '');

		if (array_key_exists('comments_closed', $i) && $i['comments_closed'] !== EMPTY_STR && $i['comments_closed'] > NULL_DATE) {
			if ($ret['commentPolicy']) {
				$ret['commentPolicy'] .= ' ';
			}
			$ret['commentPolicy'] .= 'until=' . datetime_convert('UTC', 'UTC', $i['comments_closed'], ATOM_TIME);
		}

		$ret['attributedTo'] = self::encode_person($i['author'], false);

		if ($i['mid'] !== $i['parent_mid']) {
			$ret['inReplyTo'] = ((strpos($i['thr_parent'], 'http') === 0) ? $i['thr_parent'] : z_root() . '/item/' . urlencode($i['thr_parent']));

			$cnv = IConfig::Get($i['parent'], 'activitypub', 'context');
			if (!$cnv) {
				$cnv = $i['parent_mid'];
			}
		}

		if (empty($cnv)) {
			$cnv = IConfig::Get($i, 'activitypub', 'context');
			if (!$cnv) {
				$cnv = $i['parent_mid'];
			}
		}

		if (!empty($cnv)) {
			if (is_string($cnv) && str_starts_with($cnv, z_root())) {
				$cnv = str_replace(['/item/', '/activity/'], ['/conversation/', '/conversation/'], $cnv);
				$ret['contextHistory'] = $cnv;
			}
			$ret['context'] = $cnv;
		}

		$actor = self::encode_person($i['author'], false);
		if ($actor)
			$ret['actor'] = $actor;
		else
			return [];

		$t = self::encode_taxonomy($i);
		if ($t) {
			foreach($t as $tag) {
				if (strcasecmp($tag['name'], '#nsfw') === 0 || strcasecmp($tag['name'], '#sensitive') === 0) {
					$ret['sensitive'] = true;
				}
			}

			$ret['tag'] = $t;
		}

		if (str_contains($i['body'], '[/share]')) {
			preg_match_all('/\[share(.*?)\[\/share\]/ism', $i['body'], $all_shares, PREG_SET_ORDER);

			$quote_urls = [];

			foreach ($all_shares as $share) {
				// Extract the link attribute from each [share] block
				if (preg_match("/link='(.*?)'/ism", $share[1], $match)) {
					$url = $match[1];
					$quote_urls[] = $url;

					$quote_name = 'RE: ' . $url;

					// Replace this share block with a formatted URL reference
					$i['body'] = str_replace($share[0], $quote_name, $i['body']);

					$obj_links[] = [
						'type' => 'Link',
						'mediaType' => 'application/ld+json; profile="https://www.w3.org/ns/activitystreams"',
						'href' => $url,
						'name' => $quote_name
					];
				}
			}

			if ($quote_urls) {
				$ret['quoteUrl'] = $quote_urls[0];

				if (empty($ret['tag'])) {
					$ret['tag'] = $obj_links;
				}
				else {
					$ret['tag'] = array_merge($ret['tag'], $obj_links);
				}
			}

		}

		if ($i['mimetype'] === 'text/bbcode') {
			if ($i['title'])
				$ret['name'] = unescape_tags($i['title']);
			if ($i['summary'])
				$ret['summary'] = unescape_tags($i['summary']);
			$ret['content'] = bbcode(unescape_tags($i['body']));
			$ret['source']  = ['content' => unescape_tags($i['body']), 'mediaType' => 'text/bbcode'];
		}

		$a = self::encode_attachment($i);
		if ($a) {
			$ret['attachment'] = $a;
		}
/*
		if ($i['target']) {
			if (is_string($i['target'])) {
				$tmp = json_decode($i['target'], true);
				if ($tmp !== null) {
					$i['target'] = $tmp;
				}
			}
			$tgt = self::encode_object($i['target']);
			if ($tgt) {
				$ret['target'] = $tgt;
			}
		}
*/
		if (intval($i['item_private']) === 0) {
			$ret['to'] = [ACTIVITY_PUBLIC_INBOX];
		}

		$hookinfo = [
			'item' => $i,
			'encoded' => $ret
		];

		call_hooks('encode_item', $hookinfo);

		return $hookinfo['encoded'];


	}

	static function decode_taxonomy($item) {

		$ret = [];

		if (array_key_exists('tag', $item) && is_array($item['tag'])) {
			$ptr = $item['tag'];
			if (!array_key_exists(0, $ptr)) {
				$ptr = [$ptr];
			}

			foreach ($ptr as $t) {
				if (is_array($t) && !array_key_exists('type', $t))
					$t['type'] = 'Hashtag';

				if (is_array($t) && (array_key_exists('href', $t) || array_key_exists('id', $t) || isset($t['icon']['url'])) && array_key_exists('name', $t)) {
					switch ($t['type']) {
						case 'Hashtag':
							$ret[] = ['ttype' => TERM_HASHTAG, 'url' => $t['href'], 'term' => escape_tags((substr($t['name'], 0, 1) === '#') ? substr($t['name'], 1) : $t['name'])];
							break;

						case 'Mention':
							$ret[] = ['ttype' => TERM_MENTION, 'url' => $t['href'], 'term' => escape_tags((substr($t['name'], 0, 1) === '@') ? substr($t['name'], 1) : $t['name'])];
							break;

						case 'Bookmark':
							$ret[] = ['ttype' => TERM_BOOKMARK, 'url' => $t['href'], 'term' => escape_tags($t['name'])];
							break;

						case 'Emoji':
							$ret[] = ['ttype' => TERM_EMOJI, 'url' => $t['id'] ?? $t['icon']['url'], 'term' => escape_tags($t['name']), 'imgurl' => $t['icon']['url']];
							break;

						default:
							break;
					}
				}
			}
		}

		return $ret;
	}

	static function encode_taxonomy($item) {

		$ret = [];

		if (array_key_exists('term', $item) && is_array($item['term'])) {
			foreach ($item['term'] as $t) {
				switch ($t['ttype']) {
					case TERM_HASHTAG:
						// href is required so if we don't have a url in the taxonomy, ignore it and keep going.
						if ($t['url']) {
							$ret[] = ['type' => 'Hashtag', 'href' => $t['url'], 'name' => '#' . $t['term']];
						}
						break;

					case TERM_MENTION:
						$ret[] = ['type' => 'Mention', 'href' => $t['url'], 'name' => '@' . $t['term']];
						break;

					case TERM_BOOKMARK:
						$ret[] = ['type' => 'Bookmark', 'href' => $t['url'], 'name' => $t['term']];
						break;

					case TERM_EMOJI:
						$ret[] = ['type' => 'Emoji', 'id' => $t['url'], 'name' => $t['term'], 'icon' => ['type' => 'Image', 'url' => $t['imgurl']]];
						break;

					default:
						break;
				}
			}
		}

		return $ret;
	}

	static function encode_attachment($item, $iconfig = false) {

		$ret = [];

		$token = IConfig::Get($item, 'ocap', 'relay');

		if (!$iconfig && array_key_exists('attach', $item)) {
			$atts = ((is_array($item['attach'])) ? $item['attach'] : json_decode($item['attach'], true));
			if ($atts) {
				foreach ($atts as $att) {
					if (!isset($att['type'], $att['href'])) {
						continue;
					}

					if (str_starts_with($att['type'], 'image')) {
						$ret[] = ['type' => 'Image', 'mediaType' => $att['type'], 'name' => $att['title'], 'url' => $att['href'] . (($token) ? '?token=' . $token : '')];
					}
					elseif (str_starts_with($att['type'], 'audio')) {
						$ret[] = ['type' => 'Audio', 'mediaType' => $att['type'], 'name' => $att['title'], 'url' => $att['href'] . (($token) ? '?token=' . $token : '')];
					}
					elseif (str_starts_with($att['type'], 'video')) {
						$ret[] = ['type' => 'Video', 'mediaType' => $att['type'], 'name' => $att['title'], 'url' => $att['href'] . (($token) ? '?token=' . $token : '')];
					}
					else {
						$ret[] = ['type' => 'Link', 'mediaType' => $att['type'], 'name' => $att['title'], 'href' => $att['href'] . (($token) ? '?token=' . $token : '')];
					}
				}
			}
		}
		if ($iconfig && array_key_exists('iconfig', $item) && is_array($item['iconfig'])) {
			foreach ($item['iconfig'] as $att) {
				if ($att['sharing']) {
					$value = ((is_string($att['v']) && preg_match('|^a:[0-9]+:{.*}$|s', $att['v'])) ? unserialize($att['v']) : $att['v']);
					$ret[] = ['type' => 'PropertyValue', 'name' => 'zot.' . $att['cat'] . '.' . $att['k'], 'value' => $value];
				}
			}
		}

		return $ret;
	}

	static function decode_iconfig($item) {

		$ret = [];

		if (isset($item['attachment']) && is_array($item['attachment'])) {
			$ptr = $item['attachment'];
			if (!array_key_exists(0, $ptr)) {
				$ptr = [$ptr];
			}
			foreach ($ptr as $att) {
				$entry = [];
				if (isset($att['type']) && $att['type'] === 'PropertyValue') {
					if (isset($att['name'])) {
						$key = explode('.', $att['name']);
						if (count($key) === 3 && $key[0] === 'zot') {
							$entry['cat']     = $key[1];
							$entry['k']       = $key[2];
							$entry['v']       = $att['value'];
							$entry['sharing'] = '1';
							$ret[]            = $entry;
						}
					}
				}
			}
		}

		return $ret;
	}

	public static function decode_attachment($item) {

		$ret = [];

		if (isset($item['attachment']) && is_array($item['attachment'])) {
			$ptr = $item['attachment'];
			if (!array_key_exists(0, $ptr)) {
				$ptr = [$ptr];
			}
			foreach ($ptr as $att) {
				if (!is_array($att)) {
					continue;
				}

				$entry = [];

				if (array_key_exists('href', $att) && $att['href']) {
					$entry['href'] = $att['href'];
				} elseif (array_key_exists('url', $att) && $att['url']) {
					$entry['href'] = $att['url'];
				}
				if (array_key_exists('mediaType', $att) && $att['mediaType']) {
					$entry['type'] = $att['mediaType'];
				} elseif (array_key_exists('type', $att) && $att['type'] === 'Image') {
					$entry['type'] = 'image/jpeg';
				} elseif (array_key_exists('type', $att) && $att['type'] === 'Link') {
					$entry['type'] = 'text/uri-list';
				}
				if (array_key_exists('name', $att) && $att['name']) {
					$entry['name'] = html2plain(purify_html($att['name']), 256);
				}
				// Friendica attachments don't match the URL in the body.
				// This makes it more difficult to detect image duplication in bb_attach()
				// which adds images to plaintext microblog software. For these we need to examine both the
				// url and image properties.
				if (isset($att['image']) && is_string($att['image']) && isset($att['url']) && $att['image'] !== $att['url']) {
					$entry['image'] = $att['image'];
				}
				if ($entry) {
					array_unshift($ret, $entry);
				}
			}
		} elseif (isset($item['attachment']) && is_string($item['attachment'])) {
			btlogger('not an array: ' . $item['attachment']);
		}

		return $ret;
	}

	static function encode_activity($i, $recurse = false) {

		$ret   = [];
		$reply = false;

		$ret['type'] = self::activity_mapper($i['verb']);

		if ((isset($i['item_deleted']) && intval($i['item_deleted'])) && !$recurse) {

			if ($i['verb'] === 'Add' && str_contains($i['tgt_type'], 'Collection')) {
				$ret['id'] = str_replace('/item/', '/activity/', $i['mid']) . '#Remove';
				$ret['type'] = 'Remove';
				if (is_string($i['obj'])) {
					$obj = json_decode($i['obj'], true);
				}
				elseif(is_array($i['obj'])) {
					$obj = $i['obj'];
				}
				if (isset($obj['id'])) {
					$ret['object'] = $obj['id'];
				}
				else {
					$ret['object'] = str_replace('/item/', '/activity/', $i['mid']);
				}
				$ret['target'] = is_array($i['target']) ? $i['target'] : json_decode($i['target'], true);

				return $ret;
			}

			$is_response = ActivityStreams::is_response_activity($ret['type']);

			if ($is_response) {
				$ret['type'] = 'Undo';
				$fragment    = 'undo';
			}
			else {
				$ret['type'] = 'Delete';
				$fragment    = 'delete';
			}

			$ret['id'] = str_replace('/item/', '/activity/', $i['mid']) . '#' . $fragment;
			$actor     = self::encode_person($i['author'], false);
			if ($actor)
				$ret['actor'] = $actor;
			else
				return [];

			$obj = (($is_response) ? self::encode_activity($i, true) : self::encode_item($i));
			if ($obj) {
				if (array_path_exists('object/id', $obj)) {
					$obj['object'] = $obj['object']['id'];
				}
				if (isset($obj['cc'])) {
					unset($obj['cc']);
				}
				$obj['to']     = [ACTIVITY_PUBLIC_INBOX];
				$ret['object'] = $obj;
			}
			else
				return [];

			$ret['to'] = [ACTIVITY_PUBLIC_INBOX];

			return $ret;

		}

		if ($ret['type'] === 'EmojiReact') {
			$ret['content'] = $i['body'];
		}

		if (strpos($i['mid'], z_root() . '/item/') !== false) {
			$ret['id'] = str_replace('/item/', '/activity/', $i['mid']);
		}
		elseif (strpos($i['mid'], z_root() . '/event/') !== false) {
			$ret['id'] = str_replace('/event/', '/activity/', $i['mid']);
		}
		else {
			$ret['id'] = ((strpos($i['mid'], 'http') === 0) ? $i['mid'] : z_root() . '/activity/' . urlencode($i['mid']));
		}

		$ret['diaspora:guid'] = $i['uuid'];

		if (!empty($i['title']))
			$ret['name'] = html2plain(bbcode($i['title']));

		if (!empty($i['summary']))
			$ret['summary'] = bbcode($i['summary']);

		if ($ret['type'] === 'Announce') {
			$tmp            = preg_replace('/\[share(.*?)\[\/share\]/ism', EMPTY_STR, $i['body']);
			$ret['content'] = bbcode($tmp);
			$ret['source']  = [
				'content'   => $i['body'],
				'mediaType' => 'text/bbcode'
			];
		}

		$ret['published'] = datetime_convert('UTC', 'UTC', $i['created'], ATOM_TIME);

		if (isset($i['created'], $i['edited']) && $i['created'] !== $i['edited']) {
			$ret['updated'] = datetime_convert('UTC', 'UTC', $i['edited'], ATOM_TIME);
			if ($ret['type'] === 'Create') {
				$ret['type'] = 'Update';
			}
		}

		if (!empty($i['app'])) {
			$ret['generator'] = ['type' => 'Application', 'name' => $i['app']];
		}
		if (!empty($i['location']) || !empty($i['coord'])) {
			$ret['location'] = ['type' => 'Place'];
			if ($i['location']) {
				$ret['location']['name'] = $i['location'];
			}
			if ($i['coord']) {
				$l                            = explode(' ', $i['coord']);
				$ret['location']['latitude']  = $l[0];
				$ret['location']['longitude'] = $l[1];
			}
		}

		if ($i['mid'] !== $i['parent_mid']) {
			$reply = true;

			// inReplyTo needs to be set in the activity for followup actions (Like, Dislike, Announce, etc.),
			// but *not* for comments and RSVPs, where it should only be present in the object

			if (!in_array($ret['type'], ['Create', 'Update', 'Add', 'Remove', 'Accept', 'Reject', 'TentativeAccept', 'TentativeReject'])) {
				$ret['inReplyTo'] = ((strpos($i['thr_parent'], 'http') === 0) ? $i['thr_parent'] : z_root() . '/item/' . urlencode($i['thr_parent']));
			}

			$cnv = IConfig::Get($i['parent'], 'activitypub', 'context');
			if (!$cnv) {
				$cnv = $i['parent_mid'];
			}
		}

		if (empty($cnv)) {
			$cnv = IConfig::Get($i, 'activitypub', 'context');
			if (!$cnv) {
				$cnv = $i['parent_mid'];
			}
		}

		if (!empty($cnv)) {
			if (is_string($cnv) && str_starts_with($cnv, z_root())) {
				$cnv = str_replace(['/item/', '/activity/'], ['/conversation/', '/conversation/'], $cnv);
				$ret['contextHistory'] = $cnv;
			}
			$ret['context'] = $cnv;
		}

		$actor = self::encode_person($i['author'], false);
		if ($actor)
			$ret['actor'] = $actor;
		else
			return [];

		if (!empty($i['obj'])) {
			if (!is_array($i['obj'])) {
				$i['obj'] = json_decode($i['obj'], true);
			}
			if (in_array($i['obj']['type'], ['Image', ACTIVITY_OBJ_PHOTO])) {
				$i['obj']['id'] = $i['mid'];
			}

			$obj = self::encode_object($i['obj']);

			if ($obj) {
				$ret['object'] = $obj;
			}
			else
				return [];
		}
		else {
			$obj = self::encode_item($i);
			if ($obj)
				$ret['object'] = $obj;
			else
				return [];
		}

		if (array_path_exists('object/type', $ret) && $ret['object']['type'] === 'Event' && $ret['type'] === 'Create') {
			$ret['type'] = 'Invite';
		}

		if (!empty($i['target'])) {
			if (!is_array($i['target'])) {
				$i['target'] = json_decode($i['target'], true);
			}
			$tgt = self::encode_object($i['target']);
			if ($tgt)
				$ret['target'] = $tgt;
			else
				return [];
		}

		$t = self::encode_taxonomy($i);
		if ($t) {
			$ret['tag'] = $t;
		}

		$a = self::encode_attachment($i, true);
		if ($a) {
			$ret['attachment'] = $a;
		}

		if (intval($i['item_private']) === 0) {
			$ret['to'] = [ACTIVITY_PUBLIC_INBOX];
		}

		$hookinfo = [
			'item' => $i,
			'encoded' => $ret
		];

		call_hooks('encode_activity', $hookinfo);

		return $hookinfo['encoded'];

	}

	// Returns an array of URLS for any mention tags found in the item array $i.
	static function map_mentions($i) {

		$list = [];

		if (array_key_exists('term', $i) && is_array($i['term'])) {
			foreach ($i['term'] as $t) {
				if (!$t['url']) {
					continue;
				}
				if ($t['ttype'] == TERM_MENTION) {
					$url    = self::lookup_term_url($t['url']);
					$list[] = (($url) ? $url : $t['url']);
				}
			}
		}

		return $list;
	}

	// Returns an array of all recipients targeted by private item array $i.
	static function map_acl($i) {
		$ret = [];

		if (!$i['item_private']) {
			return $ret;
		}

		if ($i['allow_gid']) {
			$tmp = expand_acl($i['allow_gid']);
			if ($tmp) {
				foreach ($tmp as $t) {
					$ret[] = z_root() . '/lists/' . $t;
				}
			}
		}

		if ($i['allow_cid']) {
			$tmp  = expand_acl($i['allow_cid']);
			$list = stringify_array($tmp, true);
			if ($list) {
				$details = q("select hubloc_id_url, hubloc_hash, hubloc_network from hubloc where hubloc_hash in (" . $list . ") and hubloc_id_url != '' and hubloc_deleted = 0");
				if ($details) {
					foreach ($details as $d) {
						if ($d['hubloc_network'] === 'activitypub') {
							$ret[] = $d['hubloc_hash'];
						} else {
							$ret[] = $d['hubloc_id_url'];
						}
					}
				}
			}
		}

		return $ret;
	}

	static function lookup_term_url($url) {

		// The xchan_url for mastodon is a text/html rendering. This is called from map_mentions where we need
		// to convert the mention url to an ActivityPub id. If this fails for any reason, return the url we have

		$r = q("select hubloc_network, hubloc_hash, hubloc_id_url from hubloc where hubloc_id_url = '%s' limit 1",
			dbesc($url)
		);

		if ($r) {
			if ($r[0]['hubloc_network'] === 'activitypub') {
				return $r[0]['hubloc_hash'];
			}
			return $r[0]['hubloc_id_url'];
		}

		return $url;
	}

	static function encode_person($p, $extended = true) {

		$ret = (($extended) ? [] : '');

		if (!is_array($p)) {
			return $ret;
		}

		$c = ((array_key_exists('channel_id', $p)) ? $p : channelx_by_hash($p['xchan_hash']));

		$id = (($c) ? channel_url($c) : ((filter_var($p['xchan_hash'], FILTER_VALIDATE_URL)) ? $p['xchan_hash'] : $p['xchan_url']));

		if (!$id) {
			return $ret;
		}

		if (!$extended) {
			return $id;
		}

		$ret['type'] = 'Person';

		if ($c) {
			if (PConfig::Get($c['channel_id'], 'system', 'group_actor')) {
				$ret['type'] = 'Group';
			}

			$ret['manuallyApprovesFollowers'] = ((PConfig::Get($c['channel_id'], 'system', 'autoperms')) ? false : true);
		}

		$ret['id'] = $id;

		$ret['preferredUsername'] = (($c) ? $c['channel_address'] : substr($p['xchan_addr'], 0, strpos($p['xchan_addr'], '@')));

		$ret['name']    = $p['xchan_name'];
		$ret['updated'] = datetime_convert('UTC', 'UTC', $p['xchan_name_date'], ATOM_TIME);
		$ret['icon']    = [
			'type'      => 'Image',
			'mediaType' => (($p['xchan_photo_mimetype']) ? $p['xchan_photo_mimetype'] : 'image/png'),
			'updated'   => datetime_convert('UTC', 'UTC', $p['xchan_photo_date'], ATOM_TIME),
			'url'       => $p['xchan_photo_l'] . '?rev=' . strtotime($p['xchan_photo_date']),
			'height'    => 300,
			'width'     => 300,
		];

/* This could be used to distinguish actors by protocol instead of tags,
 * array urls are not supported by some AP projects (pixelfed) though.
 *
		$ret['url'] = [
			[
				'type' => 'Link',
				'rel'  => 'alternate',
				'mediaType' => 'application/x-zot+json',
				'href' => $p['xchan_url']
			],
			[
				'type' => 'Link',
				'rel'  => 'alternate',
				'mediaType' => 'application/activity+json',
				'href' => $p['xchan_url']
			],
			[
				'type' => 'Link',
				'rel'  => 'alternate', // 'me'?
				'mediaType' => 'text/html',
				'href' => $p['xchan_url']
			]
		];
*/

		$ret['url'] = $id;

		$ret['publicKey'] = [
			'id'                 => $id,
			'owner'              => $id,
			'signatureAlgorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
			'publicKeyPem'       => $p['xchan_pubkey']
		];

		if ($c) {
			$ret['tag'][] = [
				'type' => 'PropertyValue',
				'name' => 'Protocol',
				'value' => 'zot6'
			];

			$ret['outbox'] = z_root() . '/outbox/' . $c['channel_address'];
		}

		$arr = [
			'xchan'   => $p,
			'encoded' => $ret
		];

		call_hooks('encode_person', $arr);

		$ret = $arr['encoded'];

		return $ret;
	}

	static function encode_item_object($item, $elm = 'obj') {
		$ret = [];

		if ($item[$elm]) {
			if (!is_array($item[$elm])) {
				$item[$elm] = json_decode($item[$elm], true);
			}
			if (in_array($item[$elm]['type'], ['Image', ACTIVITY_OBJ_PHOTO])) {
				$item[$elm]['id'] = $item['mid'];
			}

			$obj = self::encode_object($item[$elm]);
			if ($obj)
				return $obj;
			else
				return [];
		}
		else {
			$obj = self::encode_item($item);
			if ($obj)
				return $obj;
			else
				return [];
		}

	}


	static function activity_mapper($verb) {

		if (strpos($verb, '/') === false) {
			return $verb;
		}

		$acts = [
			'http://activitystrea.ms/schema/1.0/post'           => 'Create',
			//'http://activitystrea.ms/schema/1.0/share'          => 'Announce',
			'http://activitystrea.ms/schema/1.0/update'         => 'Update',
			'http://activitystrea.ms/schema/1.0/like'           => 'Like',
			'http://activitystrea.ms/schema/1.0/favorite'       => 'Like',
			'http://purl.org/zot/activity/dislike'              => 'Dislike',
			//'http://activitystrea.ms/schema/1.0/tag'            => 'Add',
			'http://activitystrea.ms/schema/1.0/follow'         => 'Follow',
			'http://activitystrea.ms/schema/1.0/unfollow'       => 'Unfollow',
			'http://activitystrea.ms/schema/1.0/stop-following' => 'Unfollow',
			'http://purl.org/zot/activity/attendyes'            => 'Accept',
			'http://purl.org/zot/activity/attendno'             => 'Reject',
			'http://purl.org/zot/activity/attendmaybe'          => 'TentativeAccept',
		];

		call_hooks('activity_mapper', $acts);

		if (array_key_exists($verb, $acts) && $acts[$verb]) {
			return $acts[$verb];
		}

		// We should return false, however this will trigger an uncaught execption  and crash
		// the delivery system if encountered by the JSON-LDSignature library

		logger('Unmapped activity: ' . $verb);
		return 'Create';
		//	return false;
	}

	static function activity_obj_mapper($obj) {

		$objs = [
			'http://activitystrea.ms/schema/1.0/note'          => 'Note',
			'http://activitystrea.ms/schema/1.0/comment'       => 'Note',
			'http://activitystrea.ms/schema/1.0/person'        => 'Person',
			'http://purl.org/zot/activity/profile'             => 'Profile',
			'http://activitystrea.ms/schema/1.0/photo'         => 'Image',
			'http://activitystrea.ms/schema/1.0/profile-photo' => 'Icon',
			'http://activitystrea.ms/schema/1.0/event'         => 'Event',
			'http://purl.org/zot/activity/location'            => 'Place',
			'http://purl.org/zot/activity/chessgame'           => 'Game',
			'http://purl.org/zot/activity/tagterm'             => 'zot:Tag',
			'http://purl.org/zot/activity/thing'               => 'Object',
			'http://purl.org/zot/activity/file'                => 'zot:File',
			'http://purl.org/zot/activity/mood'                => 'zot:Mood'
		];

		call_hooks('activity_obj_mapper', $objs);

		if ($obj === 'Answer') {
			return 'Note';
		}

		if (strpos($obj, '/') === false) {
			return $obj;
		}


		if (array_key_exists($obj, $objs)) {
			return $objs[$obj];
		}

		logger('Unmapped activity object: ' . $obj);
		return 'Note';

		//	return false;

	}

	static function follow($channel, $act) {

		$contact         = null;
		$their_follow_id = null;

		/*
		 *
		 * if $act->type === 'Follow', actor is now following $channel
		 * if $act->type === 'Accept', actor has approved a follow request from $channel
		 *
		 */

		if (in_array($act->type, ['Follow', 'Invite', 'Join'])) {
			$their_follow_id = $act->id;
		}

		$person_obj = (($act->type == 'Invite') ? $act->obj : $act->actor);

		if (is_array($person_obj)) {

			// store their xchan and hubloc

			self::actor_store($person_obj);

			// Find any existing abook record

			$r = q("select * from abook left join xchan on abook_xchan = xchan_hash where abook_xchan = '%s' and abook_channel = %d limit 1",
				dbesc($person_obj['id']),
				intval($channel['channel_id'])
			);
			if ($r) {
				$contact = $r[0];
			}
		}

		$role = PConfig::Get($channel['channel_id'], 'system', 'permissions_role', 'personal');
		$x = PermissionRoles::role_perms($role);
		$their_perms = Permissions::FilledPerms($x['perms_connect']);

		if ($contact && $contact['abook_id']) {

			// A relationship of some form already exists on this site.

			switch ($act->type) {

				case 'Follow':
				case 'Invite':
				case 'Join':

					// A second Follow request, but we haven't approved the first one
					if ($contact['abook_pending']) {
						return;
					}

					// We've already approved them or followed them first
					// Send an Accept back to them
					AbConfig::Set($channel['channel_id'], $person_obj['id'], 'pubcrawl', 'their_follow_id', $their_follow_id);
					Master::Summon(['Notifier', 'permission_accept', $contact['abook_id']]);
					return;

				case 'Accept':
					// They accepted our Follow request.
					// Set default permissions except for send_stream and post_wall

					foreach ($their_perms as $k => $v) {
						if(in_array($k, ['send_stream', 'post_wall'])) {
							continue; // Those will be set once we accept their follow request
						}
						AbConfig::Set($channel['channel_id'], $contact['abook_xchan'], 'their_perms', $k, $v);
					}

					$abook_instance = $contact['abook_instance'];

					if (strpos($abook_instance, z_root()) === false) {
						if ($abook_instance)
							$abook_instance .= ',';
						$abook_instance .= z_root();

						q("update abook set abook_instance = '%s', abook_not_here = 0
							where abook_id = %d and abook_channel = %d",
							dbesc($abook_instance),
							intval($contact['abook_id']),
							intval($channel['channel_id'])
						);
					}

					return;
				default:
					return;

			}
		}

		// No previous relationship exists.

		if ($act->type === 'Accept') {
			// This should not happen unless we deleted the connection before it was accepted.
			return;
		}

		// From here on out we assume a Follow activity to somebody we have no existing relationship with

		AbConfig::Set($channel['channel_id'], $person_obj['id'], 'pubcrawl', 'their_follow_id', $their_follow_id);

		// The xchan should have been created by actor_store() above

		$r = q("select * from xchan where xchan_hash = '%s' and xchan_network = 'activitypub' limit 1",
			dbesc($person_obj['id'])
		);

		if (!$r) {
			logger('xchan not found for ' . $person_obj['id']);
			return;
		}
		$ret = $r[0];

		$p         = Permissions::connect_perms($channel['channel_id']);
		$my_perms  = $p['perms'];
		$automatic = $p['automatic'];

		$closeness = PConfig::Get($channel['channel_id'], 'system', 'new_abook_closeness', 80);

		$r = abook_store_lowlevel(
			[
				'abook_account'   => intval($channel['channel_account_id']),
				'abook_channel'   => intval($channel['channel_id']),
				'abook_xchan'     => $ret['xchan_hash'],
				'abook_closeness' => intval($closeness),
				'abook_created'   => datetime_convert(),
				'abook_updated'   => datetime_convert(),
				'abook_connected' => datetime_convert(),
				'abook_dob'       => NULL_DATE,
				'abook_pending'   => intval(($automatic) ? 0 : 1),
				'abook_instance'  => z_root()
			]
		);

		if ($my_perms)
			foreach ($my_perms as $k => $v)
				AbConfig::Set($channel['channel_id'], $ret['xchan_hash'], 'my_perms', $k, $v);

		if ($their_perms)
			foreach ($their_perms as $k => $v)
				AbConfig::Set($channel['channel_id'], $ret['xchan_hash'], 'their_perms', $k, $v);

		if ($r) {
			logger("New ActivityPub follower for {$channel['channel_name']}");

			$new_connection = q("select * from abook left join xchan on abook_xchan = xchan_hash left join hubloc on hubloc_hash = xchan_hash where abook_channel = %d and abook_xchan = '%s' order by abook_created desc limit 1",
				intval($channel['channel_id']),
				dbesc($ret['xchan_hash'])
			);
			if ($new_connection) {
				Enotify::submit(
					[
						'type'       => NOTIFY_INTRO,
						'from_xchan' => $ret['xchan_hash'],
						'to_xchan'   => $channel['channel_hash'],
						'link'       => z_root() . '/connections#' . $new_connection[0]['abook_id'],
					]
				);

				if ($my_perms && $automatic) {
					// send an Accept for this Follow activity
					Master::Summon(['Notifier', 'permission_accept', $new_connection[0]['abook_id']]);
					// Send back a Follow notification to them
					Master::Summon(['Notifier', 'permission_create', $new_connection[0]['abook_id']]);
				}

				$clone = [];
				foreach ($new_connection[0] as $k => $v) {
					if (strpos($k, 'abook_') === 0) {
						$clone[$k] = $v;
					}
				}
				unset($clone['abook_id']);
				unset($clone['abook_account']);
				unset($clone['abook_channel']);

				$abconfig = AbConfig::Load($channel['channel_id'], $clone['abook_xchan']);

				if ($abconfig)
					$clone['abconfig'] = $abconfig;

				Libsync::build_sync_packet($channel['channel_id'], ['abook' => [$clone]]);
			}
		}


		/* If there is a default group for this channel and permissions are automatic, add this member to it */

		if ($channel['channel_default_group'] && $automatic) {
			$g = AccessList::by_hash($channel['channel_id'], $channel['channel_default_group']);
			if ($g)
				AccessList::member_add($channel['channel_id'], '', $ret['xchan_hash'], $g['id']);
		}


		return;

	}

	static function unfollow($channel, $act) {

		$contact = null;

		/* @FIXME This really needs to be a signed request. */

		/* actor is unfollowing $channel */

		$person_obj = $act->actor;

		if (is_array($person_obj)) {

			$r = q("select * from abook left join xchan on abook_xchan = xchan_hash where abook_xchan = '%s' and abook_channel = %d limit 1",
				dbesc($person_obj['id']),
				intval($channel['channel_id'])
			);
			if ($r) {
				// remove all permissions they provided
				AbConfig::Delete($channel['channel_id'], $r[0]['xchan_hash'], 'system', 'their_perms');
			}
		}

		return;
	}

	public static function drop($channel, $observer, $act) {
		$r = q("select * from item where mid = '%s' and uid = %d limit 1",
			dbesc((is_array($act->obj)) ? $act->obj['id'] : $act->obj),
			intval($channel['channel_id'])
		);

		if (!$r) {
			return;
		}

		if (in_array($observer, [$r[0]['author_xchan'], $r[0]['owner_xchan']])) {
			drop_item($r[0]['id'], (($r[0]['item_wall']) ? DROPITEM_PHASE1 : DROPITEM_NORMAL), observer_hash: $observer);
		} elseif (in_array($act->actor['id'], [$r[0]['author_xchan'], $r[0]['owner_xchan']])) {
			drop_item($r[0]['id'], (($r[0]['item_wall']) ? DROPITEM_PHASE1 : DROPITEM_NORMAL));
		}

		sync_an_item($channel['channel_id'], $r[0]['id']);

		if ($r[0]['item_wall']) {
			Master::Summon(['Notifier', 'drop', $r[0]['id']]);
		}
	}


	static function actor_store($person_obj, $force = false) {

		if (!is_array($person_obj)) {
			return;
		}

		/* not implemented
		if (array_key_exists('movedTo',$person_obj) && $person_obj['movedTo'] && ! is_array($person_obj['movedTo'])) {
			$tgt = self::fetch($person_obj['movedTo']);
			if (is_array($tgt)) {
				self::actor_store($tgt);
				ActivityPub::move($person_obj['id'],$tgt);
			}
			return;
		}
		*/

		$url = $person_obj['id'] ?? '';

		if (!$url) {
			return;
		}

		$hublocs = self::get_actor_hublocs($url);
		$has_zot_hubloc = false;
		$ap_hubloc = null;

		if ($hublocs) {
			foreach ($hublocs as $hub) {
				if ($hub['hubloc_network'] === 'activitypub') {
					$ap_hubloc = $hub;
				}
				if ($hub['hubloc_network'] === 'zot6') {
					$has_zot_hubloc = true;
					Libzot::update_cached_hubloc($hub);
				}
			}
		}

		if ($ap_hubloc) {
			// we already have a stored record. Determine if it needs updating.
			if ($ap_hubloc['hubloc_updated'] < datetime_convert('UTC', 'UTC', ' now - 3 days') || $force) {
				$person_obj = self::get_actor($url, $force);
			}
			else {
				return;
			}
		}

		$inbox = $person_obj['inbox'] ?? null;

		// invalid AP identity

		if (!$inbox || strpos($inbox, z_root()) !== false) {
			return;
		}

		$name = ((isset($person_obj['name'])) ? escape_tags($person_obj['name']) : '');
		if (!$name) {
			$name = ((isset($person_obj['preferredUsername'])) ? escape_tags($person_obj['preferredUsername']) : '');
		}
		if (!$name) {
			$name = t('Unknown');
		}

		$webfinger_addr = ((isset($person_obj['webfinger'])) ? str_replace('acct:', '', $person_obj['webfinger']) : '');
		$hostname = '';
		$baseurl  = '';

		$m = parse_url($url);
		if ($m) {
			$hostname = unparse_url($m, ['host']);
			$baseurl  = unparse_url($m, ['scheme', 'host', 'port']);
		}

		if (!$webfinger_addr && !empty($person_obj['preferredUsername']) && $hostname) {
			$webfinger_addr = escape_tags($person_obj['preferredUsername']) . '@' . $hostname;
		}

		$icon = null;
		if (isset($person_obj['icon'])) {
			if (is_array($person_obj['icon'])) {
				if (array_key_exists('url', $person_obj['icon'])) {
					$icon = $person_obj['icon']['url'];
				}
				else {
					if (is_string($person_obj['icon'][0])) {
						$icon = $person_obj['icon'][0];
					}
					elseif (array_key_exists('url', $person_obj['icon'][0])) {
						$icon = $person_obj['icon'][0]['url'];
					}
				}
			}
			else {
				$icon = $person_obj['icon'];
			}
		}

		$links   = false;
		$profile = false;

		if (isset($person_obj['url']) && is_array($person_obj['url'])) {
			if (!array_key_exists(0, $person_obj['url'])) {
				$links = [$person_obj['url']];
			}
			else {
				$links = $person_obj['url'];
			}
		}

		if (is_array($links) && $links) {
			foreach ($links as $link) {
				if (is_array($link) && array_key_exists('mediaType', $link) && $link['mediaType'] === 'text/html') {
					$profile = $link['href'];
				} elseif (is_string($link)) {
					$profile = $link;
					break;
				}
			}
			if (!$profile && isset($links[0]['href'])) {
				$profile = $links[0]['href'];
			}
		}
		elseif (isset($person_obj['url']) && is_string($person_obj['url'])) {
			$profile = $person_obj['url'];
		}

		if (!$profile) {
			$profile = $url;
		}

		$pubkey = '';
		if (array_key_exists('publicKey', $person_obj) && array_key_exists('publicKeyPem', $person_obj['publicKey'])) {
			if ($person_obj['id'] === $person_obj['publicKey']['owner']) {
				$pubkey = $person_obj['publicKey']['publicKeyPem'];
				if (strstr($pubkey, 'RSA ')) {
					$pubkey = Keyutils::rsaToPem($pubkey);
				}
			}
		}

		$epubkey = '';
		if (isset($person_obj['assertionMethod'])) {
			if (!isset($person_obj['assertionMethod'][0])) {
				$person_obj['assertionMethod'] = [$person_obj['assertionMethod']];
			}

			foreach($person_obj['assertionMethod'] as $am) {
				if ($person_obj['id'] === $am['controller'] &&
					$am['type'] === 'Multikey' &&
					str_starts_with($am['publicKeyMultibase'], 'z6Mk')
				) {
					$epubkey = $am['publicKeyMultibase'];
				}
			}
		}

		$group_actor = ($person_obj['type'] === 'Group');

		$r = q("select * from xchan join hubloc on xchan_hash = hubloc_hash where xchan_hash = '%s'",
			dbesc($url)
		);

		if ($r) {
			// Record exists. Cache existing records for one week at most
			// then refetch to catch updated profile photos, names, etc.
			$d = datetime_convert('UTC', 'UTC', 'now - 3 days');
			if ($r[0]['hubloc_updated'] > $d && !$force) {
				return;
			}

			q("UPDATE site SET site_update = '%s', site_dead = 0 WHERE site_url = '%s'",
				dbesc(datetime_convert()),
				dbesc($baseurl)
			);

			// update existing xchan record
			q("update xchan set xchan_name = '%s', xchan_pubkey = '%s', xchan_epubkey = '%s', xchan_addr = '%s', xchan_network = 'activitypub', xchan_name_date = '%s', xchan_pubforum = %d where xchan_hash = '%s'",
				dbesc($name),
				dbesc($pubkey),
				dbesc($epubkey),
				dbesc($webfinger_addr),
				dbescdate(datetime_convert()),
				intval($group_actor),
				dbesc($url)
			);

			// update existing hubloc record
			q("update hubloc set hubloc_addr = '%s', hubloc_network = 'activitypub', hubloc_url = '%s', hubloc_host = '%s', hubloc_callback = '%s', hubloc_updated = '%s', hubloc_id_url = '%s' where hubloc_hash = '%s'",
				dbesc($webfinger_addr),
				dbesc($baseurl),
				dbesc($hostname),
				dbesc($inbox),
				dbescdate(datetime_convert()),
				dbesc($profile),
				dbesc($url)
			);
		}
		else {
			// create a new record

			xchan_store_lowlevel(
				[
					'xchan_hash'      => $url,
					'xchan_guid'      => $url,
					'xchan_pubkey'    => $pubkey,
					'xchan_epubkey'   => $epubkey,
					'xchan_addr'      => $webfinger_addr,
					'xchan_url'       => $profile,
					'xchan_name'      => $name,
					'xchan_photo_l'   => z_root() . '/' . get_default_profile_photo(),
					'xchan_photo_m'   => z_root() . '/' . get_default_profile_photo(80),
					'xchan_photo_s'   => z_root() . '/' . get_default_profile_photo(48),
					'xchan_name_date' => datetime_convert(),
					'xchan_network'   => 'activitypub',
					'xchan_pubforum'  => intval($group_actor)
				]
			);

			hubloc_store_lowlevel(
				[
					'hubloc_guid'     => $url,
					'hubloc_hash'     => $url,
					'hubloc_addr'     => $webfinger_addr,
					'hubloc_network'  => 'activitypub',
					'hubloc_url'      => $baseurl,
					'hubloc_host'     => $hostname,
					'hubloc_callback' => $inbox,
					'hubloc_updated'  => datetime_convert(),
					'hubloc_primary'  => 1,
					'hubloc_id_url'   => $profile
				]
			);
		}

		// We store all ActivityPub actors we can resolve. Some of them may be able to communicate over Zot6. Find them.
		// Adding zot discovery urls to the actor record will cause federation to fail with the 20-30 projects which don't accept arrays in the url field.

		$actor_protocols = self::get_actor_protocols($person_obj);
		if (!$has_zot_hubloc && in_array('zot6', $actor_protocols)) {
			$zx = q("select * from hubloc where hubloc_id_url = '%s' and hubloc_network = 'zot6'",
				dbesc($url)
			);
			if (!$zx) {
				// FIXME: we might need to fetch and store this url immediately
				// otherwise at least the first post of a yet unknown author might
				// be stored with the activitypub url instead of the portable id.
				// Another solution could be to fix the items after Gprobe has done its work.
				Master::Summon(['Gprobe', bin2hex($url)]);
			}
		}

		if ($icon) {
			Master::Summon(['Xchan_photo', bin2hex($icon), bin2hex($url), $force]);
		}
	}


	// sort function width decreasing
	static function vid_sort($a, $b) {
		$a_width = $a['width'] ?? 0;
		$b_width = $b['width'] ?? 0;

		if ($a_width === $b_width)
			return 0;

		return (($a_width > $b_width) ? -1 : 1);
	}


	static function get_actor_bbmention($id) {

		$x = q("select * from hubloc left join xchan on hubloc_hash = xchan_hash where hubloc_hash = '%s' or hubloc_id_url = '%s' limit 1",
			dbesc($id),
			dbesc($id)
		);

		if ($x) {
			return sprintf('@[zrl=%s]%s[/zrl]', $x[0]['xchan_url'], $x[0]['xchan_name']);
		}
		return '@{' . $id . '}';

	}

	static function update_poll($pollItem, $response) {

		logger('updating poll');

		$multi = false;

		if (!$pollItem) {
			logger('no item');
			return false;
		}

		if (intval($pollItem['item_blocked']) === ITEM_MODERATED) {
			logger('item blocked');
			return false;
		}

		$channel = channelx_by_n($pollItem['uid']);
		if (!$channel) {
			logger('no channel');
			return false;
		}

		$relatedItem = find_related($pollItem);

		$ids = (($relatedItem) ? $pollItem['id'] . ',' . $relatedItem['id'] : $pollItem['id']);

		dbq("START TRANSACTION");
		// Using the provided items as is will produce desastrous race conditions
		// in case of multiple choice polls - hence:

		$items = dbq("SELECT * FROM item WHERE id in ($ids) FOR UPDATE");

		foreach ($items as $item) {
			if ($item['id'] === $pollItem['id']) {
				$pollItem = $item;
			}
			if (!empty($relatedItem['id']) && $item['id'] === $relatedItem['id']) {
				$relatedItem = $item;
			}
		}

		$o = json_decode($pollItem['obj'], true);

		if ($o && array_key_exists('anyOf', $o)) {
			$multi = true;
		}

		if ($response) {
			$mid = $response['mid'];
			$content = trim($response['title']);

			$r = q("select mid, title from item where parent_mid = '%s' and author_xchan = '%s' and mid != parent_mid ",
				dbesc($pollItem['mid']),
				dbesc($response['author_xchan'])
			);

			// prevent any duplicate votes by same author for oneOf and duplicate votes with same author and same answer for anyOf

			if ($r) {
				if ($multi) {
					foreach ($r as $rv) {
						if (trim($rv['title']) === $content && $rv['mid'] !== $mid) {
							logger('already voted multi');
							return false;
						}
					}
				} else {
					foreach ($r as $rv) {
						if ($rv['mid'] !== $mid && $content) {
							logger('already voted');
							return false;
						}
					}
				}
			}

			$answer_found = false;
			$foundPrevious = false;
			if ($multi) {
				for ($c = 0; $c < count($o['anyOf']); $c++) {
					if (trim($o['anyOf'][$c]['name']) === $content) {
						$answer_found = true;


						if (is_array($o['anyOf'][$c]['replies'])) {
							foreach ($o['anyOf'][$c]['replies'] as $reply) {
								if (is_array($reply) && array_key_exists('id', $reply) && $reply['id'] === $mid) {
									$foundPrevious = true;
								}
							}
						}

						if (!$foundPrevious) {
							$o['anyOf'][$c]['replies']['totalItems']++;
							$o['anyOf'][$c]['replies']['items'][] = ['id' => $mid, 'type' => 'Note'];
						}
					}
				}
			} else {
				for ($c = 0; $c < count($o['oneOf']); $c++) {
					if (trim($o['oneOf'][$c]['name']) === $content) {
						$answer_found = true;
						if (is_array($o['oneOf'][$c]['replies'])) {
							foreach ($o['oneOf'][$c]['replies'] as $reply) {
								if (is_array($reply) && array_key_exists('id', $reply) && $reply['id'] === $mid) {
									$foundPrevious = true;
								}
							}
						}

						if (!$foundPrevious) {
							$o['oneOf'][$c]['replies']['totalItems']++;
							$o['oneOf'][$c]['replies']['items'][] = ['id' => $mid, 'type' => 'Note'];
						}
					}
				}
			}
		}
		if ($pollItem['comments_closed'] > NULL_DATE) {
			if ($pollItem['comments_closed'] > datetime_convert()) {
				$o['closed'] = datetime_convert('UTC', 'UTC', $pollItem['comments_closed'], ATOM_TIME);
				// set this to force an update
				$answer_found = true;
			}
		}

		// A change was made locally
		if ($response && $answer_found && !$foundPrevious) {

			// update this copy
			$i = [$pollItem];
			xchan_query($i, true);
			$i = fetch_post_tags($i);
			$i[0]['obj'] = $o;

			$edited = datetime_convert();
			$i[0]['edited'] = $edited;

			// create the new object
			$newObj = self::build_packet(self::encode_activity($i[0]), $channel, true);

			// and immediately update the db
			$u = q("UPDATE item
				SET obj = (
					CASE
					WHEN item.id = %d THEN '%s'
					WHEN item.id = %d THEN '%s'
					END
				),
				edited = '%s'
				WHERE id IN ($ids)",
				intval($pollItem['id']),
				dbesc(json_encode($o)),
				intval($relatedItem['id']),
				dbesc($newObj),
				dbesc($edited)
			);

			dbq("COMMIT");

			Master::Summon(['Notifier', 'edit_post', $pollItem['id'], $response['mid']]);
			if (!empty($relatedItem['id'])) {
				Master::Summon(['Notifier', 'edit_post', $relatedItem['id'], $response['mid']]);
			}
		}

		return true;
	}

	static function decode_note($act) {

		$response_activity = false;
		$s = [];

		// These activities should have been handled separately in the Inbox module and should not be turned into posts

		if (
			in_array($act->type, ['Follow', 'Accept', 'Reject', 'Create', 'Update']) &&
			($act->objprop('type') === 'Follow' || ActivityStreams::is_an_actor($act->objprop('type')))
		) {
			return false;
		}
		// Within our family of projects, Follow/Unfollow of a thread is an internal activity which should not be transmitted,
		// hence if we receive it - ignore or reject it.
		// Unfollow is not defined by ActivityStreams, which prefers Undo->Follow.
		// This may have to be revisited if AP projects start using Follow for objects other than actors.

		if (in_array($act->type, ['Follow', 'Unfollow'])) {
			return false;
		}

		if (!isset($act->actor['id'])) {
			logger('No actor!');
			return false;
		}

		// ensure we store the original actor
		self::actor_store($act->actor);

		$s['owner_xchan']  = $act->actor['id'];
		$s['author_xchan'] = $act->actor['id'];

		$s['mid'] = self::getMessageID($act);

		if (!$s['mid']) {
			return false;
		}

		$s['uuid'] = self::getUUID($act);

		if (!$s['uuid']) {
			// If we have not found anything useful, create an uuid v5 from the mid
			$s['uuid'] = uuid_from_url($s['mid']);
		}

		$content = [];

		if (is_array($act->obj)) {
			$content = self::get_content($act->obj);
		}

		$s['parent_mid'] = $act->parent_id;

		if (array_key_exists('published', $act->data)) {
			$s['created']   = datetime_convert('UTC', 'UTC', $act->data['published']);
			$s['commented'] = $s['created'];
		}
		elseif ($act->objprop('published')) {
			$s['created']   = datetime_convert('UTC', 'UTC', $act->obj['published']);
			$s['commented'] = $s['created'];
		}
		if (array_key_exists('updated', $act->data)) {
			$s['edited'] = datetime_convert('UTC', 'UTC', $act->data['updated']);
		}
		elseif ($act->objprop('updated')) {
			$s['edited'] = datetime_convert('UTC', 'UTC', $act->obj['updated']);
		}
		if (array_key_exists('expires', $act->data)) {
			$s['expires'] = datetime_convert('UTC', 'UTC', $act->data['expires']);
		}
		elseif ($act->objprop('expires')) {
			$s['expires'] = datetime_convert('UTC', 'UTC', $act->obj['expires']);
		}

		if ($act->objprop('location')) {
			$s['location'] = ((isset($act->objprop('location')['name'])) ? html2plain(purify_html($act->objprop('location')['name'])) : '');
			if (isset($act->objprop('location')['latitude'], $act->objprop('location')['longitude'])) {
				$s['coord'] = floatval($act->objprop('location')['latitude']) . ' ' . floatval($act->objprop('location')['longitude']);
			}
		}

		if (in_array($act->type, ['Invite', 'Create']) && $act->objprop('type') === 'Event') {
			$s['mid'] = $s['parent_mid'] = $act->id;
		}

		if (ActivityStreams::is_response_activity($act->type)) {

			$response_activity = true;

			$s['parent_mid'] = $act->objprop('id') ?: $act->obj;

			// over-ride the object timestamp with the activity

			if (isset($act->data['published'])) {
				$s['created'] = datetime_convert('UTC', 'UTC', $act->data['published']);
			}

			if (isset($act->data['updated'])) {
				$s['edited'] = datetime_convert('UTC', 'UTC', $act->data['updated']);
			}

			$obj_actor = is_array($act->objprop('actor')) ? $act->objprop('actor') : $act->get_actor('attributedTo', $act->obj);

			if (empty($obj_actor['id'])) {
				return false;
			}

			// ensure we store the original actor
			self::actor_store($obj_actor);

			$mention = self::get_actor_bbmention($obj_actor['id']);

			if ($act->type === 'Like') {
				$content['content'] = sprintf(t('Likes %1$s\'s %2$s'), $mention, $act->obj['type']) . EOL . EOL . ($content['content'] ?? '');
			}
			if ($act->type === 'Dislike') {
				$content['content'] = sprintf(t('Doesn\'t like %1$s\'s %2$s'), $mention, $act->obj['type']) . EOL . EOL . ($content['content'] ?? '');
			}

			// handle event RSVPs
			if (($act->objprop('type') === 'Event') || ($act->objprop('type') === 'Invite' && array_path_exists('object/type', $act->obj) && $act->obj['object']['type'] === 'Event')) {
				if ($act->type === 'Accept') {
					$content['content'] = sprintf(t('Will attend %s\'s event'), $mention) . EOL . EOL . ($content['content'] ?? '');
				}
				if ($act->type === 'Reject') {
					$content['content'] = sprintf(t('Will not attend %s\'s event'), $mention) . EOL . EOL . ($content['content'] ?? '');
				}
				if ($act->type === 'TentativeAccept') {
					$content['content'] = sprintf(t('May attend %s\'s event'), $mention) . EOL . EOL . ($content['content'] ?? '');
				}
				if ($act->type === 'TentativeReject') {
					$content['content'] = sprintf(t('May not attend %s\'s event'), $mention) . EOL . EOL . ($content['content'] ?? '');
				}
			}

			if ($act->type === 'Announce') {
				$content['content'] = sprintf(t('&#x1f501; Repeated %1$s\'s %2$s'), $mention, $act->obj['type']);
			}

			if (in_array($act->type, ['EmojiReact'])) {

				// Pleroma reactions
				$t = trim(self::get_textfield($act->data, 'content'));

				$content['content'] = $t;

				// Unicode emojis
				if (grapheme_strlen($t) === 1) {
					$content['content'] = '<h1>' . $t . '</h1>';
				}

				$a = self::decode_taxonomy($act->data);

				if ($a) {
					$s['term'] = $a;
				}
			}
		}

		$s['item_thread_top'] = 0;
		$s['comment_policy'] = 'authenticated';

		if ($s['mid'] === $s['parent_mid']) {
			$s['item_thread_top'] = 1;
			$s['item_nocomment'] = 0;
			$s['comments_closed'] = NULL_DATE;

			// it is a parent node - decode the comment policy info if present
			if ($act->objprop('commentPolicy')) {
				$until = strpos($act->obj['commentPolicy'], 'until=');
				if ($until !== false) {
					$s['comments_closed'] = datetime_convert('UTC', 'UTC', substr($act->obj['commentPolicy'], $until + 6));
					if ($s['comments_closed'] < datetime_convert()) {
						$s['item_nocomment'] = 1;
					}
				}

				$remainder = substr($act->obj['commentPolicy'], 0, (($until) ? $until : strlen($act->obj['commentPolicy'])));
				if ($remainder) {
					$s['comment_policy'] = $remainder;
				}
			}
		}

		if (!array_key_exists('created', $s))
			$s['created'] = datetime_convert();

		if (!array_key_exists('edited', $s))
			$s['edited'] = $s['created'];

		$s['title']   = (($response_activity) ? EMPTY_STR : html2plain($content['name']));
		$s['summary'] = (($content['summary'] !== $content['content']) ? html2plain($content['summary']) : '');
		$s['body']    = ((self::bb_content($content, 'bbcode') && (!$response_activity)) ? self::bb_content($content, 'bbcode') : self::bb_content($content, 'content'));

		// peertube quirks
		if ($act->objprop('mediaType') === 'text/markdown') {
			$s['body'] = markdown_to_bb($act->objprop('content'));
		}

		$quote_urls = [];

		if (isset($act->obj['tag'])) {
			foreach($act->obj['tag'] as $t) {
				if (is_array($t) && $t['type'] === 'Link' && $t['mediaType'] === 'application/ld+json; profile="https://www.w3.org/ns/activitystreams"') {
					$quote_urls[] = $t['href'];
				}
			}
		}

		if (!$quote_urls) {
			$quote_url = $act->obj['quoteUrl'] ?? $act->obj['quoteUri'] ?? $act->obj['_misskey_quote'] ?? $act->obj['quote'] ??  null;

			if ($quote_url) {
				$quote_urls = [$quote_url];
			}
		}

		// Backwards compatibility: only process quote items if there is no share tag in them.
		// Otherwise they will appear doubled.
		if ($quote_urls && !str_contains($s['body'], '[/share]')) {
			foreach($quote_urls as $quote_url) {
				$quote = self::get_quote($quote_url);

				if (!$quote) {
					continue;
				}

				$s['body'] = self::pasteQuote($s['body'], $quote);
				$s['term'] = $quote['term'];
			}
		}

		$s['verb'] = self::activity_mapper($act->type);

		// Mastodon does not provide update timestamps when updating poll tallies which means race conditions may occur here.
		if ($act->type === 'Update' && $act->objprop('type') === 'Question' && $s['edited'] === $s['created']) {
			$s['edited'] = datetime_convert();
		}

		if (in_array($act->type, ['Delete', 'Undo', 'Tombstone']) || ($act->type === 'Create' && (isset($act->obj['type']) && $act->obj['type'] === 'Tombstone'))) {
			$s['item_deleted'] = 1;
		}

		if ($act->objprop('type')) {
			$s['obj_type'] = self::activity_obj_mapper($act->obj['type']);
		}

		$s['obj'] = $act->obj;

		if (array_path_exists('actor/id', $s['obj'])) {
			$s['obj']['actor'] = $s['obj']['actor']['id'];
		}

		if (is_array($act->tgt) && $act->tgt) {
			if (array_key_exists('type', $act->tgt)) {
				$s['tgt_type'] = self::activity_obj_mapper($act->tgt['type']);
			}
			// We shouldn't need to store collection contents which could be large. We will often only require the meta-data
			if (isset($s['tgt_type']) && str_contains($s['tgt_type'], 'Collection')) {
				$s['target'] = ['id' => $act->tgt['id'], 'type' => $s['tgt_type'], 'attributedTo' => $act->tgt['attributedTo'] ?? $act->tgt['actor']];
			}
		}

		$generator = $act->get_property_obj('generator');
		if ((!$generator) && (!$response_activity)) {
			$generator = $act->get_property_obj('generator', $act->obj);
		}

		if ($generator && array_key_exists('type', $generator)
			&& in_array($generator['type'], ['Application', 'Service']) && array_key_exists('name', $generator)) {
			$s['app'] = escape_tags($generator['name']);
		}

		if (is_array($act->obj) && !$response_activity) {
			$a = self::decode_taxonomy($act->obj);

			if ($a) {
				if (isset($s['term'])) {
					// term might contain content from a quote post
					$s['term'] = array_merge($s['term'], $a);
				}
				else {
					$s['term'] = $a;
				}
			}

			$a = self::decode_attachment($act->obj);
			if ($a) {
				$s['attach'] = $a;
			}

			$a = self::decode_iconfig($act->data);
			if ($a) {
				$s['iconfig'] = $a;
			}
		}


		// Objects that might have media attachments which aren't already provided in the content element.
		// We'll check specific media objects separately.

		if (in_array($act->objprop('type',''), ['Article', 'Document', 'Event', 'Note', 'Page', 'Place', 'Question']) && !empty($s['attach'])) {
			$s = self::bb_attach($s);
		}

		if ($act->objprop('type') === 'Question' && in_array($act->type, ['Create', 'Update'])) {
			if ($act->objprop('endTime')) {
				$s['comments_closed'] = datetime_convert('UTC', 'UTC', $act->obj['endTime']);
			}
		}

		if ($act->objprop('closed')) {
			$s['comments_closed'] = datetime_convert('UTC', 'UTC', $act->obj['closed']);
		}

		if (!$response_activity) {

			if ($act->objprop('type') === 'Profile') {
				$s['parent_mid'] = $s['mid'];
				$s['item_thread_top'] = 1;
			}


			// we will need a hook here to extract magnet links e.g. peertube
			// right now just link to the largest mp4 we find that will fit in our
			// standard content region

			if ($act->objprop('type') === 'Video') {

				$vtypes = [
					'video/mp4',
					'video/ogg',
					'video/webm'
				];

				$mps    = [];
				$poster = null;
				$ptr    = null;

				// try to find a poster to display on the video element

				if ($act->objprop('icon')) {
					if (is_array($act->obj['icon'])) {
						if (array_key_exists(0,$act->obj['icon'])) {
							$ptr = $act->obj['icon'];
						}
						else {
							$ptr = [ $act->obj['icon'] ];
						}
					}
					if ($ptr) {
						foreach ($ptr as $foo) {
							if (is_array($foo) && array_key_exists('type',$foo) && $foo['type'] === 'Image' && is_string($foo['url'])) {
								$poster = $foo['url'];
							}
						}
					}
				}

				$tag = (($poster) ? '[video poster=\'' . $poster . '\']' : '[video]' );

				$ptr = null;

				if ($act->objprop('url')) {
					if (is_array($act->obj['url'])) {
						if (array_key_exists(0,$act->obj['url'])) {
							$ptr = $act->obj['url'];
						}
						else {
							$ptr = [ $act->obj['url'] ];
						}
						// handle peertube's weird url link tree if we find it here
						// 0 => html link, 1 => application/x-mpegURL with 'tag' set to an array of actual media links
						foreach ($ptr as $idex) {
							if (is_array($idex) && array_key_exists('mediaType',$idex)) {
								if ($idex['mediaType'] === 'application/x-mpegURL' && isset($idex['tag']) && is_array($idex['tag'])) {
									$ptr = $idex['tag'];
									break;
								}
							}
						}

						foreach ($ptr as $vurl) {
							if (array_key_exists('mediaType',$vurl)) {
								if (in_array($vurl['mediaType'], $vtypes)) {
									if (! array_key_exists('height',$vurl)) {
										$vurl['height'] = 0;
									}
									$mps[] = $vurl;
								}
							}
						}
					}
					if ($mps) {
						usort($mps,[ '\Zotlabs\Lib\Activity', 'vid_sort' ]);
						foreach ($mps as $m) {
							if (intval($m['height']) <= 720 && self::media_not_in_body($m['href'],$s['body'])) {
								$s['body'] = $tag . $m['href'] . '[/video]' . "\r\n" . $s['body'];
								break;
							}
						}
					}
					elseif (is_string($act->obj['url']) && self::media_not_in_body($act->obj['url'],$s['body'])) {
						$s['body'] = $tag . $act->obj['url'] . '[/video]' . "\r\n" . $s['body'];
					}

				}
			}

			if ($act->objprop('type') === 'Audio') {

				$atypes = [
					'audio/mpeg',
					'audio/ogg',
					'audio/wav'
				];

				$ptr = null;

				if (array_key_exists('url', $act->obj)) {
					if (is_array($act->obj['url'])) {
						if (array_key_exists(0, $act->obj['url'])) {
							$ptr = $act->obj['url'];
						}
						else {
							$ptr = [$act->obj['url']];
						}
						foreach ($ptr as $vurl) {
							if (in_array($vurl['mediaType'], $atypes) && self::media_not_in_body($vurl['href'], $s['body'])) {
								$s['body'] = '[audio]' . $vurl['href'] . '[/audio]' . "\r\n" . $s['body'];
								break;
							}
						}
					}
					elseif (is_string($act->obj['url']) && self::media_not_in_body($act->obj['url'], $s['body'])) {
						$s['body'] = '[audio]' . $act->obj['url'] . '[/audio]' . "\r\n" . $s['body'];
					}
				}

			}

			if ($act->objprop('type') === 'Image' && strpos($s['body'], 'zrl=') === false) {

				$ptr = null;

				if (array_key_exists('url', $act->obj)) {
					if (is_array($act->obj['url'])) {
						if (array_key_exists(0, $act->obj['url'])) {
							$ptr = $act->obj['url'];
						}
						else {
							$ptr = [$act->obj['url']];
						}
						foreach ($ptr as $vurl) {
							if (strpos($s['body'], $vurl['href']) === false) {
								$bb_imgs = '[zmg]' . $vurl['href'] . '[/zmg]' . "\r\n";
								break;
							}
						}
						$s['body'] = $bb_imgs . $s['body'];
					}
					elseif (is_string($act->obj['url'])) {
						if (strpos($s['body'], $act->obj['url']) === false) {
							$s['body'] .= '[zmg]' . $act->obj['url'] . '[/zmg]' . "\r\n" . $s['body'];
						}
					}
				}
			}

			if ($act->objprop('type') === 'Page' && !$s['body']) {

				$ptr  = null;
				$purl = EMPTY_STR;

				if (array_key_exists('url', $act->obj)) {
					if (is_array($act->obj['url'])) {
						if (array_key_exists(0, $act->obj['url'])) {
							$ptr = $act->obj['url'];
						}
						else {
							$ptr = [$act->obj['url']];
						}
						foreach ($ptr as $vurl) {
							if (array_key_exists('mediaType', $vurl) && $vurl['mediaType'] === 'text/html') {
								$purl = $vurl['href'];
								break;
							}
							elseif (array_key_exists('mimeType', $vurl) && $vurl['mimeType'] === 'text/html') {
								$purl = $vurl['href'];
								break;
							}
						}
					}
					elseif (is_string($act->obj['url'])) {
						$purl = $act->obj['url'];
					}
					if ($purl) {
						$li = z_fetch_url(z_root() . '/linkinfo?binurl=' . bin2hex($purl));
						if ($li['success'] && $li['body']) {
							$s['body'] .= "\r\n" . $li['body'];
						}
						else {
							$s['body'] .= "\r\n" . $purl;
						}
					}
				}
			}
		}

		if (in_array($act->objprop('type'), ['Note', 'Article', 'Page', 'Question'])) {
			$ptr = null;

			if (array_key_exists('url', $act->obj)) {
				if (is_array($act->obj['url'])) {
					if (array_key_exists(0, $act->obj['url'])) {
						$ptr = $act->obj['url'];
					}
					else {
						$ptr = [$act->obj['url']];
					}
					foreach ($ptr as $vurl) {
						if (isset($vurl['mediaType']) && $vurl['mediaType'] === 'text/html') {
							$s['plink'] = $vurl['href'];
							break;
						}
					}
				}
				elseif (is_string($act->obj['url'])) {
					$s['plink'] = $act->obj['url'];
				}
			}
		}

        if (empty($s['plink'])) {
			$s['plink'] = $s['mid'];
		}

		// assume this is private unless specifically told otherwise.

		$s['item_private'] = 1;
		if ($act->recips && (in_array(ACTIVITY_PUBLIC_INBOX, $act->recips) || in_array('Public', $act->recips) || in_array('as:Public', $act->recips))) {
			$s['item_private'] = 0;
		}

		if ($act->objprop('directMessage')) {
			$s['item_private'] = 2;
		}

		$ap_rawmsg = '';
		$diaspora_rawmsg = '';
		$raw_arr = [];

		$raw_arr = json_decode($act->raw, true);

		// This is a zot6 packet and the raw activitypub or diaspora message json
		// is possibly available in the attachement.
		if (array_key_exists('signed', $raw_arr) && isset($act->data['attachment']) && is_array($act->data['attachment'])) {
			foreach($act->data['attachment'] as $a) {
				if (
					isset($a['type']) && $a['type'] === 'PropertyValue' &&
					isset($a['name']) && $a['name'] === 'zot.activitypub.rawmsg' &&
					isset($a['value'])
				) {
					$ap_rawmsg = $a['value'];
				}
				if (
					isset($a['type']) && $a['type'] === 'PropertyValue' &&
					isset($a['name']) && $a['name'] === 'zot.diaspora.fields' &&
					isset($a['value'])
				) {
					$diaspora_rawmsg = $a['value'];
				}
			}
		}


		if (!$ap_rawmsg && array_key_exists('signed', $raw_arr)) {
			// zap
			$ap_rawmsg = json_encode($act->data, JSON_UNESCAPED_SLASHES);
		}

		if ($ap_rawmsg) {
			IConfig::Set($s, 'activitypub', 'rawmsg', $ap_rawmsg, 1);
		}
		elseif (!array_key_exists('signed', $raw_arr)) {
			IConfig::Set($s, 'activitypub', 'rawmsg', $act->raw, 1);
		}

		if ($diaspora_rawmsg) {
			IConfig::Set($s, 'diaspora', 'fields', $diaspora_rawmsg, 1);
		}

		if ($act->raw_recips) {
			IConfig::Set($s, 'activitypub', 'recips', $act->raw_recips);
		}

		if ($act->objprop('type') === 'Event' && $act->objprop('timezone')) {
			IConfig::Set($s, 'event', 'timezone', $act->objprop('timezone'), true);
		}

		$hookinfo = [
			'act' => $act,
			's'   => $s
		];

		call_hooks('decode_note', $hookinfo);
		return $hookinfo['s'];

	}

	static function store($channel, $observer_hash, $act, $item, $fetch_parents = true, $force = false, $is_collection_operation = false) {
		$is_sys_channel = is_sys_channel($channel['channel_id']);
		$is_child_node  = false;
		$parent = null;

		// TODO: not implemented
		// Pleroma scrobbles can be really noisy and contain lots of duplicate activities. Disable them by default.
		/*if (($act->type === 'Listen') && ($is_sys_channel || get_pconfig($channel['channel_id'], 'system', 'allow_scrobbles', false))) {
			return;
		}*/

		if ($item['parent_mid'] && $item['parent_mid'] !== $item['mid']) {
			$is_child_node = true;
		}

		if (empty($item['item_fetched'])) {
			$item['owner_xchan'] = $observer_hash;
		}

		// An ugly and imperfect way to recognise a mastodon or friendica direct message
		if (
			$item['item_private'] === 1 &&
			!isset($act->raw_recips['cc']) &&
			is_array($act->raw_recips['to']) &&
			in_array(channel_url($channel), $act->raw_recips['to']) &&
			!in_array($act->actor['followers'], $act->raw_recips['to'])
		) {
			$item['item_private'] = 2;
		}

		$allowed = false;
		$relay = false;

		$permit_mentions = intval(PConfig::Get($channel['channel_id'], 'system','permit_all_mentions') && i_am_mentioned($channel, $item));

		if ($is_child_node) {

			$parent = q("select * from item where mid = '%s' and uid = %d",
				dbesc($item['parent_mid']),
				intval($channel['channel_id'])
			);

			if (!$parent) {
				if (perm_is_allowed($channel['channel_id'], $observer_hash, 'send_stream') || $is_sys_channel) {
					if ($item['verb'] === 'Announce') {
						$force = true;
					}

					if ($fetch_parents) {
						App::$cache['as_fetch_objects'][$item['mid']]['channels'][] = $channel['channel_id'];
						App::$cache['as_fetch_objects'][$item['mid']]['force'] = intval($force);
						return;
					}
				}

				logger('no parent');
				return;
			}

			$relay = $channel['channel_hash'] === $parent[0]['owner_xchan'];

			if (str_contains($parent[0]['tgt_type'], 'Collection') && !$relay && !$is_collection_operation) {
				logger('not a collection activity');
				return;
			}

			if ($parent[0]['obj_type'] === 'Question') {
				if (in_array($item['obj_type'], ['Note', ACTIVITY_OBJ_COMMENT]) && $item['title'] && (!$item['body'])) {
					$item['obj_type'] = 'Answer';
					$item['item_hidden'] = 1;
				}
			}

			if ($parent[0]['item_wall']) {

				// set the owner to the owner of the parent
				$item['owner_xchan'] = $parent[0]['owner_xchan'];

				// quietly reject group comment boosts by group owner
				// (usually only sent via ActivityPub so groups will work on microblog platforms)
				// This catches those activities if they slipped in via a conversation fetch

				if ($parent[0]['parent_mid'] !== $item['parent_mid']) {
					if ($item['verb'] === 'Announce' && $item['author_xchan'] === $item['owner_xchan']) {
						logger('group boost activity by group owner rejected');
						return;
					}
				}

				// check permissions against the author, not the sender
				$allowed = perm_is_allowed($channel['channel_id'], $item['author_xchan'], 'post_comments');
				if ((!$allowed) && $permit_mentions) {
					if ($parent[0]['owner_xchan'] === $channel['channel_hash']) {
						$allowed = false;
					}
					else {
						$allowed = true;
					}
				}

				// TODO: not implemented
				/*if (absolutely_no_comments($parent[0])) {
					$allowed = false;
				}*/

				if (!$allowed) {
					if (PConfig::Get($channel['channel_id'], 'system', 'moderate_unsolicited_comments') && $item['obj_type'] !== 'Answer') {
						$item['item_blocked'] = ITEM_MODERATED;
						$allowed = true;
					}
					else {
						logger('rejected comment from ' . $item['author_xchan'] . ' for ' . $channel['channel_address']);
						logger('rejected: ' . print_r($item, true), LOGGER_DATA);

						// TODO: not implemented
						// let the sender know we received their comment but we don't permit spam here.
						// self::send_rejection_activity($channel,$item['author_xchan'],$item);

						return;
					}
				}

				// TODO: not implemented
				/*if (perm_is_allowed($channel['channel_id'],$item['author_xchan'],'moderated')) {
					$item['item_blocked'] = ITEM_MODERATED;
				}*/
			}
			else {
				$allowed = true;

				// reject public stream comments that weren't sent by the conversation owner
				if ($is_sys_channel && $item['owner_xchan'] !== $observer_hash && !$fetch_parents && empty($item['item_fetched'])) {
					$allowed = false;
				}
			}
		}
		else {

			// The $item['item_fetched'] flag is set in fetch_and_store_parents().
			// In this case we should check against author permissions because sender is not owner.
			if (perm_is_allowed($channel['channel_id'], ((empty($item['item_fetched'])) ? $observer_hash : $item['author_xchan']), 'send_stream') || $is_sys_channel) {
				$allowed = true;
			}

			if ($permit_mentions) {
				$allowed = true;
			}
		}

		if (tgroup_check($channel['channel_id'], $item) && (!$is_child_node)) {
			// for forum deliveries, make sure we keep a copy of the signed original
			IConfig::Set($item, 'activitypub', 'rawmsg', $act->raw, 1);
			$allowed = true;
		}

		if (intval($item['item_private']) === 2) {
			if (perm_is_allowed($channel['channel_id'], $observer_hash, 'post_mail')) {
				$allowed = true;
			}
		}

		if ($is_sys_channel) {

			/* TODO: not implemented
			if (! check_pubstream_channelallowed($observer_hash)) {
				$allowed = false;
			}

			// don't allow pubstream posts if the sender even has a clone on a pubstream denied site

			$h = q("select hubloc_url from hubloc where hubloc_hash = '%s'",
				dbesc($observer_hash)
			);
			if ($h) {
				foreach ($h as $hub) {
					if (! check_pubstream_siteallowed($hub['hubloc_url'])) {
						$allowed = false;
						break;
					}
				}
			}
			*/

			if (intval($item['item_private'])) {
				$allowed = false;
			}
		}

		// TODO: not implemented
		/*$blocked = LibBlock::fetch($channel['channel_id'],BLOCKTYPE_SERVER);
		if ($blocked) {
			foreach($blocked as $b) {
				if (strpos($observer_hash,$b['block_entity']) !== false) {
					$allowed = false;
				}
			}
		}*/

		if (!$allowed && !$force) {
			logger('no permission');
			return;
		}

		$item['aid'] = $channel['channel_account_id'];
		$item['uid'] = $channel['channel_id'];

		// Some authors may be zot6 authors in which case we want to store their nomadic identity
		// instead of their ActivityPub identity

		$item['author_xchan'] = self::find_best_identity($item['author_xchan']);
		$item['owner_xchan']  = self::find_best_identity($item['owner_xchan']);
		$item['source_xchan']  = ((!empty($item['source_xchan'])) ? self::find_best_identity($item['source_xchan']) : '');

		if (!$item['author_xchan']) {
			logger('No author: ' . print_r($act, true));
		}

		if (!$item['owner_xchan']) {
			logger('No owner: ' . print_r($act, true));
		}

		if (!$item['author_xchan'] || !$item['owner_xchan'])
			return;

		if ($channel['channel_system']) {
			$incl = Config::Get('system','pubstream_incl');
			$excl = Config::Get('system','pubstream_excl');

			if(($incl || $excl) && !MessageFilter::evaluate($item, $incl, $excl)) {
				logger('post is filtered');
				return;
			}
		}

		$abook = q("select * from abook where ( abook_xchan = '%s' OR abook_xchan  = '%s' OR abook_xchan  = '%s') and abook_channel = %d ",
			dbesc($item['author_xchan']),
			dbesc($item['owner_xchan']),
			dbesc($item['source_xchan']),
			intval($channel['channel_id'])
		);

		if ($abook) {
			if (!post_is_importable($channel['channel_id'], $item, $abook)) {
				logger('post is filtered');
				return;
			}
		}

		if (array_key_exists('conversation', $act->obj)) {
			IConfig::Set($item, 'ostatus', 'conversation', $act->obj['conversation'], 1);
		}

		// This isn't perfect but the best we can do for now.
		$item['comment_policy'] = ((isset($act->data['commentPolicy'])) ? $act->data['commentPolicy'] : 'authenticated');

		if (!empty($act->obj['contextHistory'])) {
			IConfig::Set($item, 'activitypub', 'context', $act->obj['contextHistory'], 1);
		}
		elseif (!empty($act->obj['context'])) {
			IConfig::Set($item, 'activitypub', 'context', $act->obj['context'], 1);
		}

		IConfig::Set($item, 'activitypub', 'recips', $act->raw_recips);

		if (intval($act->sigok)) {
			$item['item_verified'] = 1;
		}

		if ($is_child_node) {

			$item['owner_xchan'] = $parent[0]['owner_xchan'];

			if ($parent[0]['parent_mid'] !== $item['parent_mid']) {
				$item['thr_parent'] = $item['parent_mid'];
			}
			else {
				$item['thr_parent'] = $parent[0]['parent_mid'];
			}
			$item['parent_mid'] = $parent[0]['parent_mid'];


			/*
			 *
			 * Check for conversation privacy mismatches
			 * We can only do this if we have a channel and we have fetched the parent
			 *
			 */

			// public conversation, but this comment went rogue and was published privately
			// hide it from everybody except the channel owner

			if (intval($parent[0]['item_private']) === 0) {
				if (intval($item['item_private'])) {
					$item['item_restrict'] = ((isset($item['item_restrict'])) ? $item['item_restrict'] | 1 : 1);
					$item['allow_cid'] = '<' . $channel['channel_hash'] . '>';
					$item['allow_gid'] = $item['deny_cid'] = $item['deny_gid'] = '';
				}
			}

			// private conversation, but this comment went rogue and was published publicly
			// hide it from everybody except the channel owner

			if (intval($parent[0]['item_private'])) {
				if (!intval($item['item_private'])) {
					$item['item_private'] = intval($parent[0]['item_private']);
					$item['allow_cid'] = '<' . $channel['channel_hash'] . '>';
					$item['allow_gid'] = $item['deny_cid'] = $item['deny_gid'] = '';
				}
			}
		}
/*
		if (isset($item['term']) && !PConfig::Get($channel['channel_id'], 'system', 'no_smilies')) {
			foreach ($item['term'] as $t) {
				if ($t['ttype'] === TERM_EMOJI) {
					$class = 'emoji';
					$shortname = ':' . trim($t['term'], ':') . ':';
					if (is_solo_string($shortname, $item['body'])) {
						$class .= ' single-emoji';
					}

					$item['body'] = str_replace($shortname, '[img class="' . $class . '" alt="' . $t['term'] . '" title="' . $t['term'] . '"]' . ($t['imgurl'] ?: $t['url']) . '[/img]', $item['body']);
				}
			}
		}
*/

		// TODO: not implemented
		// self::rewrite_mentions($item);

		$r = q("select id, created, edited from item where mid = '%s' and uid = %d limit 1",
			dbesc($item['mid']),
			intval($item['uid'])
		);

		if ($r) {
			if ($item['edited'] > $r[0]['edited']) {
				$item['id'] = $r[0]['id'];
				$x = item_store_update($item, deliver: false);
			}
			else {
				return;
			}
		}
		else {
			$x = item_store($item, deliver: false, addAndSync: false);
		}

		if ($x['success']) {
			if ($relay && $channel['channel_hash'] === $x['item']['owner_xchan'] && $x['item']['verb'] !== 'Add' && !$is_collection_operation) {
				$approval = Activity::addToCollection($channel, $act->data, $x['item']['parent_mid'], $x['item'], deliver: false);
			}

			if (check_item_source($channel['channel_id'], $x['item']) && in_array($x['item']['obj_type'], ['Event', ACTIVITY_OBJ_EVENT])) {
				event_addtocal($x['item_id'], $channel['channel_id']);
			}

			tag_deliver($channel['channel_id'], $x['item_id']);

			if ($relay && $is_child_node) {
				// We are the owner of this conversation, so send all received comments back downstream
				Master::Summon(['Notifier', 'comment-import', $x['item_id']]);
				if (!empty($approval['item_id'])) {
					Master::Summon(['Notifier', 'comment-import', $approval['item_id']]);
				}
			}

			send_status_notifications($x['item_id'], $x['item']);

			sync_an_item($channel['channel_id'], $x['item_id']);

			if (isset($act->obj['replies']['id'])) {
				App::$cache['as_fetch_collection'][$act->obj['replies']['id']]['channels'][] = $channel['channel_id'];
				App::$cache['as_fetch_collection'][$act->obj['replies']['id']]['force'] = intval($force);
			}
		}
	}

	/**
	 * @brief fetch a thread upwards by either providing a message id or an item/activity pair
	 *
	 * @param array $channel
	 * @param array $observer_hash
	 * @param array $item string|array
	 * @param object $act activitystreams object (optional) default null
	 * @param bool $force disregard permissions and force storage (optional) default false
	 * @return bool
	 */

	static public function fetch_and_store_parents($channel, $observer_hash, $item, $act = null, $force = false) {
		logger('fetching parents');

		if (!$item) {
			return false;
		}

		$p = [];
		$announce_init = false;
		$group_announce_init = false;

		if (is_object($act) && is_array($item)) {
			$p[] = [$act, $item];
			$announce_init = ($item['verb'] === 'Announce');
			$group_announce_init = ($announce_init && $act->actor['type'] === 'Group');
		}

		if (is_string($item)) {
			$mid = $item;
			$item = [
				'parent_mid' => $mid,
				'mid' => ''
			];
		}

		$current_item = $item;

		$i = 0;

		while ($current_item['parent_mid'] !== $current_item['mid']) {
			$cached = ASCache::Get($current_item['parent_mid']);
			if ($cached) {
				// logger('cached: ' . $current_item['parent_mid']);
				$n = $cached;
			}
			else {
				// logger('fetching: ' . $current_item['parent_mid']);
				$n = self::fetch($current_item['parent_mid'], $channel);
				if (!$n) {
					break;
				}
				ASCache::Set($current_item['parent_mid'], $n);
			}

			$a = new ActivityStreams($n);
			if ($a->type === 'Announce' && is_array($a->obj)
				&& array_key_exists('object', $a->obj) && array_key_exists('actor', $a->obj)) {
				// This is a relayed/forwarded Activity (as opposed to a shared/boosted object)
				// Reparse the encapsulated Activity and use that instead
				logger('relayed activity', LOGGER_DEBUG);
				$a = new ActivityStreams($a->obj);
			}

			logger($a->debug(), LOGGER_DATA);

			if (!$a->is_valid()) {
				logger('not a valid activity');
				break;
			}

			$item = Activity::decode_note($a);

			if (!$item) {
				break;
			}

			$hookinfo = [
				'a'    => $a,
				'item' => $item
			];

			call_hooks('fetch_and_store', $hookinfo);

			$item = $hookinfo['item'];

			if ($item) {

				$item['item_fetched'] = true;

				if ($announce_init) {
					// Store the sender of the initial announce
					$item['source_xchan'] = $observer_hash;
					// WARNING: the presence of both source_xchan and non-zero item_uplink here will cause a delivery loop
					$item['item_uplink']  = 0;

					if ($item['item_thread_top']) {
						$item['verb'] = 'Announce';
					}

					if (!$group_announce_init) {
						// Force a new thread if the announce init actor is not a group
						$item['verb'] = 'Announce';
						$item['parent_mid'] = $item['thr_parent'] = $item['mid'];
						$item['item_thread_top'] = 1;
					}

				}
				else {
					$announce_init = ($i === 0 && $item['verb'] === 'Announce');
					$group_announce_init = ($announce_init && $a->actor['type'] === 'Group');
				}

				if (intval($channel['channel_system']) && intval($item['item_private'])) {
					$p = [];
					break;
				}

				if (count($p) > 100) {
					$p = [];
					break;
				}

				array_unshift($p, [$a, $item]);

				if ($item['parent_mid'] === $item['mid']) {
					break;
				}

			}

			$current_item = $item;
			$i++;
		}

		if ($p) {
			foreach ($p as $pv) {
				if ($pv[0]->is_valid()) {
					Activity::store($channel, $observer_hash, $pv[0], $pv[1], false, $force);
				}
			}
			return true;
		}
		return false;
	}


	public static function bb_attach($item) {

		$ret = false;

		if (!(is_array($item['attach']) && $item['attach'])) {
			return $item;
		}

		foreach ($item['attach'] as $a) {

			if (array_key_exists('type', $a) && stripos($a['type'], 'image') !== false) {
				// don't add inline image if it's an svg and we already have an inline svg
				if ($a['type'] === 'image/svg+xml' && strpos($item['body'], '[/svg]')) {
					continue;
				}
				// Friendica attachment weirdness
				// Check both the attachment image and href since they can be different and the one in the href is a different link with different resolution.
				// Otheriwse you'll get duplicated images
				if (isset($a['image'])) {
					if (self::media_not_in_body($a['image'], $item['body']) && self::media_not_in_body($a['href'], $item['body'])) {
						if (isset($a['name']) && $a['name']) {
							$alt = htmlspecialchars($a['name'], ENT_QUOTES);
							$item['body'] = '[img=' . $a['href']  . ']' . $alt . '[/img]' . "\r\n" . $item['body'];
						} else {
							$item['body'] = '[img]' . $a['href'] . '[/img]' . "\r\n" . $item['body'];
						}
					}
					continue;
				}
				elseif (self::media_not_in_body($a['href'], $item['body'])) {
					if (isset($a['name']) && $a['name']) {
						$alt = htmlspecialchars($a['name'], ENT_QUOTES);
						$item['body'] = '[img=' . $a['href']  . ']' . $alt . '[/img]' . "\r\n" . $item['body'];
					} else {
						$item['body'] = '[img]' . $a['href'] . '[/img]' . "\r\n" . $item['body'];
					}
				}
			}

			if (array_key_exists('type', $a) && stripos($a['type'], 'video') !== false) {
				if (self::media_not_in_body($a['href'], $item['body'])) {
					$item['body'] = '[video]' . $a['href'] . '[/video]' . "\r\n" . $item['body'];
				}
			}
			if (array_key_exists('type', $a) && stripos($a['type'], 'audio') !== false) {
				if (self::media_not_in_body($a['href'], $item['body'])) {
					$item['body'] = '[audio]' . $a['href'] . '[/audio]' . "\r\n" . $item['body'];
				}
			}
			//if (array_key_exists('type', $a) && stripos($a['type'], 'activity') !== false) {
				//if (self::media_not_in_body($a['href'], $item['body'])) {
					//$item = self::get_quote($a['href'], $item);
				//}
			//}
		}

		return $item;
	}


	// check for the existence of existing media link in body

	public static function media_not_in_body($s, $body) {

		if (empty($body)) {
			return true;
		}

		$s_alt = htmlspecialchars($s, ENT_QUOTES, 'UTF-8');

		if (
			(strpos($body, ']' . $s . '[/img]') === false) &&
			(strpos($body, '[img=' . $s . ']') === false) &&
			(strpos($body, ']' . $s . '[/zmg]') === false) &&
			(strpos($body, '[zmg=' . $s . ']') === false) &&
			(strpos($body, ']' . $s . '[/video]') === false) &&
			(strpos($body, ']' . $s . '[/zvideo]') === false) &&
			(strpos($body, ']' . $s . '[/audio]') === false) &&
			(strpos($body, ']' . $s . '[/zaudio]') === false) &&
			(strpos($body, ']' . $s_alt . '[/img]') === false) &&
			(strpos($body, '[img=' . $s_alt . ']') === false) &&
			(strpos($body, ']' . $s_alt . '[/zmg]') === false) &&
			(strpos($body, '[zmg=' . $s_alt . ']') === false) &&
			(strpos($body, ']' . $s_alt . '[/video]') === false) &&
			(strpos($body, ']' . $s_alt . '[/zvideo]') === false) &&
			(strpos($body, ']' . $s_alt . '[/audio]') === false) &&
			(strpos($body, ']' . $s_alt . '[/zaudio]') === false)
		) {
			return true;
		}
		return false;
	}

	static function bb_content($content, $field) {

		require_once('include/html2bbcode.php');
		require_once('include/event.php');
		$ret = false;

		if (array_key_exists($field, $content)) {
			if (is_array($content[$field])) {
				foreach ($content[$field] as $k => $v) {
					$ret .= html2bbcode($v);
					// save this for auto-translate or dynamic filtering
					// $ret .= '[language=' . $k . ']' . html2bbcode($v) . '[/language]';
				}
			}
			else {
				if ($field === 'bbcode' && array_key_exists('bbcode', $content)) {
					$ret = $content[$field];
				}
				else {
					$ret = html2bbcode($content[$field]);
				}
			}
		}

		if ($field === 'content' && array_key_exists('event', $content) && (!strpos($ret, '[event'))) {
			$ret .= format_event_bbcode($content['event']);
		}

		return $ret;
	}

	static function get_content($act) {

		$content = [];
		$event   = null;

		if ((!$act) || (!is_array($act))) {
			return $content;
		}

		if (isset($act['type']) && $act['type'] === 'Event') {
			$adjust              = false;
			$event               = [];
			$event['event_hash'] = $act['id'];
			if (array_key_exists('startTime', $act) && strpos($act['startTime'], -1, 1) === 'Z') {
				$adjust           = true;
				$event['adjust']  = 1;
				$event['dtstart'] = datetime_convert('UTC', 'UTC', $act['startTime'] . (($adjust) ? '' : 'Z'));
			}
			if (array_key_exists('endTime', $act)) {
				$event['dtend'] = datetime_convert('UTC', 'UTC', $act['endTime'] . (($adjust) ? '' : 'Z'));
			}
			else {
				$event['nofinish'] = true;
			}
		}

		foreach (['name', 'summary', 'content'] as $a) {
			if (($x = self::get_textfield($act, $a)) !== false) {
				$content[$a] = $x;
			}
		}

		if ($event) {
			$event['summary'] = $content['name'];
			if (!$event['summary']) {
				if ($content['summary']) {
					$event['summary'] = html2plain($content['summary']);
				}
			}
			$event['description'] = html2bbcode($content['content']);
			if ($event['summary'] && $event['dtstart']) {
				$content['event'] = $event;
			}
		}

		if (array_path_exists('source/mediaType', $act) && array_path_exists('source/content', $act)) {
			if (in_array($act['source']['mediaType'], ['text/bbcode'])) {
				$content['bbcode'] = purify_html($act['source']['content']);
			}
		}

		return $content;
	}

	static function get_textfield($act, $field): null|string|array {
		$content = null;

		if (array_key_exists($field, $act) && $act[$field]) {
			$content = purify_html($act[$field]);
		}
		elseif (array_key_exists($field . 'Map', $act) && $act[$field . 'Map']) {
			foreach ($act[$field . 'Map'] as $k => $v) {
				$content[escape_tags($k)] = purify_html($v);
			}
		}

		return $content;
	}

	// Find either an Authorization: Bearer token or 'token' request variable
	// in the current web request and return it
	static function token_from_request() {

		foreach (['REDIRECT_REMOTE_USER', 'HTTP_AUTHORIZATION'] as $s) {
			$auth = ((array_key_exists($s, $_SERVER) && strpos($_SERVER[$s], 'Bearer ') === 0)
				? str_replace('Bearer ', EMPTY_STR, $_SERVER[$s])
				: EMPTY_STR
			);
			if ($auth) {
				break;
			}
		}

		if (!$auth) {
			if (array_key_exists('token', $_REQUEST) && $_REQUEST['token']) {
				$auth = $_REQUEST['token'];
			}
		}

		return $auth;
	}

	static function find_best_identity($xchan) {

		if (filter_var($xchan, FILTER_VALIDATE_URL)) {
			$r = q("SELECT hubloc_hash, hubloc_network FROM hubloc WHERE hubloc_id_url = '%s' AND hubloc_network IN ('zot6', 'activitypub') AND hubloc_deleted = 0",
				dbesc($xchan)
			);
			if ($r) {
				$r = Libzot::zot_record_preferred($r);
				logger('find_best_identity: ' . $xchan . ' > ' . $r['hubloc_hash']);
				return $r['hubloc_hash'];
			}
		}

		return $xchan;

	}

	static function get_cached_actor($id) {

		// remove any fragments like #main-key since these won't be present in our cached data
		$cache_url = ((strpos($id, '#')) ? substr($id, 0, strpos($id, '#')) : $id);
		$actor = XConfig::Get($cache_url, 'system', 'actor_record');

		if ($actor && isset($actor['actor_cache_date']) && $actor['actor_cache_date'] > datetime_convert('UTC', 'UTC', ' now - 3 days')) {
			unset($actor['actor_cache_date']);
			return $actor;
		}

		// try other get_cached_actor providers (e.g. diaspora)
		$hookdata = [
			'id'    => $id,
			'actor' => null
		];

		call_hooks('get_cached_actor_provider', $hookdata);

		return $hookdata['actor'];
	}

	static function get_actor($actor_id, $force = false) {
		// remove fragment
		$actor_id = ((strpos($actor_id, '#')) ? substr($actor_id, 0, strpos($actor_id, '#')) : $actor_id);

		$actor = ((!$force) ? self::get_cached_actor($actor_id) : null);

		if ($actor) {
			return $actor;
		}

		$actor = self::fetch($actor_id);

		if ($actor) {
			return $actor;
		}

		return null;
	}

	static function get_unknown_actor($act) {

		// try other get_actor providers (e.g. diaspora)
		$hookdata = [
			'activity' => $act,
			'actor' => null
		];

		call_hooks('get_actor_provider', $hookdata);

		return $hookdata['actor'];
	}

	static function get_actor_hublocs($url, $options = 'all') {

		$url = ((strpos($url, '#')) ? substr($url, 0, strpos($url, '#')) : $url);

		switch ($options) {
			case 'activitypub':
				$hublocs = q("select * from hubloc left join xchan on hubloc_hash = xchan_hash where hubloc_hash = '%s' and hubloc_deleted = 0 order by hubloc_id desc",
					dbesc($url)
				);
				break;
			case 'zot6':
				$hublocs = q("select * from hubloc left join xchan on hubloc_hash = xchan_hash where hubloc_id_url = '%s' and hubloc_deleted = 0 order by hubloc_id desc",
					dbesc($url)
				);
				break;
			case 'all':
			default:
				$hublocs = q("select * from hubloc left join xchan on hubloc_hash = xchan_hash where ( hubloc_id_url = '%s' OR hubloc_hash = '%s' ) and hubloc_deleted = 0 order by hubloc_id desc",
					dbesc($url),
					dbesc($url)
				);
				break;
		}

		return $hublocs;
	}

	static function get_actor_collections($url) {
		$ret          = [];
		$actor_record = XConfig::Get($url, 'system', 'actor_record');
		if (!$actor_record) {
			return $ret;
		}

		foreach (['inbox', 'outbox', 'followers', 'following'] as $collection) {
			if (isset($actor_record[$collection]) && $actor_record[$collection]) {
				$ret[$collection] = $actor_record[$collection];
			}
		}
		if (!empty($actor_record['endpoints']['sharedInbox'])) {
			$ret['sharedInbox'] = $actor_record['endpoints']['sharedInbox'];
		}

		return $ret;
	}


	static function get_actor_protocols($actor) {
		$ret = [];

		if (!array_key_exists('tag', $actor) || empty($actor['tag']) || !is_array($actor['tag'])) {
			return $ret;
		}

		foreach ($actor['tag'] as $t) {
			// TODO: implement FEP-fb2a at the sending side and deprecate PropertyValue
			if ((isset($t['type']) && $t['type'] === 'PropertyValue') &&
				(isset($t['name']) && $t['name'] === 'Protocol') &&
				isset($t['value'])
			) {
				$ret[] = trim($t['value']);
			}

			// FEP-fb2a - actor metadata
			if ((isset($t['type']) && $t['type'] === 'Note') &&
				(isset($t['name']) && $t['name'] === 'Protocols') &&
				isset($t['content'])
			) {
				$ret = array_map('trim', explode(',', $t['content']));
			}

		}

		return $ret;
	}

	static function get_quote($url) {
		$ret = [];
		$a = null;

		$cached = ASCache::Get($url);
		if ($cached) {
			// logger('cached: ' . $url);
			$a = unserialise($cached);
		}
		else {
			// logger('fetching: ' . $url);
			$a = self::fetch($url);
			if ($a) {
				ASCache::Set($url, $a);
			}
		}


		if ($a) {
			$act = new ActivityStreams($a);

			if ($act->is_valid()) {
				$decoded = self::decode_note($act);

				$bbcode = "[share author='" . urlencode($act->actor['name'] ?? $act->actor['preferredUsername']) .
					"' profile='" . $act->actor['id'] .
					"' avatar='" . ($act->actor['icon']['url'] ?? z_root() . '/' . get_default_profile_photo(80)) .
					"' link='" . $url .
					"' auth='" . ((is_matrix_url($act->actor['id'])) ? 'true' : 'false') .
					"' posted='" . $act->obj['published'] .
					"' message_id='" . $act->obj['id'] .
					"']";

				$bbcode  .= $decoded['body'];
				$bbcode  .= '[/share]';

				$ret['bbcode'] = $bbcode;
				$ret['url'] = $decoded['plink'];
				$ret['mid'] = $decoded['mid'];
				$ret['term'] = $decoded['term'] ?? [];

			}
		}

		return $ret;
	}

	static function get_attributed_to_actor_url($act) {

		$url = '';

		if (!isset($act->obj['attributedTo'])) {
			return $url;
		}

		if (is_string($act->obj['attributedTo'])) {
			$url = $act->obj['attributedTo'];
		}

		if (is_array($act->obj['attributedTo'])) {
			foreach($act->obj['attributedTo'] as $a) {
				if (is_array($a) && isset($a['type']) && $a['type'] === 'Person') {
					if (isset($a['id'])) {
						$url = $a['id'];
						break;
					}
				}
				elseif (is_string($a)) {
					$url = $a;
					break;
				}
			}
		}

		return $url;

	}

	public static function ap_context($contextType = null): array {
		return ['@context' => [
			ACTIVITYSTREAMS_JSONLD_REV,
			'https://w3id.org/security/v1',
		//	'https://www.w3.org/ns/did/v1',
		//	'https://w3id.org/security/multikey/v1',
		//	'https://w3id.org/security/data-integrity/v1',
			'https://purl.archive.org/socialweb/webfinger',
			self::ap_schema($contextType)
		]];
	}

	public static function ap_schema($contextType = null): array {
		// $contextType is reserved for future use so that the caller can specify
		// a limited subset of the entire schema definition for particular activities.

		return [
			'zot'              => z_root() . '/apschema#',

			'contextHistory'   => 'https://w3id.org/fep/171b/contextHistory',
			'schema'           => 'http://schema.org#',
			'ostatus'          => 'http://ostatus.org#',
			'diaspora'         => 'https://diasporafoundation.org/ns/',
			'litepub'          => 'http://litepub.social/ns#',
			'toot'             => 'http://joinmastodon.org/ns#',

			'commentPolicy'    => 'zot:commentPolicy',
			'Bookmark'         => 'zot:Bookmark',
			'Category'         => 'zot:Category',
			'Emoji'            => 'toot:Emoji',

			'directMessage'    => 'litepub:directMessage',

			'PropertyValue'    => 'schema:PropertyValue',
			'value'            => 'schema:value',
			'uuid'             => 'schema:identifier',

			'conversation'     => 'ostatus:conversation',

			'guid'             => 'diaspora:guid',

			'manuallyApprovesFollowers' => 'as:manuallyApprovesFollowers',
			'Hashtag'          => 'as:Hashtag',

			'quoteUrl'         => 'as:quoteUrl',
		];

	}

	/**
	 * @brief Builds the activity packet and signs it if $channel is provided.
	 *
	 * @param array $obj
	 * @param array $channel (optional) default []
	 * @param bool $json_encode (optional) default true
	 * @return string|array
	 */

	public static function build_packet(array $obj, array $channel = [], bool $json_encode = true): string|array {
		$arr = array_merge(Activity::ap_context(), $obj);

		if ($channel) {
			$proof = (new JcsEddsa2022)->sign($arr, $channel);
			$arr['proof'] = $proof;

			$signature = LDSignatures::sign($arr, $channel);
			$arr['signature'] = $signature;
		}

		if ($json_encode) {
			return json_encode($arr, JSON_UNESCAPED_SLASHES);
		}

		return $arr;
	}

	/**
	 * @brief Prepares the arguments and inititates the Fetchparents or Zotconvo daemon.
	 * @param string $observer
	 *
	 */

	public static function init_background_fetch(string $observer_hash = '') {
		if (isset(App::$cache['zot_fetch_objects'])) {
			foreach (App::$cache['zot_fetch_objects'] as $mid => $info) {
				$force = $info['force'];
				$channels_str = '';

				foreach ($info['channels'] as $c) {
					if ($channels_str) {
						$channels_str .= ',';
					}
					$channels_str .= $c;
				}

				Master::Summon(['Zotconvo', $channels_str, $mid, $force]);
			}
		}

		if (!$observer_hash) {
			logger('Attempt to initiate Fetchparents or Convo daemon without observer');
			return;
		}

		if (isset(App::$cache['as_fetch_objects'])) {
			foreach (App::$cache['as_fetch_objects'] as $mid => $info) {
				$force = $info['force'];
				$channels_str = '';

				foreach ($info['channels'] as $c) {
					if ($channels_str) {
						$channels_str .= ',';
					}
					$channels_str .= $c;
				}

				Master::Summon(['Fetchparents', $channels_str, $observer_hash, $mid, $force]);
			}
		}

		if (isset(App::$cache['as_fetch_collection'])) {
			$connected = q("SELECT abook_id FROM abook
				WHERE abook_xchan = '%s' LIMIT 1",
				dbesc($observer_hash)
			);

			foreach (App::$cache['as_fetch_collection'] as $mid => $info) {
				$force = $info['force'];

				if ($connected && !$force) {
					// If the sender is a connnection with someone on the hub,
					// we will already receive any additional comments to their posts
					continue;
				}

				$channels_str = '';

				foreach ($info['channels'] as $c) {
					if ($channels_str) {
						$channels_str .= ',';
					}
					$channels_str .= $c;
				}

				Master::Summon(['Convo', $channels_str, $observer_hash, $mid, $force]);
			}
		}

	}

	public static function addToCollection($channel, $object, $target, $sourceItem = null, $deliver = true) {
		if (!isset($channel['xchan_hash'])) {
			$channel = channelx_by_hash($channel['channel_hash']);
		}

		$item = ((new Item())
			->setUid($channel['channel_id'])
			->setVerb('Add')
			->setAuthorXchan($channel['channel_hash'])
			->setOwnerXchan($channel['channel_hash'])
			->setObj($object)
			->setObjType($object['type'])
			->setParentMid(str_replace('/conversation/','/item/', $target))
			->setThrParent(str_replace('/conversation/','/item/', $target))
			->setTgtType('Collection')
			->setTarget([
				'id' => str_replace('/item/','/conversation/', $target),
				'type' => 'Collection',
				'attributedTo' => z_root() . '/channel/' . $channel['channel_address'],
			])
		);
		if ($sourceItem) {
			$item->setSourceXchan($sourceItem['source_xchan'])
				->setAllowCid($sourceItem['allow_cid'])
				->setAllowGid($sourceItem['allow_gid'])
				->setDenyCid($sourceItem['deny_cid'])
				->setDenyGid($sourceItem['deny_gid'])
				->setPrivate($sourceItem['item_private'])
				->setRestrict($sourceItem['item_restrict'])
				->setHidden($sourceItem['item_hidden'])
				->setDelayed($sourceItem['item_delayed'])
				->setUnpublished($sourceItem['item_unpublished'])
				->setBlocked($sourceItem['item_blocked'])
				->setType($sourceItem['item_type'])
				->setCommentPolicy($sourceItem['comment_policy'])
				->setPublicPolicy($sourceItem['public_policy'])
				->setPostopts($sourceItem['postopts']);
		}
		$result = post_activity_item($item->toArray(), deliver: $deliver, channel: $channel, observer: $channel, addAndSync: false);
		logger('addToCollection: ' . print_r($result, true));

		return $result;
	}

	public static function removeFromCollection($channel, $object, $target, $deliver = true) {
		if (!isset($channel['xchan_hash'])) {
			$channel = channelx_by_hash($channel['channel_hash']);
		}

		$item = ((new Item())
			->setUid($channel['channel_id'])
			->setVerb('Remove')
			->setAuthorXchan($channel['channel_hash'])
			->setOwnerXchan($channel['channel_hash'])
			->setObj($object)
			->setObjType($object['type'])
			->setParentMid(str_replace('/conversation/','/item/', $target))
			->setThrParent(str_replace('/conversation/','/item/', $target))
			->setReplyto(z_root() . '/channel/' . $channel['channel_address'])
			->setTgtType('Collection')
			->setTarget([
				'id' => str_replace('/item/','/conversation/', $target),
				'type' => 'Collection',
				'attributedTo' => z_root() . '/channel/' . $channel['channel_address']
			])
		);

		$result = post_activity_item($item->toArray(), deliver: $deliver, channel: $channel, observer: $channel, addAndSync: false);
		logger('removeFromCollection: ' . print_r($result, true));
		return $result;
	}


	/**
	 * @brief Retrieves message ID from activity object.
	 * @param object $act Activity object
	 * @return string Message ID or empty string if not found
	 */
	public static function getMessageID($act): string
	{
		if (ActivityStreams::is_response_activity($act->type) || $act->objprop('type') === 'Profile') {
			return $act->id;
		}

		return $act->objprop('id', null)
			?? (is_string($act->obj) ? $act->obj : null)
			?? '';
	}

	/**
	 * @brief Retrieves the UUID from an activity object.
	 * @param object $act Activity object
	 * @return string UUID or empty string if not found
	 */
	public static function getUUID($act): string
	{
		if (ActivityStreams::is_response_activity($act->type)) {
			return $act->data['uuid']
				?? $act->data['diaspora:guid']
				?? '';
		}

		return $act->objprop('uuid', null)
			?? $act->objprop('diaspora:guid', null)
			?? '';
	}

	public static function pasteQuote(string $body, array $quote): string
	{
		// Escape URLs for regex safety
		$urls = array_map('preg_quote', [$quote['url'], $quote['mid']], array_fill(0, 2, '/'));

		$patterns = [];
		foreach ($urls as $url) {
			// Match both plain and BBCode-style references, with optional line breaks or spaces
			$patterns[] = '/RE:\s*(?:\[url=' . $url . '\]' . $url . '\[\/url\]|' . $url . ')[\s\r\n]?/i';
		}

		$found = false;
		foreach ($patterns as $pattern) {
			if (preg_match($pattern, $body)) {
				$found = true;
				$body = preg_replace($pattern, $quote['bbcode'], $body);
				break;
			}
		}

		if (!$found) {
			if (!empty($body)) {
				$body .= "\r\n\r\n";
			}
			$body .= $quote['bbcode'];
		}

		return $body;
	}

}
