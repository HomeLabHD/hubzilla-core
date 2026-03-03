<?php

/**
 *   * Name: Channel Activity
 *   * Description: A widget that provides an overview of channels that require your attention and quick links to content that you have recently created or edited
 */

namespace Zotlabs\Widget;

use App;
use Zotlabs\Lib\Apps;
use Zotlabs\Lib\Queue;
use Zotlabs\Lib\QueueWorkerStats;

class Channel_activities {

	public static $activities = [];
	public static $uid = null;
	public static $limit = 3;
	public static $channel = [];

	public static function widget($arr) {
		if (!local_channel()) {
			return EMPTY_STR;
		}

		self::$uid = local_channel();
		self::$channel = App::get_channel();

		if (is_site_admin()) {
			self::get_system_status();
		}
		self::get_photos_activity();
		self::get_files_activity();
		self::get_webpages_activity();
		self::get_channels_activity();

		$hookdata = [
			'channel' => self::$channel,
			'activities' => self::$activities,
			'limit' => self::$limit
		];

		call_hooks('channel_activities_widget', $hookdata);

		$activity_html = '';

		if ($hookdata['activities']) {
			$keys = array_column($hookdata['activities'], 'date');
			array_multisort($keys, SORT_DESC, $hookdata['activities']);

			foreach ($hookdata['activities'] as $a) {
				$activity_html .= replace_macros(
					get_markup_template($a['tpl']),
					[
						'$url'   => $a['url'] ?? null,
						'$icon'  => $a['icon'],
						'$label' => $a['label'],
						'$items' => $a['items'],
						'$labels' => $a['labels'] ?? [],
					]
				);
			}
		}

		$tpl = get_markup_template('channel_activities_widget.tpl');

		return replace_macros($tpl, [
			'$welcome'        => t('Welcome'),
			'$channel_name'  => self::$channel['channel_name'],
			'$no_activities' => t('No recent activities'),
			'$activities'    => $hookdata['activities'],
			'$activity_html' => $activity_html
		]);

	}

	private static function get_photos_activity() {

		$r = q("SELECT edited, height, width, imgscale, description, filename, resource_id FROM photo WHERE uid = %d
			AND photo_usage = 0 AND is_nsfw = 0 AND imgscale = 3
			ORDER BY edited DESC LIMIT 6",
			intval(self::$uid)
		);

		if (!$r) {
			return;
		}

		foreach($r as $rr) {
			$i[] = [
				'url' => z_root() . '/photos/' . self::$channel['channel_address'] . '/image/' . $rr['resource_id'],
				'edited' => datetime_convert('UTC', date_default_timezone_get(), $rr['edited']),
				'width' => $rr['width'],
				'height' => $rr['height'],
				'alt' => (($rr['description']) ? $rr['description'] : $rr['filename']),
				'src' => z_root() . '/photo/' . $rr['resource_id'] . '-' . $rr['imgscale']
			];
		}

		self::$activities['photos'] = [
			'label' => t('Photos'),
			'icon' => 'image',
			'url' => z_root() . '/photos/' . self::$channel['channel_address'],
			'date' => $r[0]['edited'],
			'items' => $i,
			'tpl' => 'channel_activities_photos.tpl'
		];

	}

	private static function get_files_activity() {

		$r = q("SELECT * FROM attach WHERE uid = %d
			AND is_dir = 0 AND is_photo = 0
			ORDER BY edited DESC LIMIT %d",
			intval(self::$uid),
			intval(self::$limit)
		);

		if (!$r) {
			return;
		}

		foreach($r as $rr) {
			$i[] = [
				'url' => z_root() . '/cloud/' . self::$channel['channel_address'] . '/' . rtrim($rr['display_path'], $rr['filename']) . '#' . $rr['id'],
				'summary' => $rr['filename'],
				'footer' => datetime_convert('UTC', date_default_timezone_get(), $rr['edited'])
			];
		}

		self::$activities['files'] = [
			'label' => t('Files'),
			'icon' => 'folder',
			'url' => z_root() . '/cloud/' . self::$channel['channel_address'],
			'date' => $r[0]['edited'],
			'items' => $i,
			'tpl' => 'channel_activities.tpl'
		];

	}

	private static function get_webpages_activity() {

		if(!Apps::system_app_installed(self::$uid, 'Webpages')) {
			return;
		}

		$r = q("SELECT * FROM iconfig LEFT JOIN item ON iconfig.iid = item.id WHERE item.uid = %d
			AND iconfig.cat = 'system' AND iconfig.k = 'WEBPAGE' AND item_type = %d
			ORDER BY item.edited DESC LIMIT %d",
			intval(self::$uid),
			intval(ITEM_TYPE_WEBPAGE),
			intval(self::$limit)
		);

		if (!$r) {
			return;
		}

		foreach($r as $rr) {
			$summary = html2plain(purify_html(bbcode($rr['body'], ['drop_media' => true, 'tryoembed' => false])), 85, true);
			if ($summary) {
				$summary = substr_words(htmlentities($summary, ENT_QUOTES, 'UTF-8', false), 85);
			}

			$i[] = [
				'url' => z_root() . '/page/' . self::$channel['channel_address'] . '/' . $rr['v'],
				'title' => $rr['title'],
				'summary' => $summary,
				'footer' => datetime_convert('UTC', date_default_timezone_get(), $rr['edited'])
			];
		}

		self::$activities['webpages'] = [
			'label' => t('Webpages'),
			'icon' => 'layout-text-sidebar',
			'url' => z_root() . '/webpages/' . self::$channel['channel_address'],
			'date' => $r[0]['edited'],
			'items' => $i,
			'tpl' => 'channel_activities.tpl'
		];

	}

	private static function get_channels_activity() {

		$account = App::get_account();

		$r = q("SELECT channel_id, channel_name, xchan_addr, xchan_photo_s FROM channel
			LEFT JOIN xchan ON channel_hash = xchan_hash
			WHERE channel_account_id = %d
			AND channel_id != %d AND channel_removed = 0",
			intval($account['account_id']),
			intval(self::$uid)
		);

		if (!$r) {
			return;
		}

		$channels_activity = 0;

		foreach($r as$rr) {

			$intros = q("SELECT COUNT(abook_id) AS total FROM abook WHERE abook_channel = %d
				AND abook_pending = 1 AND abook_self = 0 AND abook_ignored = 0",
				intval($rr['channel_id'])
			);

			$notices = q("SELECT COUNT(id) AS total FROM notify WHERE uid = %d AND seen = 0",
				intval($rr['channel_id'])
			);

			if (!$intros[0]['total'] && !$notices[0]['total']) {
				continue;
			}

			$footer = '';

			if ($intros[0]['total']) {
				$footer .= intval($intros[0]['total']) . ' ' . tt('new connection', 'new connections', intval($intros[0]['total']), 'noun');
				if ($notices[0]['total']) {
					$footer .= ', ';
				}
			}
			if ($notices[0]['total']) {
				$footer .= intval($notices[0]['total']) . ' ' . tt('notice', 'notices', intval($notices[0]['total']), 'noun');
			}

			$tpl = get_markup_template('manage_channel_item.tpl');

			$i[] = [
				'url'     => z_root() . '/manage/' . $rr['channel_id'],
				'title'   => '',
				'summary' => replace_macros($tpl, [
					'$photo' => $rr['xchan_photo_s'],
					'$name'  => $rr['channel_name'],
					'$addr'  => $rr['xchan_addr'],
				]),
				'footer'  => $footer
			];

			$channels_activity++;

		}

		if(!$channels_activity) {
			return;
		}

		self::$activities['channels'] = [
			'label' => t('Channels'),
			'icon' => 'house',
			'url' => z_root() . '/manage',
			'date' => datetime_convert(),
			'items' => $i,
			'tpl' => 'channel_activities.tpl'
		];

	}

	private static function get_system_status(): void {
		self::$activities['status'] = [
			'label' => t('System status'),
			'icon' => 'gpu-card',
			'date' => datetime_convert(),
			'items' => [
				'loadavg' => '0 / 0 / 0',
				'dbqueries' => 0,
				'outqueue' => 0,
				'queueworkers' => 0,
				'workqsz' => 0,
				'ts' => time(),
			],
			'tpl' => 'system_status_widget.tpl',
			'labels' => [
				'loadavg' => t('Load average'),
				'dbqueries' => t('DB queries/sec'),
				'outqueue' => t('Output queue'),
				'queueworkers' => t('Queue workers'),
				'workqsz' => t('Work queue size'),
			],
		];
	}

}

