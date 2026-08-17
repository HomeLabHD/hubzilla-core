<?php
namespace Zotlabs\Module;

use Zotlabs\Lib\ASObjectStorage;
use Zotlabs\Web\Controller;

require_once('include/conversation.php');
require_once('include/text.php');


/**
 * @file Zotlabs/Module/Sharedwithme.php
 *
 */

class Sharedwithme extends Controller {

	function get() {
		if(! local_channel()) {
			notice( t('Permission denied.') . EOL);
			return;
		}

		$channel = \App::get_channel();

		$is_owner = (local_channel() && (local_channel() == $channel['channel_id']));

		$item_normal = item_normal();

		//drop single file - localuser
		if((argc() > 2) && (argv(2) === 'drop')) {

			$id = intval(argv(1));

			drop_item($id);

			goaway(z_root() . '/sharedwithme');

		}

		//drop all files - localuser
		if((argc() > 1) && (argv(1) === 'dropall')) {

			$r = q("SELECT id FROM item WHERE verb IN ('Create', 'Update') AND obj_type IN ('Document', 'Video', 'Audio', 'Image') AND uid = %d AND owner_xchan != '%s' $item_normal",
				intval(local_channel()),
				dbesc($channel['channel_hash'])
			);

			$ids = ids_to_array($r);

			if($ids)
				drop_items($ids);

			goaway(z_root() . '/sharedwithme');

		}

		//list files
		$r = q("SELECT id, uid, obj, item_unseen FROM item WHERE verb IN ('Create', 'Update') AND obj_type IN ('Document', 'Video', 'Audio', 'Image') AND uid = %d AND owner_xchan != '%s' $item_normal",
			intval(local_channel()),
			dbesc($channel['channel_hash'])
		);

		$r = fetch_post_tags($r, true);

		$items = [];
		$ids = [];


		if($r) {

			foreach($r as $rr) {
				$obj = (new ASObjectStorage($rr['obj']))->decode();
				$item = [];

				$item['id'] = $rr['id'];
				$item['unseen'] = $rr['item_unseen'];

				if($item['unseen']) {
					$ids[] = $rr['id'];
				}
				if (is_array($obj) && isset($obj['url']) && is_array($obj['url'])) {
					foreach($obj['url'] as $u) {
						$item['objfiletype'] = $u['mediaType'] ?? '';
						$item['objfiletypeclass'] = getIconFromType($u['mediaType'] ?? 'octet/stream');
						$item['objurl'] = $u['href'] . '?f=&zid=' . $channel['xchan_addr'];
						$item['objfilename'] = $u['name'] ?? t('unknown');

						$items[] = $item;
					}
				}
			}
		}

		$ids = implode(',', $ids);

		if($ids) {
			q("UPDATE item SET item_unseen = 0 WHERE id IN ( $ids ) AND uid = %d",
				intval(local_channel())
			);
		}

		$o = '';

		$o .= replace_macros(get_markup_template('sharedwithme.tpl'), array(
			'$header' => t('Files: shared with me'),
			'$name' => t('Name'),
			'$label_new' => t('NEW'),
			'$size' => t('Size'),
			'$lastmod' => t('Last Modified'),
			'$dropall' => t('Remove all files'),
			'$drop' => t('Remove this file'),
			'$items' => $items
		));

		return $o;

	}

}
