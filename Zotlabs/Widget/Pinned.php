<?php
namespace Zotlabs\Widget;

use Zotlabs\Lib\Config;

/**
 *   * Name: Pinned items
 *   * Description: Display pinned items
 *   * Author: Max Kostikov
 *   * Requires: disabled_for_pdledit_gui
 */


class Pinned {

	private $allowed_types = 0;
	private $uid = 0;


	/*
	 * @brief Displays pinned items
	 *
	 * @param $uid
	 * @param $types
	 * @return array of results: 'html' string, 'ids' array
	 *
	 */
	function widget($uid, $types) {

		$ret = [ 'html' => EMPTY_STR, 'ids' => [] ];

		$this->uid = intval($uid);
		if(! $this->uid)
			return $ret;

		$this->allowed_types = Config::Get('system', 'pin_types', [ ITEM_TYPE_POST ]);

		$items = $this->list($types);

		if(empty($items))
			return $ret;

		$ret['ids'] = array_column($items, 'id');

		$observer = \App::get_observer();

		xchan_query($items);

		foreach($items as $item) {

			$midb64 = $item['uuid'];

			if(isset($observer['xchan_hash']) && in_array($observer['xchan_hash'], get_pconfig($item['uid'], 'pinned_hide', $midb64, [])))
				continue;

			$author = $item['author'];
			$owner = $item['owner'];

			$profile_avatar = $author['xchan_photo_m'];
			$profile_link = chanlink_hash($item['author_xchan']);
			$profile_name = $author['xchan_name'];

			$commentable = ($item['item_nocomment'] == 0 && $item['comments_closed'] == NULL_DATE ? true : false);

			$location = format_location($item);
			$isevent = false;
			$attend = null;
			$canvote = false;

			$response_verbs[] = 'like';

			if(feature_enabled(\App::$profile['profile_uid'],'dislike')) {
				$response_verbs[] = 'dislike';
			}

			$response_verbs[] = 'announce';

			if ($item['obj_type'] === 'Question') {
				$response_verbs[] = 'answer';
			}

			$responses = get_responses($response_verbs, $item);

			$verified = (intval($item['item_verified']) ? t('Message signature validated') : '');
			$forged = ((! intval($item['item_verified']) && $item['sig']) ? t('Message signature incorrect') : '');

			$shareable = ((local_channel() && \App::$profile_uid == local_channel() && $item['item_private'] != 1) ? true : false);
			if ($shareable) {
				// This actually turns out not to be possible in some protocol stacks without opening up hundreds of new issues.
				// Will allow it only for uri resolvable sources.
				if(strpos($item['mid'],'http') === 0) {
					$share = []; // Isn't yet ready for primetime
					//$share = [ t('Repeat This'), t('repeat') ];
				}
				$embed = [ t('Share This'), t('share') ];
			}

			$is_new = boolval(strcmp(datetime_convert('UTC','UTC',$item['created']),datetime_convert('UTC','UTC','now - 12 hours')) > 0);

			$body = prepare_body($item,true);

			$str = [
				'item_type'	 => intval($item['item_type']),
				'body'		 => $body['html'],
				'tags'		 => $body['tags'],
				'categories'	 => $body['categories'],
				'mentions'	 => $body['mentions'],
				'attachments'	 => $body['attachments'],
				'folders'	 => $body['folders'],
				'text'		 => strip_tags($body['html']),
				'id'		 => $item['id'],
				'mids'		 => json_encode([ $midb64 ]),
				'mid'		 => $item['uuid'],
				'rawmid'	 => $item['mid'],
				'parent'	 => $item['parent'],
				'isevent'	 => $isevent,
				'attend'	 => $attend,
				'conlabels'	 => [],
				'canvote'	 => $canvote,
				'linktitle' 	 => sprintf( t('View %s\'s profile - %s'), $profile_name, ($author['xchan_addr'] ? $author['xchan_addr'] : $author['xchan_url']) ),
				'olinktitle' 	 => sprintf( t('View %s\'s profile - %s'), $owner['xchan_name'], ($owner['xchan_addr'] ? $owner['xchan_addr'] : $owner['xchan_url']) ),
				'profile_url' 	 => $profile_link,
				'name'		 => $profile_name,
				'thumb'		 => $profile_avatar,
				'via'		 => t('via'),
				'title'		 => $item['title'],
				'title_tosource' => get_pconfig($item['uid'],'system','title_tosource'),
				'ago'		 => relative_date($item['created']),
				'app'		 => $item['app'],
				'str_app'	 => sprintf( t('from %s'), $item['app'] ),
				'isotime'	 => datetime_convert('UTC', date_default_timezone_get(), $item['created'], 'c'),
				'localtime'	 => datetime_convert('UTC', date_default_timezone_get(), $item['created'], 'r'),
				'editedtime'	 => (($item['edited'] != $item['created']) ? sprintf( t('last edited: %s'), datetime_convert('UTC', date_default_timezone_get(), $item['edited'], 'r') ) : ''),
				'expiretime'	 => ($item['expires'] > NULL_DATE ? sprintf( t('Expires: %s'), datetime_convert('UTC', date_default_timezone_get(), $item['expires'], 'r') ) : ''),
				'verified'	 => $verified,
				'forged'	 => $forged,
				'location'	 => $location,
				'divider'	 => get_pconfig($item['uid'],'system','item_divider'),
				'attend_title' => t('Attendance Options'),
				'vote_title' => t('Voting Options'),
				'is_new'	 => $is_new,
				'owner_url'	 => ($owner['xchan_addr'] != $author['xchan_addr'] ? chanlink_hash($owner['xchan_hash']) : ''),
				'owner_photo'=> $owner['xchan_photo_m'],
				'owner_name' => $owner['xchan_name'],
				'photo'		 => $body['photo'],
				'event'		 => $body['event'],
				'has_tags'	 => (($body['tags'] || $body['categories'] || $body['mentions'] || $body['attachments'] || $body['folders']) ? true : false),
				// Item toolbar buttons
				'share'		 => (isset($share) && count($share) ? $share : false),
				'embed'		 => (isset($embed) && count($embed) ? $embed : false),
				'plink'		 => get_plink($item),
				'pinned'	 => t('Pinned post'),
				'pinme'		 => (isset($observer['xchan_hash']) && $observer['xchan_hash'] == $owner['xchan_hash'] ? t('Unpin from the top') : ''),
				'hide'		 => (! $is_new && isset($observer['xchan_hash']) && $observer['xchan_hash'] != $owner['xchan_hash'] ? t("Don't show") : ''),
				// end toolbar buttons
				'modal_dismiss' => t('Close'),
				'responses'	 => $responses,
				'author_id' => (($author['xchan_addr']) ? $author['xchan_addr'] : $author['xchan_url'])

			];

			$tpl = get_markup_template('pinned_item.tpl');
			$ret['html'] .= replace_macros($tpl, $str);
		}

		return $ret;
	}


	/*
	 * @brief List pinned items depend on type
	 *
	 * @param $types
	 * @return array of pinned items
	 *
	 */
	private function list($types) {

		if(empty($types) || (! is_array($types)))
			return [];

		$item_types = array_intersect($this->allowed_types, $types);
		if(empty($item_types))
			return [];

		$mids_list = [];

		foreach($item_types as $type) {

			$mids = get_pconfig($this->uid, 'pinned', $type, []);
			foreach($mids as $mid) {
				if(!empty($mid))
					$mids_list[] = unpack_link_id($mid);
			}
		}
		if(empty($mids_list))
			return [];


		$r = q("SELECT parent AS item_id FROM item WHERE uuid IN ( '%s' ) AND uid = %d AND id = parent AND item_private = 0",
			dbesc(implode(",", $mids_list)),
			intval($this->uid)
		);

		return items_by_parent_ids($r, blog_mode: true);

	}

}
