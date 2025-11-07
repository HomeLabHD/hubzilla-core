<?php
namespace Zotlabs\Module;

use Zotlabs\Lib\Config;

require_once('include/photos.php');


class Profperm extends \Zotlabs\Web\Controller {

	function init() {

		if(! local_channel())
			return;

		$channel = \App::get_channel();
		$which = $channel['channel_address'];

		$profile = \App::$argv[1];

		profile_load($which,$profile);

	}


	function get() {

		if(! local_channel()) {
			notice( t('Permission denied') . EOL);
			return;
		}


		if(argc() < 2) {
			notice( t('Invalid profile identifier.') . EOL );
			return;
		}

		// Switch to text mod interface if we have more than 'n' contacts or group members

		$switchtotext = get_pconfig(local_channel(),'system','groupedit_image_limit');
		if($switchtotext === false)
			$switchtotext = Config::Get('system','groupedit_image_limit');
		if($switchtotext === false)
			$switchtotext = 400;

		$change = 0;

		if((argc() > 2) && intval(argv(1)) && intval(argv(2))) {
			$r = q("SELECT abook_id FROM abook WHERE abook_id = %d and abook_channel = %d limit 1",
				intval(argv(2)),
				intval(local_channel())
			);
			if($r)
				$change = intval(argv(2));
		}


		$o = '';

		if((argc() > 1) && (intval(argv(1)))) {
			$r = q("SELECT * FROM profile WHERE id = %d AND uid = %d AND is_default = 0 LIMIT 1",
				intval(argv(1)),
				intval(local_channel())
			);
			if(! $r) {
				notice( t('Invalid profile identifier.') . EOL );
				return;
			}

			$profile = $r[0];

			$r = q("SELECT * FROM abook left join xchan on abook_xchan = xchan_hash WHERE abook_channel = %d AND abook_profile = '%s'",
				intval(local_channel()),
				dbesc($profile['profile_guid'])
			);

			$ingroup = [];
			if($r) {
				foreach($r as $member)
					$ingroup[] = $member['abook_id'];
			}

			$members = $r;

			if($change) {
				if(in_array($change,$ingroup)) {
					q("UPDATE abook SET abook_profile = '' WHERE abook_id = %d AND abook_channel = %d",
						intval($change),
						intval(local_channel())
					);
				}
				else {
					q("UPDATE abook SET abook_profile = '%s' WHERE abook_id = %d AND abook_channel = %d",
						dbesc($profile['profile_guid']),
						intval($change),
						intval(local_channel())
					);

				}

				$r = q("SELECT * FROM abook left join xchan on abook_xchan = xchan_hash
					WHERE abook_channel = %d AND abook_profile = '%s'",
					intval(local_channel()),
					dbesc($profile['profile_guid'])
				);

				$members = $r;
				$ingroup = [];
				if($r) {
					foreach($r as $member)
						$ingroup[] = $member['abook_id'];
				}
			}

			$o .= '<h2>' . t('Profile Visibility Editor') . '</h2>';
			$o .= '<h3>' . t('Profile') . ' \'' . $profile['profile_name'] . '\'</h3>';
			$o .= '<div id="prof-edit-desc">' . t('Click on a contact to add or remove.') . '</div>';
		}

		// Build template data
		$members_tpl = [];
		$textmode = (($switchtotext && (count($members) > $switchtotext)) ? true : false);
		if($members) {
			foreach($members as $member) {
				if($member['xchan_url']) {
					$member['click'] = 'profChangeMember(' . $profile['id'] . ',' . $member['abook_id'] . '); return false;';
					$members_tpl[] = [ 'micro' => micropro($member, true, 'mpprof', $textmode) ];
				}
			}
		}

		$all_members_tpl = [];
		$r = abook_connections(local_channel());
		if($r) {
			$textmode = (($switchtotext && (count($r) > $switchtotext)) ? true : false);
			foreach($r as $member) {
				if(! in_array($member['abook_id'], $ingroup)) {
					$member['click'] = 'profChangeMember(' . $profile['id'] . ',' . $member['abook_id'] . '); return false;';
					$all_members_tpl[] = [ 'micro' => micropro($member, true, 'mpprof', $textmode) ];
				}
			}
		}

		// Use tpl for the inner part
		$inner_html = replace_macros(get_markup_template('profile_members.tpl'), [
			'$visible_to'      => t('Visible To'),
			'$all_connections' => t('All Connections'),
			'$members'         => $members_tpl,
			'$all_members'     => $all_members_tpl,
		]);

		if($change) {
			echo $inner_html;
			killme();
		}

		$o .= $inner_html;
		return $o;
	}
}

