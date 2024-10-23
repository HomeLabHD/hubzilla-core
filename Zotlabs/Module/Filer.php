<?php
namespace Zotlabs\Module;

use App;

class Filer extends \Zotlabs\Web\Controller {

	function get() {

		if(!local_channel()) {
			killme();
		}

		$term = unxmlify(trim($_GET['term'] ?? ''));
		$item_id = ((App::$argc > 1) ? intval(App::$argv[1]) : 0);

		logger('filer: tag ' . $term . ' item ' . $item_id);

		if($item_id && strlen($term)){

			$sys = get_sys_channel();

			$r = q("SELECT * FROM item WHERE (uid = %d OR uid = %d) AND id = %d
				and item_type in (0,6,7) and item_deleted = 0 and item_unpublished = 0
				and item_delayed = 0 and item_pending_remove = 0 and item_blocked = 0 LIMIT 1",
				intval(local_channel()),
				intval($sys['channel_id']),
				intval($item_id)
			);

			if ($r && $r[0]['uid'] === $sys['channel_id']) {
				$r = [copy_of_pubitem(App::get_channel(), $r[0]['mid'])];
			}

			if(!$r) {
				killme();
			}

			$item_id = $r[0]['id'];

			// file item
			store_item_tag(local_channel(),$item_id,TERM_OBJ_POST,TERM_FILE,$term,'');

			// protect the entire conversation from periodic expiration

			q("update item set item_retained = 1, changed = '%s' where id = %d and uid = %d",
				dbesc(datetime_convert()),
				intval($r[0]['parent']),
				intval(local_channel())
			);
		}
		else {
			$filetags = array();
			$r = q("select distinct(term) from term where uid = %d and ttype = %d order by term asc",
				intval(local_channel()),
				intval(TERM_FILE)
			);
			if(count($r)) {
				foreach($r as $rr)
					$filetags[] = $rr['term'];
			}
			$tpl = get_markup_template("filer_dialog.tpl");
			$o = replace_macros($tpl, array(
				'$field' => array('term', t('Enter a folder name'), '', '', $filetags, 'placeholder="' . t('or select an existing folder (doubleclick)') . '"'),
				'$submit' => t('Save'),
				'$title' => t('Save to Folder'),
				'$cancel' => t('Cancel')
			));

			echo $o;
		}
		killme();
	}

}
