<?php /** @file */

use Zotlabs\Lib\Activity;
use Zotlabs\Lib\Apps;
use Zotlabs\Lib\ASObjectStorage;
use Zotlabs\Lib\Config;
use Zotlabs\Lib\PConfig;

require_once('include/items.php');


function item_extract_images($body) {

	$saved_image = array();
	$orig_body = $body;
	$new_body = '';

	$cnt = 0;
	$img_start = strpos($orig_body, '[img');
	$img_st_close = ($img_start !== false ? strpos(substr($orig_body, $img_start), ']') : false);
	$img_end = ($img_start !== false ? strpos(substr($orig_body, $img_start), '[/img]') : false);
	while(($img_st_close !== false) && ($img_end !== false)) {

		$img_st_close++; // make it point to AFTER the closing bracket
		$img_end += $img_start;

		if(! strcmp(substr($orig_body, $img_start + $img_st_close, 5), 'data:')) {
			// This is an embedded image

			$saved_image[$cnt] = substr($orig_body, $img_start + $img_st_close, $img_end - ($img_start + $img_st_close));
			$new_body = $new_body . substr($orig_body, 0, $img_start) . '[!#saved_image' . $cnt . '#!]';

			$cnt++;
		}
		else
			$new_body = $new_body . substr($orig_body, 0, $img_end + strlen('[/img]'));

		$orig_body = substr($orig_body, $img_end + strlen('[/img]'));

		if($orig_body === false) // in case the body ends on a closing image tag
			$orig_body = '';

		$img_start = strpos($orig_body, '[img');
		$img_st_close = ($img_start !== false ? strpos(substr($orig_body, $img_start), ']') : false);
		$img_end = ($img_start !== false ? strpos(substr($orig_body, $img_start), '[/img]') : false);
	}

	$new_body = $new_body . $orig_body;

	return array('body' => $new_body, 'images' => $saved_image);
}


function item_redir_and_replace_images($body, $images, $cid) {

	$origbody = $body;
	$newbody = '';

	$observer = App::get_observer();
	$obhash = (($observer) ? $observer['xchan_hash'] : '');
	$obaddr = (($observer) ? $observer['xchan_addr'] : '');

	for($i = 0; $i < count($images); $i++) {
		$search = '/\[url\=(.*?)\]\[!#saved_image' . $i . '#!\]\[\/url\]' . '/is';
		$replace = '[url=' . magiclink_url($obhash,$obaddr,'$1') . '][!#saved_image' . $i . '#!][/url]' ;

		$img_end = strpos($origbody, '[!#saved_image' . $i . '#!][/url]') + strlen('[!#saved_image' . $i . '#!][/url]');
		$process_part = substr($origbody, 0, $img_end);
		$origbody = substr($origbody, $img_end);

		$process_part = preg_replace($search, $replace, $process_part);
		$newbody = $newbody . $process_part;
	}
	$newbody = $newbody . $origbody;

	$cnt = 0;
	foreach($images as $image) {
		// We're depending on the property of 'foreach' (specified on the PHP website) that
		// it loops over the array starting from the first element and going sequentially
		// to the last element
		$newbody = str_replace('[!#saved_image' . $cnt . '#!]', '[img]' . $image . '[/img]', $newbody);
		$cnt++;
	}

	return $newbody;
}



/**
 * Render actions localized
 */

function localize_item(&$item){

	if (activity_match($item['verb'], ['Like', 'Dislike', ACTIVITY_LIKE, ACTIVITY_DISLIKE, ACTIVITY_SHARE])){
		if(intval($item['item_thread_top']))
			return;

		$author_link = $item['thr_parent_author']['xchan_url'];
		$author_name = $item['thr_parent_author']['xchan_name'];
		$item_url = $item['thr_parent'];

		$Bphoto = '';

		switch($obj['obj_type']) {
			case ACTIVITY_OBJ_PHOTO:
			case 'Image':
				$post_type = t('photo');
				break;
			case ACTIVITY_OBJ_EVENT:
			case 'Event':
				$post_type = t('event');
				break;
			case ACTIVITY_OBJ_PERSON:
			case 'Person':
				$post_type = t('channel');
				$author_name = $obj['title'];
				$obj = (new ASObjectStorage($item['obj']))->decode();
				if($obj['link']) {
					$author_link  = get_rel_link($obj['link'],'alternate');
					$Bphoto = get_rel_link($obj['link'],'photo');
				}
				break;
			case ACTIVITY_OBJ_THING:
				$obj = (new ASObjectStorage($item['obj']))->decode();
				$post_type = $obj['title'];
				if($obj['owner']) {
					if(array_key_exists('name',$obj['owner']))
						$obj['owner']['name'];
					if(array_key_exists('link',$obj['owner']))
						$author_link = get_rel_link($obj['owner']['link'],'alternate');
				}
				if($obj['link']) {
					$Bphoto = get_rel_link($obj['link'],'photo');
				}
				break;

			case ACTIVITY_OBJ_NOTE:
			case 'Note':
			default:
				if ($item['thr_parent'] === $item['parent_mid']) {
					$post_type = t('conversation');
				}

				elseif ($item['thr_parent_thr_parent'] === $item['parent_mid']) {
					$post_type = t('comment');
				}

				else {
					$post_type = t('reply');
				}

				break;
		}

		// If we couldn't parse something useful, don't bother translating.
		// We need something better than zid here, probably magic_link(), but it needs writing

		if($author_link && $author_name && $item_url) {
			$author	 = '[zrl=' . chanlink_url($item['author']['xchan_url']) . ']' . $item['author']['xchan_name'] . '[/zrl]';
			$objauthor =  '[zrl=' . chanlink_url($author_link) . ']' . $author_name . '[/zrl]';

			$plink = '[zrl=' . zid($item_url) . ']' . $post_type . '[/zrl]';

			if(activity_match($item['verb'], ['Like', ACTIVITY_LIKE])) {
				$bodyverb = t('%1$s likes %2$s\'s %3$s');
				// short version, in notification strings the author will be displayed separately
				$shortbodyverb = t('likes %1$s\'s %2$s');
			}
			elseif(activity_match($item['verb'], ['Dislike', ACTIVITY_DISLIKE])) {
				$bodyverb = t('%1$s doesn\'t like %2$s\'s %3$s');
				$shortbodyverb = t('doesn\'t like %1$s\'s %2$s');
			}
			elseif(activity_match($item['verb'], ACTIVITY_SHARE)) {
				$bodyverb = t('%1$s repeated %2$s\'s %3$s');
				$shortbodyverb = t('repeated %1$s\'s %2$s');
			}

			$item['shortlocalize'] = sprintf($shortbodyverb, '[bdi]' . $author_name . '[/bdi]', $post_type);

			$item['body'] = $item['localize'] = sprintf($bodyverb, '[bdi]' . $author . '[/bdi]', '[bdi]' . $objauthor . '[/bdi]', $plink);
			if($Bphoto != "")
				$item['body'] .= "\n\n\n" . '[zrl=' . chanlink_url($author_link) . '][zmg=80x80]' . $Bphoto . '[/zmg][/zrl]';

		}
		else {
			logger('localize_item like failed: link ' . $author_link . ' name ' . $author_name . ' url ' . $item_url);
		}

	}
}

/**
 * @brief Count the total of comments on this item and its desendants.
 *
 * @param array $item an assoziative item-array which provides:
 *  * \e array \b children
 * @return number
 */

function count_descendants($item) {

	$total = count($item['children']);

	if($total > 0) {
		foreach($item['children'] as $child) {
			if(! visible_activity($child))
				$total --;

			$total += count_descendants($child);
		}
	}

	return $total;
}

/**
 * @brief Check if the activity of the item is visible.
 *
 * likes (etc.) can apply to other things besides posts. Check if they are post
 * children, in which case we handle them specially. Activities which are unrecognised
 * as having special meaning and hidden will be treated as posts or comments and visible
 * in the stream.
 *
 * @param array $item
 * @return boolean
 */
function visible_activity($item) {
	$hidden_activities = ['Like', 'Dislike', 'Accept', 'Reject', 'TentativeAccept', ACTIVITY_LIKE, ACTIVITY_DISLIKE, ACTIVITY_SHARE, ACTIVITY_ATTEND, ACTIVITY_ATTENDNO, ACTIVITY_ATTENDMAYBE];

	if(intval($item['item_notshown']))
		return false;

	if ($item['obj_type'] === 'Answer') {
		return false;
	}

	if (in_array($item['verb'], ['Add', 'Remove'])) {
		return false;
	}

	foreach($hidden_activities as $act) {
		if((activity_match($item['verb'], $act)) && ($item['mid'] != $item['parent_mid'])) {
			return false;
		}
	}

	// We only need edit activities for other federated protocols
	// which do not support edits natively. While this does federate
	// edits, it presents a number of issues locally - such as #757 and #758.
	// The SQL check for an edit activity would not perform that well so to fix these issues
	// requires an additional item flag (perhaps 'item_edit_activity') that we can add to the
	// query for searches and notifications.

	// For now we'll just forget about trying to make edits work on network protocols that
	// don't support them.

	// if(is_edit_activity($item))
	//	return false;

	return true;
}


/**
 * @brief "Render" a conversation or list of items for HTML display.
 *
 * There are two major forms of display:
 *  - Sequential or unthreaded ("New Item View" or search results)
 *  - conversation view
 *
 * The $mode parameter decides between the various renderings and also
 * figures out how to determine page owner and other contextual items
 * that are based on unique features of the calling module.
 *
 * @param array $items
 * @param string $mode
 * @param boolean $update
 * @param string $page_mode default traditional
 * @param string $prepared_item
 * @return string
 */
function conversation($items, $mode, $update, $page_mode = 'traditional', $prepared_item = '') {

	$content_html = '';
	$o = '';

	require_once('bbcode.php');

	$ssl_state = ((local_channel()) ? true : false);

	if (local_channel())
		load_pconfig(local_channel(),'');

	$profile_owner   = 0;
	$page_writeable  = false;
	$live_update_div = '';
	$jsreload        = '';

	$preview = (($page_mode === 'preview') ? true : false);
	$r_preview = (($page_mode === 'r_preview') ? true : false);
	$previewing = (($preview) ? ' preview ' : '');
	$preview_lbl = t('This is an unsaved preview');

	if (in_array($mode, [ 'network', 'pubstream'])) {

		$profile_owner = local_channel();
		$page_writeable = ((local_channel()) ? true : false);

		if (!$update) {
			// The special div is needed for liveUpdate to kick in for this page.
			// We only launch liveUpdate if you aren't filtering in some incompatible
			// way and also you aren't writing a comment (discovered in javascript).

			$live_update_div = '<div id="live-network"></div>' . "\r\n"
				. "<script> var profile_uid = " . $_SESSION['uid']
				. "; var netargs = '" . substr(App::$cmd,8)
				. '?f='
				. (!empty($_GET['cid'])    ? '&cid='    . $_GET['cid']    : '')
				. (!empty($_GET['search']) ? '&search=' . $_GET['search'] : '')
				. (!empty($_GET['star'])   ? '&star='   . $_GET['star']   : '')
				. (!empty($_GET['order'])  ? '&order='  . $_GET['order']  : '')
				. (!empty($_GET['bmark'])  ? '&bmark='  . $_GET['bmark']  : '')
				. (!empty($_GET['liked'])  ? '&liked='  . $_GET['liked']  : '')
				. (!empty($_GET['conv'])   ? '&conv='   . $_GET['conv']   : '')
				. (!empty($_GET['spam'])   ? '&spam='   . $_GET['spam']   : '')
				. (!empty($_GET['nets'])   ? '&nets='   . $_GET['nets']   : '')
				. (!empty($_GET['cmin'])   ? '&cmin='   . $_GET['cmin']   : '')
				. (!empty($_GET['cmax'])   ? '&cmax='   . $_GET['cmax']   : '')
				. (!empty($_GET['file'])   ? '&file='   . $_GET['file']   : '')
				. (!empty($_GET['uri'])    ? '&uri='    . $_GET['uri']   : '')
				. (!empty($_GET['pf'])     ? '&pf='     . $_GET['pf']     : '')
				. "'; var profile_page = " . App::$pager['page'] . "; </script>\r\n";
		}
	}

	elseif ($mode === 'hq') {
		$profile_owner = local_channel();
		$page_writeable = true;
		$live_update_div = '<div id="live-hq"></div>' . "\r\n";
	}

	elseif ($mode === 'channel') {
		$profile_owner = App::$profile['profile_uid'];
		$page_writeable = ($profile_owner == local_channel());

		if (!$update) {
			// This is ugly, but we can't pass the profile_uid through the session to the ajax updater,
			// because browser prefetching might change it on us. We have to deliver it with the page.

			$live_update_div = '<div id="live-channel"></div>' . "\r\n"
				. "<script> var profile_uid = " . App::$profile['profile_uid']
				. "; var netargs = '?f='; var profile_page = " . App::$pager['page'] . "; </script>\r\n";
		}
	}

	elseif ($mode === 'cards') {
		$profile_owner = App::$profile['profile_uid'];
		$page_writeable = ($profile_owner == local_channel());
		$live_update_div = '<div id="live-cards"></div>' . "\r\n"
			. "<script> var profile_uid = " . App::$profile['profile_uid']
			. "; var netargs = '?f='; var profile_page = " . App::$pager['page'] . "; </script>\r\n";
		$jsreload = $_SESSION['return_url'];
	}

	elseif ($mode === 'articles') {
		$profile_owner = App::$profile['profile_uid'];
		$page_writeable = ($profile_owner == local_channel());
		$live_update_div = '<div id="live-articles"></div>' . "\r\n"
			. "<script> var profile_uid = " . App::$profile['profile_uid']
			. "; var netargs = '?f='; var profile_page = " . App::$pager['page'] . "; </script>\r\n";
		$jsreload = $_SESSION['return_url'];
	}


	elseif ($mode === 'display') {
		$profile_owner = local_channel();
		$page_writeable = false;
		$live_update_div = '<div id="live-display"></div>' . "\r\n";
	}

	elseif ($mode === 'page') {
		$profile_owner = App::$profile['uid'];
		$page_writeable = ($profile_owner == local_channel());
		$live_update_div = '<div id="live-page"></div>' . "\r\n";
	}

	elseif ($mode === 'search') {
		$live_update_div = '<div id="live-search"></div>' . "\r\n";
	}

	elseif ($mode === 'moderate') {
		$profile_owner = local_channel();
	}

	elseif ($mode === 'photos') {
		$profile_owner = App::$profile['profile_uid'];
		$page_writeable = ($profile_owner == local_channel());
		$live_update_div = '<div id="live-photos"></div>' . "\r\n";
		// for photos we've already formatted the top-level item (the photo)
		$content_html = App::$data['photo_html'];
	}

	$page_dropping = ((local_channel() && local_channel() == $profile_owner) ? true : false);

	if (! feature_enabled($profile_owner,'multi_delete'))
		$page_dropping = false;

	$uploading = false;

	$channel = App::get_channel();
	$observer = App::get_observer();

	if (local_channel()) {
		// Allow uploading if there is no default privacy and the view_storage permission is set to PERMS_PUBLIC
		if ($channel['channel_allow_cid'] === '' &&  $channel['channel_allow_gid'] === ''
			&& $channel['channel_deny_cid'] === '' && $channel['channel_deny_gid'] === ''
			&& intval(\Zotlabs\Access\PermissionLimits::Get(local_channel(),'view_storage')) === PERMS_PUBLIC) {
			$uploading = true;
		}

		// Allow uploading if OCAP tokens are enabled
		if (PConfig::Get(local_channel(), 'system', 'ocap_enabled')) {
			$uploading = true;
		}
	}

	if (!$update) {
		$_SESSION['return_url'] = App::$query_string;
	}

	load_contact_links(local_channel());

	$cb = array('items' => $items, 'mode' => $mode, 'update' => $update, 'preview' => $preview);
	call_hooks('conversation_start',$cb);

	$items = $cb['items'];

	// array with html for each thread (parent+comments)
	$threads = array();
	$threadsid = -1;

	$page_template = get_markup_template("conversation.tpl");

	if($items) {

		if(is_unthreaded($mode)) {

			// "New Item View" on network page or search page results
			// - just loop through the items and format them minimally for display

			$tpl = 'search_item.tpl';

			foreach($items as $item) {

				$x = [
					'mode' => $mode,
					'item' => $item
				];
				call_hooks('stream_item',$x);

				if(isset($x['item']['blocked']) && $x['item']['blocked'])
					continue;

				$item = $x['item'];

				$threadsid++;

				$comment     = '';
				$owner_url   = '';
				$owner_photo = '';
				$owner_name  = '';
				$sparkle     = '';
				$is_new      = false;

				if($mode === 'search' || $mode === 'community') {
					if(activity_match($item['verb'], ['Like', 'Dislike', ACTIVITY_LIKE, ACTIVITY_DISLIKE]) && $item['id'] != $item['parent']) {
						continue;
					}
				}

				$sp = false;
				$profile_link = best_link_url($item,$sp);
				if($sp)
					$sparkle = ' sparkle';
				else
					$profile_link = zid($profile_link);

				$profile_name = $item['author']['xchan_name'];
				$profile_link = $item['author']['xchan_url'];
				$profile_avatar = $item['author']['xchan_photo_m'];

				$location = format_location($item);

				localize_item($item);
				if($mode === 'network-new')
					$dropping = true;
				else
					$dropping = false;

				$drop = array(
					'pagedropping' => $page_dropping,
					'dropping' => $dropping,
					'select' => t('Select'),
					'delete' => t('Delete'),
				);

				$star = [];
				if ((local_channel() && local_channel() === intval($item['uid'])) && intval($item['item_thread_top']) && feature_enabled(local_channel(), 'star_posts')) {
					$star = [
						'toggle' => t("Toggle Star Status"),
						'isstarred' => ((intval($item['item_starred'])) ? true : false),
					];
				}

				$lock = (($item['item_private'] || strlen($item['allow_cid']) || strlen($item['allow_gid']) || strlen($item['deny_cid']) || strlen($item['deny_gid']))
					? t('Private Message')
					: false
				);
				$locktype = $item['item_private'];


				$likebuttons = false;
				$shareable = false;

				$verified = (intval($item['item_verified']) ? t('Message signature validated') : '');
				$forged = ((!empty($item['sig']) && !intval($item['item_verified'])) ? t('Message signature incorrect') : '');

				$unverified = '';

//				$tags=array();
//				$terms = get_terms_oftype($item['term'],array(TERM_HASHTAG,TERM_MENTION,TERM_UNKNOWN,TERM_COMMUNITYTAG));
//				if(count($terms))
//					foreach($terms as $tag)
//						$tags[] = format_term_for_display($tag);

				$body = prepare_body($item,true);

				$has_tags = (($body['tags'] || $body['categories'] || $body['mentions'] || $body['attachments'] || $body['folders']) ? true : false);

				if(strcmp(datetime_convert('UTC','UTC',$item['created']),datetime_convert('UTC','UTC','now - 12 hours')) > 0)
					$is_new = true;

				$conv_link = z_root() . '/display/' . $item['uuid'];

				if(local_channel()) {
					$conv_link = z_root() . '/hq/' . $item['uuid'];
				}

				if ($mode === 'pubstream-new') {
					$conv_link = z_root() . '/pubstream?mid=' . $item['uuid'];
				}

				$contact = [];

				if(App::$contacts && array_key_exists($item['author_xchan'],App::$contacts)) {
					$contact = App::$contacts[$item['author_xchan']];
				}

				$tmp_item = array(
					'template' => $tpl,
					'toplevel' => 'toplevel_item',
					'item_type' => intval($item['item_type']),
					'mode' => $mode,
					'approve' => t('Approve'),
					'delete' => t('Delete'),
					'preview_lbl' => $preview_lbl,
					'id' => (($preview) ? 'P0' : $item['item_id']),
					'mid' => $item['uuid'],
					'mids' => json_encode([$item['uuid']]),
					'linktitle' => sprintf( t('View %s\'s profile @ %s'), $profile_name, $profile_link),
					'author_id' => (($item['author']['xchan_addr']) ? $item['author']['xchan_addr'] : $item['author']['xchan_url']),
					'profile_url' => $profile_link,
					'thread_action_menu' => thread_action_menu($item,$mode),
					'thread_author_menu' => thread_author_menu($item,$mode),
					'name' => $profile_name,
					'sparkle' => $sparkle,
					'lock' => $lock,
					'locktype' => $locktype,
					'thumb' => $profile_avatar,
					'title' => $item['title'],
					'body' => $body['html'],
					'event' => $body['event'],
					'photo' => $body['photo'],
					'tags' => $body['tags'],
					'categories' => $body['categories'],
					'mentions' => $body['mentions'],
					'attachments' => $body['attachments'],
					'folders' => $body['folders'],
					'verified' => $verified,
					'unverified' => $unverified,
					'forged' => $forged,
					'txt_cats' => t('Categories:'),
					'txt_folders' => t('Filed under:'),
					'has_cats' => (($body['categories']) ? 'true' : ''),
					'has_folders' => (($body['folders']) ? 'true' : ''),
					'text' => strip_tags($body['html']),
					'ago' => relative_date($item['created']),
					'app' => $item['app'],
					'str_app' => sprintf( t('from %s'), $item['app']),
					'isotime' => datetime_convert('UTC', date_default_timezone_get(), $item['created'], 'c'),
					'localtime' => datetime_convert('UTC', date_default_timezone_get(), $item['created'], 'r'),
					'editedtime' => (($item['edited'] != $item['created']) ? sprintf( t('last edited: %s'), datetime_convert('UTC', date_default_timezone_get(), $item['edited'], 'r')) : ''),
					'expiretime' => (($item['expires'] > DBA::$dba->get_null_date()) ? sprintf( t('Expires: %s'), datetime_convert('UTC', date_default_timezone_get(), $item['expires'], 'r')):''),
					'location' => $location,
					'divider' => false,
					'indent' => '',
					'owner_name' => $owner_name,
					'owner_url' => $owner_url,
					'owner_photo' => $owner_photo,
					'plink' => get_plink($item,false),
					'edpost' => false,
					'star' => $star,
					'drop' => $drop,
					'vote' => $likebuttons,
					'like' => '',
					'dislike' => '',
					'comment' => '',
					'conv' => (($preview) ? '' : array('href'=> $conv_link, 'title'=> t('View in context'))),
					'previewing' => $previewing,
					'wait' => t('Please wait'),
					'thread_level' => 1,
					'has_tags' => $has_tags,
					'is_new' => $is_new,
					'contact_id' => (($contact) ? $contact['abook_id'] : '')
				);

				$arr = array('item' => $item, 'output' => $tmp_item);
				call_hooks('display_item', $arr);

//				$threads[$threadsid]['id'] = $item['item_id'];
				$threads[] = $arr['output'];
			}
		}
		else {

			// Normal View
//			logger('conv: items: ' . print_r($items,true));

			$conv = new Zotlabs\Lib\ThreadStream($mode, $preview, $uploading, $prepared_item);

			// In the display mode we don't have a profile owner.

			if($mode === 'display' && $items)
				$conv->set_profile_owner($items[0]['uid']);

			// get all the topmost parents
			// this shouldn't be needed, as we should have only them in our array
			// But for now, this array respects the old style, just in case

			$threads = array();
			foreach($items as $item) {

				// Check for any blocked authors


				$x = [ 'mode' => $mode, 'item' => $item ];
				call_hooks('stream_item',$x);

				if(isset($x['item']['blocked']))
					continue;

				$item = $x['item'];

				if (!visible_activity($item)) {
					continue;
				}

				$item['pagedrop'] = $page_dropping;

				if($item['id'] == $item['parent'] || $r_preview) {

					$item_object = new Zotlabs\Lib\ThreadItem($item);

					$conv->add_thread($item_object);
					if(($page_mode === 'list') || ($page_mode === 'pager_list')) {
						$item_object->set_display_mode('list');
					}
					if($mode === 'cards' || $mode === 'articles') {
						$item_object->set_reload($jsreload);
					}

				}
			}

			$threads = $conv->get_template_data();
			if(!$threads) {
				logger('[ERROR] conversation : Failed to get template data.', LOGGER_DEBUG);
				$threads = array();
			}
		}
	}

	if(in_array($page_mode, [ 'traditional', 'preview', 'pager_list'] )) {
		$page_template = get_markup_template("threaded_conversation.tpl");
	}
	elseif($update) {
		$page_template = get_markup_template("convobj.tpl");
	}
	else {
		$page_template = get_markup_template("conv_frame.tpl");
		$threads = null;
	}

//	if($page_mode === 'preview')
//		logger('preview: ' . print_r($threads,true));

//  Do not un-comment if smarty3 is in use
//	logger('page_template: ' . $page_template);

//	logger('nouveau: ' . print_r($threads,true));

	$o .= replace_macros($page_template, array(
		'$baseurl' => z_root(),
		'$photo_item' => $content_html,
		'$live_update' => $live_update_div,
		'$remove' => t('remove'),
		'$mode' => $mode,
		'$user' => App::$user,
		'$threads' => $threads,
		'$wait' => t('Loading...'),
		'$conversation_tools' => t('Conversation Features'),
		'$dropping' => ($page_dropping?t('Delete Selected Items'):False),
		'$preview' => $preview
	));

	return $o;
}


function best_link_url($item) {
	$best_url = $item['author-link'] ?? $item['url'] ?? '';
	$sparkle  = false;
	$clean_url = isset($item['author-link']) ? normalise_link($item['author-link']) : '';

	if($clean_url  && local_channel() && (local_channel() == $item['uid'])) {
		if(isset(App::$contacts) && !empty(App::$contacts[$clean_url])) {
			if(App::$contacts[$clean_url]['network'] === NETWORK_DFRN) {
				$best_url = z_root() . '/redir/' . App::$contacts[$clean_url]['id'];
				$sparkle = true;
			}
			else
				$best_url = App::$contacts[$clean_url]['url'];
		}
	}

	return $best_url;
}



function thread_action_menu($item,$mode = '') {

	$menu = [];

	if(local_channel() || (local_channel() && App::$module === 'pubstream')) {
		$menu[] = [
			'menu' => 'view_source',
			'title' => t('View Source'),
			'icon' => 'code',
			'action' => 'viewsrc(' . $item['id'] . '); return false;',
			'href' => '#'
		];

		if(!is_unthreaded($mode) && local_channel() == $item['uid']) {
			if($item['parent'] == $item['id'] && (get_observer_hash() != $item['author_xchan'])) {
				$menu[] = [
					'menu' => 'follow_thread',
					'title' => t('Follow Thread'),
					'icon' => 'plus',
					'action' => 'dosubthread(' . $item['id'] . '); return false;',
					'href' => '#'
				];
			}

			$menu[] = [
				'menu' => 'unfollow_thread',
				'title' => t('Unfollow Thread'),
				'icon' => 'dash',
				'action' => 'dounsubthread(' . $item['id'] . '); return false;',
				'href' => '#'
			];
		}

	}




	$args = [ 'item' => $item, 'mode' => $mode, 'menu' => $menu ];
	call_hooks('thread_action_menu', $args);

	return $args['menu'];

}

function author_is_pmable($xchan, $abook) {

	$x = [ 'xchan' => $xchan, 'abook' => $abook, 'result' => 'unset' ];
	call_hooks('author_is_pmable',$x);
	if($x['result'] !== 'unset')
		return $x['result'];

	return false;

}






function thread_author_menu($item, $mode = '') {

	$menu = [];
	$channel = [];
	$local_channel = local_channel();

	if($local_channel) {
		if(! count(App::$contacts))
			load_contact_links($local_channel);

		$channel = App::get_channel();
	}

	$profile_link = chanlink_hash($item['author_xchan']);
	$contact = false;

	$follow_url = '';

	if(isset($channel['channel_hash']) && $channel['channel_hash'] !== $item['author_xchan']) {
		if(App::$contacts && array_key_exists($item['author_xchan'],App::$contacts)) {
			$contact = App::$contacts[$item['author_xchan']];
		}
		else {
			$url = (($item['author']['xchan_addr']) ? $item['author']['xchan_addr'] : $item['author']['xchan_url']);
			if($local_channel && $url && (! in_array($item['author']['xchan_network'],[ 'rss', 'anon','unknown', 'zot', 'token']))) {
				$follow_url = z_root() . '/follow/?f=&url=' . urlencode($url) . '&interactive=0';
			}
		}
	}

	$contact_url = '';
	$posts_link = '';

	if($contact) {
		if (isset($contact['abook_self']) && !intval($contact['abook_self']))
			$contact_url = z_root() . '/connections#' . $contact['abook_id'];
		$posts_link = z_root() . '/network/?cid=' . $contact['abook_id'];
	}

	if($profile_link) {
		$menu[] = [
			'menu' => 'view_profile',
			'title' => t('View Profile'),
			'icon' => 'fw',
			'action' => '',
			'href' => $profile_link,
			'data' => '',
			'class' => ''
		];
	}

	if($posts_link) {
		$menu[] = [
			'menu' => 'view_posts',
			'title' => t('Recent Activity'),
			'icon' => 'fw',
			'action' => '',
			'href' => $posts_link,
			'data' => '',
			'class' => ''
		];
	}

	if($follow_url) {
		$menu[] = [
			'menu' => 'follow',
			'title' => t('Connect'),
			'icon' => 'fw',
			'action' => 'doFollowAuthor(\'' . $follow_url . '\'); return false;',
			'href' => '#',
			'data' => '',
			'class' => ''
		];
	}

	if($contact_url) {
		$menu[] = [
			'menu' => 'connedit',
			'title' => t('Edit Connection'),
			'icon' => 'fw',
			'action' => '',
			'href' => $contact_url,
			'data' => 'data-id="' . $contact['abook_id'] . '"',
			'class' => 'contact-edit'
		];
	}

	$args = [ 'item' => $item, 'mode' => $mode, 'menu' => $menu ];
	call_hooks('thread_author_menu', $args);

	return $args['menu'];

}


/**
 * @brief Format the like/dislike text for a profile item.
 *
 * @param int $cnt number of people who like/dislike the item
 * @param array $arr array of pre-linked names of likers/dislikers
 * @param string $type one of 'like, 'dislike'
 * @param int $id item id
 * @return string formatted text
 */
function format_like($cnt, $arr, $type, $id) {
	$o = '';
	if ($cnt == 1) {
		$o .= (($type === 'like') ? sprintf( t('%s likes this.'), $arr[0]) : sprintf( t('%s doesn\'t like this.'), $arr[0])) . EOL ;
	} else {
		$spanatts = 'class="fakelink" onclick="openClose(\'' . $type . 'list-' . $id . '\');"';
		$o .= (($type === 'like') ?
					sprintf( tt('<span  %1$s>%2$d people</span> like this.','<span  %1$s>%2$d people</span> like this.',$cnt), $spanatts, $cnt)
					 :
					sprintf( tt('<span  %1$s>%2$d people</span> don\'t like this.','<span  %1$s>%2$d people</span> don\'t like this.',$cnt), $spanatts, $cnt) );
		$o .= EOL;
		$total = count($arr);
		if($total >= MAX_LIKERS)
			$arr = array_slice($arr, 0, MAX_LIKERS - 1);
		if($total < MAX_LIKERS)
			$arr[count($arr)-1] = t('and') . ' ' . $arr[count($arr)-1];
		$str = implode(', ', $arr);
		if($total >= MAX_LIKERS)
			$str .= sprintf( tt(', and %d other people',', and %d other people',$total - MAX_LIKERS), $total - MAX_LIKERS );
		$str = (($type === 'like') ? sprintf( t('%s like this.'), $str) : sprintf( t('%s don\'t like this.'), $str));
		$o .= "\t" . '<div id="' . $type . 'list-' . $id . '" style="display: none;" >' . $str . '</div>';
	}

	return $o;
}


/**
 * Wrapper to allow addons to replace the status editor if desired.
 */
function status_editor($x, $popup = false, $module='') {
	$hook_info = ['editor_html' => '', 'x' => $x, 'popup' => $popup, 'module' => $module];
	call_hooks('status_editor',$hook_info);
	if ($hook_info['editor_html'] == '') {
		return hz_status_editor($x, $popup);
	} else {
		return $hook_info['editor_html'];
	}
}

/**
 * This is our general purpose content editor.
 * It was once nicknamed "jot" and you may see references to "jot" littered throughout the code.
 * They are referring to the content editor or components thereof.
 */

function hz_status_editor($x, $popup = false) {

	$c = channelx_by_n($x['profile_uid']);
	if($c && $c['channel_moved'])
		return;

	$webpage   = ((!empty($x['webpage'])) ? $x['webpage'] : '');
	$plaintext = true;

	$feature_nocomment = feature_enabled($x['profile_uid'], 'disable_comments');
	if(!empty($x['disable_comments']))
		$feature_nocomment = false;

	$feature_expire = ((feature_enabled($x['profile_uid'], 'content_expire') && (! $webpage)) ? true : false);
	if(!empty($x['hide_expire']))
		$feature_expire = false;

	$feature_future = ((feature_enabled($x['profile_uid'], 'delayed_posting') && (! $webpage)) ? true : false);
	if(!empty($x['hide_future']))
		$feature_future = false;

	$geotag = ((isset($x['allow_location']) && $x['allow_location']) ? replace_macros(get_markup_template('jot_geotag.tpl'), array()) : '');
	$setloc = t('Set your location');
	$clearloc = ((get_pconfig($x['profile_uid'], 'system', 'use_browser_location')) ? t('Clear browser location') : '');
	if(!empty($x['hide_location']))
		$geotag = $setloc = $clearloc = '';

	$mimetype = ((!empty($x['mimetype'])) ? $x['mimetype'] : 'text/bbcode');

	$mimeselect = ((!empty($x['mimeselect'])) ? $x['mimeselect'] : false);
	if($mimeselect)
		$mimeselect = mimetype_select($x['profile_uid'], $mimetype);
	else
		$mimeselect = '<input type="hidden" name="mimetype" value="' . $mimetype . '" />';

	$weblink = (($mimetype === 'text/bbcode') ? t('Insert web link') : false);
	if(!empty($x['hide_weblink']))
		$weblink = false;

	$embedPhotos = t('Embed (existing) photo from your photo albums');

	$writefiles = (($mimetype === 'text/bbcode') ? perm_is_allowed($x['profile_uid'], get_observer_hash(), 'write_storage') : false);
	if(!empty($x['hide_attach']))
		$writefiles = false;

	$layout = ((!empty($x['layout'])) ? $x['layout'] : '');

	$layoutselect = ((!empty($x['layoutselect'])) ? $x['layoutselect'] : false);
	if($layoutselect)
		$layoutselect = layout_select($x['profile_uid'], $layout);
	else
		$layoutselect = '<input type="hidden" name="layout_mid" value="' . $layout . '" />';

	if(array_key_exists('channel_select',$x) && $x['channel_select']) {
		require_once('include/channel.php');
		$id_select = identity_selector();
	}
	else
		$id_select = '';

	$reset = ((!empty($x['reset'])) ? $x['reset'] : '');

	$feature_auto_save_draft = ((feature_enabled($x['profile_uid'], 'auto_save_draft')) ? "true" : "false");

	$tpl = get_markup_template('jot-header.tpl');

	$tplmacros = [
		'$baseurl' => z_root(),
		'$editselect' => (($plaintext) ? 'none' : '/(profile-jot-text|prvmail-text)/'),
		'$pretext' => ((!empty($x['pretext'])) ? $x['pretext'] : ''),
		'$geotag' => $geotag,
		'$nickname' => $x['nickname'],
		'$linkurl' => t('Please enter a link URL:'),
		'$term' => t('Tag term:'),
		'$whereareu' => t('Where are you right now?'),
		'$editor_autocomplete'=> ((!empty($x['editor_autocomplete'])) ? $x['editor_autocomplete'] : ''),
		'$bbco_autocomplete'=> ((!empty($x['bbco_autocomplete'])) ? $x['bbco_autocomplete'] : ''),
		'$modalchooseimages' => t('Choose images to embed'),
		'$modalchoosealbum' => t('Choose an album'),
		'$modaldiffalbum' => t('Choose a different album...'),
		'$modalerrorlist' => t('Error getting album list'),
		'$modalerrorlink' => t('Error getting photo link'),
		'$modalerroralbum' => t('Error getting album'),
		'$nocomment_enabled' => t('Comments enabled'),
		'$nocomment_disabled' => t('Comments disabled'),
		'$confirmdelete' => t('Confirm delete'),
		'$auto_save_draft' => $feature_auto_save_draft,
		'$reset' => $reset,
		'$popup' => $popup
	];

	call_hooks('jot_header_tpl_filter',$tplmacros);

	if (isset(App::$page['htmlhead'])) {
		App::$page['htmlhead'] .= replace_macros($tpl, $tplmacros);
	}
	else {
		App::$page['htmlhead'] = replace_macros($tpl, $tplmacros);
	}

	$tpl = get_markup_template('jot.tpl');

	$preview = t('Preview');
	if(!empty($x['hide_preview']))
		$preview = '';

	$defexpire = ((($z = get_pconfig($x['profile_uid'], 'system', 'default_post_expire')) && (! $webpage)) ? $z : '');
	if($defexpire)
		$defexpire = datetime_convert('UTC',date_default_timezone_get(),$defexpire,'Y-m-d H:i');

	$defpublish = ((($z = get_pconfig($x['profile_uid'], 'system', 'default_post_publish')) && (! $webpage)) ? $z : '');
	if($defpublish)
		$defpublish = datetime_convert('UTC',date_default_timezone_get(),$defpublish,'Y-m-d H:i');

	$cipher = get_pconfig($x['profile_uid'], 'system', 'default_cipher');
	if(! $cipher)
		$cipher = 'AES-128-CCM';

	if(array_key_exists('catsenabled',$x))
		$catsenabled = $x['catsenabled'];
	else
		$catsenabled = ((feature_enabled($x['profile_uid'], 'categories') && (! $webpage)) ? 'categories' : '');

	// avoid illegal offset errors
	if(! array_key_exists('permissions',$x))
		$x['permissions'] = [ 'allow_cid' => '', 'allow_gid' => '', 'deny_cid' => '', 'deny_gid' => '' ];

	$jotplugins = '';
	call_hooks('jot_tool', $jotplugins);

	$jotnets = '';
	if(!empty($x['jotnets'])) {
		call_hooks('jot_networks', $jotnets);
	}

	$sharebutton = (!empty($x['button']) ? $x['button'] : t('Submit'));
	$placeholdtext = (!empty($x['content_label']) ? $x['content_label'] : t('Start a conversation'));

	$tplmacros = [
		'$return_path' => ((!empty($x['return_path'])) ? $x['return_path'] : App::$query_string),
		'$action' =>  z_root() . '/item',
		'$share' => $sharebutton,
		'$placeholdtext' => $placeholdtext,
		'$webpage' => $webpage,
		'$placeholdpagetitle' => ((!empty($x['ptlabel'])) ? $x['ptlabel'] : t('Page link name')),
		'$pagetitle' => (!empty($x['pagetitle']) ? $x['pagetitle'] : ''),
		'$id_select' => $id_select,
		'$id_seltext' => t('Post as'),
		'$writefiles' => $writefiles,
		'$bold' => t('Bold'),
		'$italic' => t('Italic'),
		'$highlighter' => t('Highlight selected text'),
		'$underline' => t('Underline'),
		'$quote' => t('Quote'),
		'$code' => t('Code'),
		'$attach' => t('Attach/Upload file'),
		'$weblink' => $weblink,
		'$embedPhotos' => $embedPhotos,
		'$embedPhotosModalTitle' => t('Embed an image from your albums'),
		'$embedPhotosModalCancel' => t('Cancel'),
		'$embedPhotosModalOK' => t('OK'),
		'$setloc' => $setloc,
		'$voting' => t('Toggle voting'),
		'$poll' => t('Toggle poll'),
		'$poll_option_label' => t('Option'),
		'$poll_add_option_label' => t('Add option'),
		'$poll_expire_unit_label' => [t('Minutes'), t('Hours'), t('Days')],
		'$multiple_answers' => ['poll_multiple_answers', t("Allow multiple answers"), '', '', [t('No'), t('Yes')],null,null],
		'$consensus' => ((array_key_exists('item',$x)) ? $x['item']['item_consensus'] : 0),
		'$nocommenttitle' => t('Disable comments'),
		'$nocommenttitlesub' => t('Toggle comments'),
		'$feature_nocomment' => $feature_nocomment,
		'$nocomment' => ((array_key_exists('item',$x)) ? $x['item']['item_nocomment'] : 0),
		'$clearloc' => $clearloc,
		'$title' => ((!empty($x['title'])) ? htmlspecialchars($x['title'], ENT_COMPAT,'UTF-8') : ''),
		'$summary' => ((!empty($x['summary'])) ? htmlspecialchars($x['summary'], ENT_COMPAT,'UTF-8') : ''),
		'$placeholdertitle' => ((!empty($x['placeholdertitle'])) ? $x['placeholdertitle'] : t('Title (optional)')),
		'$placeholdersummary' => ((!empty($x['placeholdersummary'])) ? $x['placeholdersummary'] : t('Summary (optional)')),
		'$catsenabled' => $catsenabled,
		'$category' => ((!empty($x['category'])) ? $x['category'] : ''),
		'$placeholdercategory' => t('Categories (optional, comma-separated list)'),
		'$permset' => t('Permission settings'),
		'$ptyp' => ((!empty($x['ptyp'])) ? $x['ptyp'] : ''),
		'$content' => ((!empty($x['body'])) ? htmlspecialchars($x['body'], ENT_COMPAT,'UTF-8') : ''),
		'$attachment' => ((!empty($x['attachment'])) ? $x['attachment'] : ''),
		'$post_id' => ((!empty($x['post_id'])) ? $x['post_id'] : ''),
		'$defloc' => $x['default_location'] ?? '',
		'$visitor' => $x['visitor'] ?? '',
		'$lockstate' => $x['lockstate'] ?? '',
		'$acl' => $x['acl'] ?? '',
		'$allow_cid' => acl2json($x['permissions']['allow_cid']),
		'$allow_gid' => acl2json($x['permissions']['allow_gid']),
		'$deny_cid' => acl2json($x['permissions']['deny_cid']),
		'$deny_gid' => acl2json($x['permissions']['deny_gid']),
		'$mimeselect' => $mimeselect,
		'$layoutselect' => $layoutselect,
		'$showacl' => ((array_key_exists('showacl', $x)) ? $x['showacl'] : true),
		'$bang' => $x['bang'] ?? '',
		'$profile_uid' => $x['profile_uid'],
		'$preview' => $preview,
		'$source' => ((!empty($x['source'])) ? $x['source'] : ''),
		'$jotplugins' => $jotplugins,
		'$jotnets' => $jotnets,
		'$jotnets_label' => t('Other networks and post services'),
		'$defexpire' => $defexpire,
		'$feature_expire' => $feature_expire,
		'$expires' => t('Set expiration date'),
		'$defpublish' => $defpublish,
		'$feature_future' => $feature_future,
		'$future_txt' => t('Set publish date'),
		'$feature_encrypt' => ((feature_enabled($x['profile_uid'], 'content_encrypt') && (! $webpage)) ? true : false),
		'$encrypt' => t('Encrypt text'),
		'$cipher' => $cipher,
		'$expiryModalOK' => t('OK'),
		'$expiryModalCANCEL' => t('Cancel'),
		'$expanded' => ((!empty($x['expanded'])) ? $x['expanded'] : false),
		'$bbcode' => ((!empty($x['bbcode'])) ? $x['bbcode'] : false),
		'$parent' => ((array_key_exists('parent',$x) && $x['parent']) ? $x['parent'] : 0),
		'$reset' => $reset,
		'$is_owner' => ((local_channel() && (local_channel() == $x['profile_uid'])) ? true : false),
		'$customjotheaders' => '',
		'$custommoretoolsdropdown' => '',
		'$custommoretoolsbuttons' => '',
		'$customsubmitright' => [],
		'$popup' => $popup
	];

	call_hooks('jot_tpl_filter',$tplmacros);

	return replace_macros($tpl, $tplmacros);
}

function conv_sort(array $arr, string $order): array {
	if (empty($arr)) {
		return [];
	}

	$uid = local_channel();
	$thread_allow = $uid
		? PConfig::Get($uid, 'system', 'thread_allow', true)
		: Config::Get('system', 'thread_allow', true);

	$sort_keys = [];
	foreach ($arr as $k => $item) {
		$sort_keys[$k] = $item['created'] ?? '';
	}
	asort($sort_keys, SORT_STRING);

	$parents = [];
	$grouped_children = [];

	foreach (array_keys($sort_keys) as $k) {
		$item = $arr[$k];
		if ($item['id'] == $item['parent']) {
			$parents[] = $item;
		} else {
			if ($thread_allow) {
				$thr_parent = $item['thr_parent'] ?? '';
				if ($thr_parent === '') {
					$thr_parent = $item['parent_mid'] ?? '';
				}
				$grouped_children[$thr_parent][] = $item;
			} else {
				$grouped_children[$item['parent']][] = $item;
			}
		}
	}

	if (stristr($order, 'created')) {
		usort($parents, 'conv_sort_by_created_desc');
	} elseif (stristr($order, 'commented')) {
		usort($parents, 'conv_sort_by_commented_desc');
	} elseif (stristr($order, 'updated')) {
		usort($parents, 'conv_sort_by_updated_desc');
	} elseif (stristr($order, 'ascending')) {
		usort($parents, 'conv_sort_by_created_asc');
	}

	foreach ($parents as $i => $parent) {
		$parents[$i]['children'] = conv_sort_build_tree_helper($parent, $grouped_children, $thread_allow);
	}

	$ret = [];
	conv_sort_flatten_helper($parents, $ret);

	return $ret;
}

function conv_sort_by_created_desc(array $a, array $b): int {
	return $b['created'] <=> $a['created'];
}

function conv_sort_by_commented_desc(array $a, array $b): int {
	return $b['commented'] <=> $a['commented'];
}

function conv_sort_by_updated_desc(array $a, array $b): int {
	$idx_a = ($a['changed'] > $a['edited']) ? $a['changed'] : ($a['edited'] ?? '');
	$idx_b = ($b['changed'] > $b['edited']) ? $b['changed'] : ($b['edited'] ?? '');
	return $idx_b <=> $idx_a;
}

function conv_sort_by_created_asc(array $a, array $b): int {
	return $a['created'] <=> $b['created'];
}

function conv_sort_build_tree_helper(array $parent, array &$grouped_children, bool $thread_allow): array {
	$lookup_key = $thread_allow ? ($parent['mid'] ?? '') : $parent['id'];

	if (!isset($grouped_children[$lookup_key])) {
		return [];
	}

	$children = $grouped_children[$lookup_key];
	foreach ($children as $k => $child) {
		$children[$k]['children'] = conv_sort_build_tree_helper($child, $grouped_children, $thread_allow);
	}

	return $children;
}

function conv_sort_flatten_helper(array $items, array &$ret): void {
	foreach ($items as $item) {
		$ret[] = $item;
		if (!empty($item['children'])) {
			conv_sort_flatten_helper($item['children'], $ret);
		}
	}
}

function find_thread_parent_index($arr,$x) {
	foreach($arr as $k => $v)
		if($v['id'] == $x['parent'])
			return $k;

	return false;
}

function format_location($item) {

	if(strpos($item['location'],'#') === 0) {
		$location = substr($item['location'],1);
		$location = ((strpos($location,'[') !== false) ? zidify_links(bbcode($location)) : $location);
	}
	else {
		$locate = array('location' => $item['location'], 'coord' => $item['coord'], 'html' => '');
		call_hooks('render_location',$locate);
		$location = ((strlen($locate['html'])) ? $locate['html'] : render_location_default($locate));
	}
	return $location;
}

function render_location_default($item) {

	$location = $item['location'];
	$coord = $item['coord'];

	if ($coord) {
		if($location)
			$location .= '&nbsp;(' . $coord . ')';
		else
			$location = $coord;
	}

	if (!$location) {
		return '';
	}

	return '<i class="bi bi-geo-alt" title="' . $location . '"></i>';
}


function prepare_page($item) {

	$naked = 1;
//	$naked = ((get_pconfig($item['uid'],'system','nakedpage')) ? 1 : 0);
	$observer = App::get_observer();
	//240 chars is the longest we can have before we start hitting problems with suhosin sites
	$preview = substr(urlencode($item['body']), 0, 240);
	$link = z_root() . '/' . App::$cmd;
	if(array_key_exists('webpage',App::$layout) && array_key_exists('authored',App::$layout['webpage'])) {
		if(App::$layout['webpage']['authored'] === 'none')
			$naked = 1;
		// ... other possible options
	}

	$body = prepare_body($item, true, [ 'newwin' => false ]);
	$edit_link = (($item['uid'] === local_channel()) ? z_root() . '/editwebpage/' . argv(1) . '/' . $item['id'] : '');

	if(App::$page['template'] == 'none') {
		$tpl = 'page_display_empty.tpl';

		return replace_macros(get_markup_template($tpl), array(
			'$body' => $body['html'],
			'$edit_link' => $edit_link
		));

	}

	$tpl = get_pconfig($item['uid'], 'system', 'pagetemplate');
	if (! $tpl)
		$tpl = 'page_display.tpl';

	return replace_macros(get_markup_template($tpl), array(
		'$author' => (($naked) ? '' : $item['author']['xchan_name']),
		'$auth_url' => (($naked) ? '' : zid($item['author']['xchan_url'])),
		'$date' => (($naked) ? '' : datetime_convert('UTC', date_default_timezone_get(), $item['created'], 'Y-m-d H:i')),
		'$title' => zidify_links(smilies(bbcode($item['title']))),
		'$body' => $body['html'],
		'$preview' => $preview,
		'$link' => $link,
		'$edit_link' => $edit_link
	));
}

function get_responses($response_verbs, $item) {

	$ret = array();
	foreach($response_verbs as $v) {
		if ($v === 'answer') {
			// we require the structure to collect the response hashes
			// but we do not use them for display - do not collect them.
			continue;
		}

		$ret[$v]['count'] = $item[$v . '_count'] ?? 0;
		$ret[$v]['button'] = get_response_button_text($v, $ret[$v]['count'], $item['item_thread_top']);
	}

//logger('ret: ' . print_r($ret,true));

	return $ret;
}

function get_response_button_text($v, $count = 0, $top_level = 0) {
	switch($v) {
		case 'like':
			return ['label' => tt('Like','Likes',$count,'noun'), 'icon' => 'hand-thumbs-up', 'class' => 'like', 'action' => 'dolike'];
			break;
		case 'announce':
			return ['label' => tt('Repeat','Repeats',$count,'noun'), 'icon' => 'repeat', 'class' => 'announce', 'action' => 'jotShare'];
			break;
		case 'dislike':
			return ['label' => tt('Dislike','Dislikes',$count,'noun'), 'icon' => 'hand-thumbs-down', 'class' => 'dislike', 'action' => 'dolike'];
			break;
		case 'comment':
			return ['label' => (($top_level) ? tt('Comment', 'Comments' ,$count, 'noun') : tt('Reply', 'Replies', $count, 'noun')), 'icon' => 'chat', 'class' => 'comment', 'action' => ''];
			break;
		case 'accept':
			return ['label' => tt('Attending','Attending',$count,'noun'), 'icon' => 'calendar-check', 'class' => 'accept', 'action' => 'dolike'];
			break;
		case 'reject':
			return ['label' => tt('Not attending','Not attending',$count,'noun'), 'icon' => 'calendar-x', 'class' => 'reject', 'action' => 'dolike'];
			break;
		case 'tentativeaccept':
			return ['label' => tt('Undecided','Undecided',$count,'noun'), 'icon' => 'calendar', 'class' => 'tentativeaccept', 'action' => 'dolike'];
			break;
		default:
			return [];
			break;
	}
}

function is_unthreaded($mode) {
	return in_array($mode, [
		'network-new',
		'pubstream-new',
		'search',
		'community',
		'moderate'
	]);
}
