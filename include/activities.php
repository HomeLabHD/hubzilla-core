<?php /** @file */

use Zotlabs\Lib\Activity;
use Zotlabs\Daemon\Master;

function profile_activity($changed, $value) {

	if (!local_channel() || !is_array($changed) || !count($changed)) {
		return;
	}

	if (!get_pconfig(local_channel(), 'system', 'post_profilechange')) {
		return;
	}

	$channel = App::get_channel();

	$arr['verb'] = 'Update';
	$arr['obj_type'] = 'Profile';

	$channel_link = '[url=' . z_root() . '/channel/' . $channel['channel_address'] . ']' . $channel['channel_name'] . '[/url]';

	$changes = '';
	$t = count($changed);
	$z = 0;
	$photo = false;
	foreach ($changed as $ch) {
		if (strlen($changes)) {
			if ($z == ($t - 1)) {
				$changes .= t(' and ');
			} else {
				$changes .= t(', ');
			}
		}

		if (in_array($ch, [t('Profile Photo'), t('Cover Photo')])) {
			$photo = true;
			$photo_size = (($ch === t('Profile Photo')) ? 4 : 8);
		}

		$z++;
		$changes .= $ch;
	}

	$profile_link = '[url=' . z_root() . '/profile/' . $channel['channel_address'] . ']' . t('public profile') . '[/url]';

	if ($t == 1 && strlen($value)) {
		// if it's a url, the HTML quotes will mess it up, so link it and don't try and zidify it because we don't know what it points to.
		$value = preg_replace_callback("/([^='" . '"' . "]|^|#\^)(https?:\/\/[a-zA-Z0-9\pL:\/\-?&;.=@_~#%\$!+,]+)/ismu", 'red_zrl_callback', $value);
		// take out the bookmark indicator
		if (str_starts_with($value, '#^')) {
			$value = str_replace('#^', '', $value);
		}

		if ($photo) {
			$value = "\n\n" . '[zmg=' . z_root() . '/photo/' . $value . '-' . $photo_size . ']' . $ch . ' ' . $channel['xchan_name'] . '[/zmg]';
		}

		$message = sprintf(t('%1$s\'s %2$s has been updated to %3$s'), $channel_link, $changes, $value);

		if (!$photo) {
			$message .= "\n\n" . sprintf(t('Visit %1$s\'s %2$s'), $channel_link, $profile_link);
		}

	} else {
		$message = sprintf(t('%1$s has an updated %2$s, changing %3$s.'), $channel_link, $profile_link, $changes);
	}

	$arr['body'] = $message;

	$arr['obj'] = [
		'type' => 'Profile',
		'content' => bbcode($message),
		'source' => [
			'content'   => $message,
			'mediaType' => 'text/bbcode'
		],
		'describes' => Activity::encode_person($channel),
		'url' => z_root() . '/profile/' . $channel['channel_address']
	];

	post_activity_item($arr);

}
