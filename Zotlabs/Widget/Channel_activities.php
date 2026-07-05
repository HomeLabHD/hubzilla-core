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
use Zotlabs\Lib\SystemProfiler;

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
		self::get_files_activity('uncategorized');
		self::get_files_activity('document');
		self::get_files_activity('audio');
		self::get_files_activity('video');
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

	private static function get_files_activity($category) {

		$not = '';
		$mime_types = stringify_array(self::get_mime_types_by_category($category));

		switch($category) {
			case 'audio':
				$label = t('Audios');
				break;
			case 'video':
				$label = t('Videos');
				break;
			case 'document':
				$label = t('Documents');
				break;
			default:
				$label = t('Uploads');
				$not = 'NOT';
		}

		$r = q("SELECT * FROM attach WHERE uid = %d
			AND is_dir = 0 AND is_photo = 0 AND filetype $not IN ($mime_types)
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

		self::$activities[$category] = [
			'label' => $label,
			'icon' => 'folder',
			'url' => z_root() . '/cloud/' . self::$channel['channel_address'],
			'date' => $r[0]['edited'],
			'items' => $i,
			'tpl' => 'channel_activities.tpl'
		];

	}

	private static function get_mime_types_by_category($category): array
	{
		$mime_types = [
			'document' => [
				'application/vnd.ms-powerpoint',
				'application/vnd.ms-excel',
				'application/vnd.sun.xml.writer',
				'application/vnd.oasis.opendocument.text',
				'application/vnd.oasis.opendocument.text-flat-xml',
				'application/vnd.sun.xml.calc',
				'application/vnd.oasis.opendocument.spreadsheet',
				'application/vnd.oasis.opendocument.spreadsheet-flat-xml',
				'application/vnd.sun.xml.impress',
				'application/vnd.oasis.opendocument.presentation',
				'application/vnd.oasis.opendocument.presentation-flat-xml',
				'application/vnd.sun.xml.draw',
				'application/vnd.oasis.opendocument.graphics',
				'application/vnd.oasis.opendocument.graphics-flat-xml',
				'application/vnd.oasis.opendocument.chart',
				'application/vnd.sun.xml.writer.global',
				'application/vnd.oasis.opendocument.text-master',
				'application/vnd.sun.xml.writer.template',
				'application/vnd.oasis.opendocument.text-template',
				'application/vnd.oasis.opendocument.text-master-template',
				'application/vnd.sun.xml.calc.template',
				'application/vnd.oasis.opendocument.spreadsheet-template',
				'application/vnd.sun.xml.impress.template',
				'application/vnd.oasis.opendocument.presentation-template',
				'application/vnd.sun.xml.draw.template',
				'application/vnd.oasis.opendocument.graphics-template',
				'application/msword',
				'application/msword',
				'application/vnd.ms-excel',
				'application/vnd.ms-powerpoint',
				'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'application/vnd.ms-word.document.macroEnabled.12',
				'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
				'application/vnd.ms-word.template.macroEnabled.12',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
				'application/vnd.ms-excel.template.macroEnabled.12',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
				'application/vnd.ms-excel.sheet.macroEnabled.12',
				'application/vnd.openxmlformats-officedocument.presentationml.presentation',
				'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
				'application/vnd.openxmlformats-officedocument.presentationml.template',
				'application/vnd.ms-powerpoint.template.macroEnabled.12',
				'application/vnd.wordperfect',
				'application/x-aportisdoc',
				'application/x-hwp',
				'application/vnd.ms-works',
				'application/vnd.ms-office',
				'application/x-mswrite',
				'application/x-dif-document',
				'text/spreadsheet',
				'application/x-dbase',
				'application/vnd.lotus-1-2-3',
				'application/coreldraw',
				'application/vnd.visio2013',
				'application/vnd.visio',
				'application/vnd.ms-visio.drawing',
				'application/x-mspublisher',
				'application/x-sony-bbeb',
				'application/x-gnumeric',
				'application/macwriteii',
				'application/x-iwork-numbers-sffnumbers',
				'application/vnd.oasis.opendocument.text-web',
				'application/x-pagemaker',
				'text/rtf',
				'text/plain',
				'application/x-fictionbook+xml',
				'application/clarisworks',
				'application/x-iwork-pages-sffpages',
				'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
				'application/x-iwork-keynote-sffkey',
				'application/x-abiword',
				'application/vnd.sun.xml.chart',
				'application/x-t602',
				'application/pdf',
			],

			'audio' => [
				'audio/mpeg',        // MP3
				'audio/mp3',
				'audio/wav',         // WAV
				'audio/x-wav',
				'audio/webm',        // WebM audio
				'audio/ogg',         // OGG
				'audio/aac',         // AAC
				'audio/flac',        // FLAC
				'audio/x-flac',
				'audio/mp4',         // M4A / MP4 audio
				'audio/x-m4a',
				'audio/3gpp',        // 3GP audio
				'audio/3gpp2',
				'audio/amr',         // AMR
				'audio/x-ms-wma',    // Windows Media Audio
				'audio/basic',       // µ-law / basic audio
			],

			'video' => [
				'video/mp4',          // MP4
				'video/x-msvideo',    // AVI
				'video/x-ms-wmv',     // WMV
				'video/mpeg',         // MPEG
				'video/ogg',          // OGG/Theora
				'video/webm',         // WebM
				'video/3gpp',         // 3GP
				'video/3gpp2',
				'video/quicktime',    // MOV
				'video/x-flv',        // Flash Video
				'video/x-matroska',   // MKV
				'video/mp2t',         // MPEG-TS (.ts)
			]
		];

		if ($category === 'uncategorized') {
			return array_merge(...array_values($mime_types));
		}

		return $mime_types[$category];
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
		head_add_js('/view/js/admin_system_status.js');

		$profiler = SystemProfiler::isEnabled();

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
				'profiler' => $profiler,
			],
			'tpl' => 'system_status_widget.tpl',
			'labels' => [
				'loadavg' => t('Load average'),
				'dbqueries' => t('DB queries/sec'),
				'outqueue' => t('Output queue'),
				'queueworkers' => t('Queue workers'),
				'workqsz' => t('Work queue size'),
				'profiler' => t('Profiling'),
				'disable' => t('Disable'),
				'enable' => t('Enable'),
				'active' => t('Active'),
				'configure' => t('Configure...'),
			],
		];
	}

}

