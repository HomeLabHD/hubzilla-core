<?php /** @file */

namespace Zotlabs\Lib;

use App;
use Zotlabs\Access\AccessList;

require_once('include/text.php');

/**
 * A thread item
 */

class ThreadItem {

	public  $data = array();
	private $template = 'conv_item.tpl';
	private $comment_box_template = 'comment_item.tpl';
	private $commentable = false;
	// list of supported reaction emojis - a site can over-ride this via config system.reactions
	private $reactions = ['slightly_smiling_face','clapping_hands','bottle_with_popping_cork','kiss_mark','disappointed_face','red_heart','grinning_face','astonished_face','sleeping_face','winking_face_with_tongue','smiling_face_with_halo','smiling_face_with_horns'];
	private $toplevel = false;
	private $children = array();
	private $parent = null;
	private $conversation = null;
	private $redirect_url = null;
	private $owner_addr = '';
	private $owner_url = '';
	private $owner_photo = '';
	private $owner_name = '';
	private $wall_to_wall = false;
	private $threaded = false;
	private $visiting = false;
	private $channel = null;
	private $display_mode = 'normal';
	private $reload = '';

	public function __construct($data) {

		$this->data = $data;
		$this->toplevel = ($this->get_id() == $this->get_data_value('parent'));
		$this->threaded = ((local_channel()) ? PConfig::Get(local_channel(), 'system', 'thread_allow', true) : Config::Get('system', 'thread_allow', true));

		// Prepare the children
		if(isset($data['children'])) {

			foreach($data['children'] as $item) {

				/*
				 * Only add those that will be displayed
				 */

				if((! visible_activity($item)) || array_key_exists('blocked',$item)) {
					continue;
				}

				$child = new ThreadItem($item);
				$this->add_child($child);
			}

			// performance: we have already added the children
			unset($this->data['children']);
		}

		// allow a site to configure the order and content of the reaction emoji list
		if($this->toplevel) {
			$x = Config::Get('system','reactions');
			if($x && is_array($x) && count($x)) {
				$this->reactions = $x;
			}
		}
	}

	/**
	 * Get data in a form usable by a conversation template
	 *
	 * Returns:
	 *      _ The data requested on success
	 *      _ false on failure
	 */

	public function get_template_data($thread_level=1, $conv_flags = []) {

		$result = [];
		$item = $this->get_data();
		$commentww = '';
		$sparkle = '';
		$buttons = '';
		$dropping = false;
		$star = false;
		$isstarred = "unstarred bi-star";
		$is_comment = false;
		$is_item = false;
		$osparkle = '';
		$total_children = $this->count_descendants();
		$unseen_comments = ((isset($item['real_uid']) && $item['real_uid']) ? 0 : $this->count_unseen_descendants());

 		$conv = $this->get_conversation();
		$observer = $conv->get_observer();

		$conv->mid_uuid_map[$item['mid']] = $item['uuid'];

		$acl = new AccessList([]);
		$acl->set($item);

		$lock = ((intval($item['item_private']) || ($item['uid'] == local_channel() && $acl->is_private()))
			? t('Restricted message')
			: false);

		// 1 = restricted message, 2 = direct message
		$locktype = intval($item['item_private']);

		if ($locktype === 2) {
			$lock = t('Private message');
		}

		// 0 = limited based on public policy
		if ($item['uid'] == local_channel() && intval($item['item_private']) && !$acl->is_private() && strlen($item['public_policy'])) {
			$lock = t('Public Policy');
			$locktype = 0;
		}

		$shareable = ((local_channel() && $conv->get_profile_owner() == local_channel()) && (intval($item['item_private']) === 0));

		// allow an exemption for sharing stuff from your private feeds
		if($item['author']['xchan_network'] === 'rss')
			$shareable = true;

		$repeatable = ((local_channel() && $conv->get_profile_owner() == local_channel()) && intval($item['item_private']) === 0 && in_array($item['author']['xchan_network'], ['zot6', 'activitypub']));

		// @fixme
		// Have recently added code to properly handle polls in group reshares by redirecting all of the poll responses to the group.
		// Sharing a poll using a regular embedded share is harder because the poll will need to fork. This is due to comment permissions.
		// The original poll author may not accept responses from strangers. Forking the poll will receive responses from the sharer's
		// followers, but there's no elegant way to merge these two sets of results together. For now, we'll disable sharing polls.

		if ($item['obj_type'] === 'Question') {
			$shareable = false;
		}

		$privacy_warning = ($item['owner']['xchan_network'] === 'activitypub' && intval($item['item_private']) === 1);

		if ($lock) {
 			if (($item['mid'] == $item['parent_mid']) && isset($item['term']) && count(get_terms_oftype($item['term'], TERM_FORUM))) {
 				$privacy_warning = true;
				$conv_flags['parent_privacy_warning'] = true;
 			}
		}

		$privacy_warning = (isset($conv_flags['parent_privacy_warning'])) ? $conv_flags['parent_privacy_warning'] : $privacy_warning;

		if ($lock && $privacy_warning) {
			$lock = t('Privacy conflict. Discretion advised.');
		}

		$mode = $conv->get_mode();

		if(local_channel() && $observer['xchan_hash'] === $item['author_xchan'])
			$edpost = array(z_root() . '/editpost/' . $item['id'], t('Edit'));
		else
			$edpost = false;

		if($observer && $observer['xchan_hash']
			&& ($observer['xchan_hash'] == $this->get_data_value('author_xchan')
			|| $observer['xchan_hash'] == $this->get_data_value('owner_xchan')
			|| $observer['xchan_hash'] == $this->get_data_value('source_xchan')
			|| $this->get_data_value('uid') == local_channel()))
			$dropping = true;


		if(array_key_exists('real_uid',$item)) {
			$edpost = false;
			$dropping = false;
		}

		$drop = [];
		if($dropping) {
			$drop = array(
				'dropping' => $dropping,
				'delete' => t('Delete'),
			);
		}
		elseif(is_site_admin()) {
			$drop = [ 'dropping' => true, 'delete' => t('Admin Delete') ];
		}

		$filer = (((local_channel() && $conv->get_profile_owner() === local_channel()) || (local_channel() && App::$module === 'pubstream')) ? t("Save to Folder") : false);

		$profile_avatar = $item['author']['xchan_photo_s'];
		$profile_link   = chanlink_hash($item['author_xchan']);
		$profile_name   = $item['author']['xchan_name'];

		$location = format_location($item);
		$isevent = false;
		$attend = null;

		// process action responses - e.g. like/dislike/attend/agree/whatever
		$response_verbs[] = 'like';

		if(feature_enabled($conv->get_profile_owner(),'dislike')) {
			$response_verbs[] = 'dislike';
		}

		if ($repeatable) {
			$response_verbs[] = 'announce';
		}

		if (in_array($item['obj_type'], ['Event', ACTIVITY_OBJ_EVENT])) {
			$response_verbs[] = 'accept';
			$response_verbs[] = 'reject';
			$response_verbs[] = 'tentativeaccept';
			if($this->is_commentable() && $observer) {
				$isevent = true;
				$attend = array( t('I will attend'), t('I will not attend'), t('I might attend'));
			}
		}

		if ($item['obj_type'] === 'Question') {
			$response_verbs[] = 'answer';
		}

		$response_verbs[] = 'comment';
		$responses = get_responses($response_verbs, $item);

		/*
		 * We should avoid doing this all the time, but it depends on the conversation mode
		 * And the conv mode may change when we change the conv, or it changes its mode
		 * Maybe we should establish a way to be notified about conversation changes
		 */

		$this->check_wall_to_wall();

		if($this->is_toplevel()) {
			$conv->comments_total = $responses['comment']['count'] ?? 0;

			if((local_channel() && $conv->get_profile_owner() === local_channel()) || (local_channel() && App::$module === 'pubstream')) {
				$star = [
					'toggle' => t("Toggle Star Status"),
					'isstarred' => ((intval($item['item_starred'])) ? true : false),
				];
			}
		}
		else {
			$is_comment = true;
		}

		$verified = (intval($item['item_verified']) ? t('Message signature validated') : '');
		$forged = ((($item['sig']) && (! intval($item['item_verified']))) ? t('Message signature incorrect') : '');
		$unverified = '' ; // (($this->is_wall_to_wall() && (! intval($item['item_verified']))) ? t('Message cannot be verified') : '');

		$settings = '';

		$tagger = [];

		// FIXME - check this permission
		if(local_channel() && $conv->get_profile_owner() == local_channel()) {
			/* disable until we agree on how to implemnt this in zot6/activitypub
			$tagger = array(
				'tagit' => t("Add Tag"),
				'classtagger' => "",
			);
			*/

			$settings = t('Conversation Features');
		}

		$has_bookmarks = false;
		if(Apps::system_app_installed(local_channel(), 'Bookmarks') && isset($item['term']) && is_array($item['term'])) {
			foreach($item['term'] as $t) {
				if(($t['ttype'] == TERM_BOOKMARK))
					$has_bookmarks = true;
			}
		}

		$has_event = false;
		if((in_array($item['obj_type'], ['Event', ACTIVITY_OBJ_EVENT])) && $conv->get_profile_owner() == local_channel())
			$has_event = true;

		$reply_to = [];
		$reactions_allowed = false;

		if($this->is_commentable() && $observer) {
			$reply_to = array( t("Reply to this message"), t("reply"), t("Reply to"));
			$reactions_allowed = true;
		}

		$share = [];
		$embed = [];
		if ($shareable) {
			$embed = [t('Share'), t('share')];
		}

		if ($repeatable) {
			$share = [t('Repeat'), t('repeat')];
		}

		$dreport = '';

		$keep_reports = intval(Config::Get('system','expire_delivery_reports'));
		if($keep_reports === 0)
			$keep_reports = 10;

		$dreport_link = '';
		if((intval($item['item_type']) == ITEM_TYPE_POST) && (! Config::Get('system','disable_dreport')) && strcmp(datetime_convert('UTC','UTC',$item['created']),datetime_convert('UTC','UTC',"now - $keep_reports days")) > 0) {
			$dreport = t('Delivery Report');
			$dreport_link = '?mid=' . $item['mid'];
		}

		$is_new = false;
		if(strcmp(datetime_convert('UTC','UTC',$item['created']),datetime_convert('UTC','UTC','now - 12 hours')) > 0)
			$is_new = true;

		localize_item($item);

		$opts = (($item['resource_type'] === 'event') ? ['is_event_item' => true] : []);
		$body = prepare_body($item, true, $opts);

		// $viewthread (below) is only valid in list mode. If this is a channel page, build the thread viewing link
		// since we can't depend on llink or plink pointing to the right local location.

		$owner_address = substr($item['owner']['xchan_addr'],0,strpos($item['owner']['xchan_addr'],'@'));
		$viewthread = $item['llink'];
		if($conv->get_mode() === 'channel')
			$viewthread = z_root() . '/channel/' . $owner_address . '?f=&mid=' . urlencode(gen_link_id($item['mid']));

		$comment_count_txt = ['label' => sprintf(tt('%d comment', '%d comments', $total_children), $total_children), 'count' => $total_children];
		$list_unseen_txt = $unseen_comments ? ['label' => sprintf(t('%d unseen'), $unseen_comments), 'count' => $unseen_comments] : [];

		$children = $this->get_children();

		$has_tags = (($body['tags'] || $body['categories'] || $body['mentions'] || $body['attachments'] || $body['folders']) ? true : false);

                $dropdown_extras_arr = [ 'item' => $item , 'dropdown_extras' => '' ];
                call_hooks('dropdown_extras',$dropdown_extras_arr);
                $dropdown_extras = $dropdown_extras_arr['dropdown_extras'];

		$midb64 = $item['uuid'];
		$mids = [ $item['uuid'] ];

		$json_mids = json_encode($mids);

		// Pinned item processing
		$allowed_type = (in_array($item['item_type'], Config::Get('system', 'pin_types', [ ITEM_TYPE_POST ])) ? true : false);
		$pinned_items = ($allowed_type ? get_pconfig($item['uid'], 'pinned', $item['item_type'], []) : []);
		$pinned = ((!empty($pinned_items) && in_array($midb64, $pinned_items)) ? true : false);

		$contact = [];

		if(App::$contacts && array_key_exists($item['author_xchan'], App::$contacts)) {
			$contact = App::$contacts[$item['author_xchan']];
		}

		$load_more = false;
		$load_more_title = '';
		$comments_total_percent = 0;
		if ($conv->comments_total > 3) {
			// provide a load more comments button
			$load_more = true;
			$load_more_title = sprintf(t('Load the next batch of total %d replies'), $conv->comments_total);
			$comments_total_percent = round(100 * 3 / $conv->comments_total);
		}

		$tmp_item = array(
			'template' => $this->get_template(),
			'mode' => $mode,
			'item_type' => intval($item['item_type']),
			'body' => $body['html'],
			'tags' => $body['tags'],
			'categories' => $body['categories'],
			'mentions' => $body['mentions'],
			'attachments' => $body['attachments'],
			'folders' => $body['folders'],
			'text' => strip_tags($body['html']),
			'id' => $this->get_id(),
			'parent' => $item['parent'],
			'mid' => $midb64,
			'mids' => $json_mids,
			'author_id' => (($item['author']['xchan_addr']) ? $item['author']['xchan_addr'] : $item['author']['xchan_url']),
			'author_is_group_actor' => (($item['author']['xchan_pubforum']) ? t('Forum') : ''),
			'isevent' => $isevent,
			'attend' => $attend,
			'linktitle' => (($item['author']['xchan_addr']) ? $item['author']['xchan_addr'] : $item['author']['xchan_url']),
			'olinktitle' => (($item['owner']['xchan_addr']) ? $item['owner']['xchan_addr'] : $item['owner']['xchan_url']),
			'llink' => $item['llink'],
			'viewthread' => $viewthread,
			'to' => t('to'),
			'via' => t('via'),
			'wall' => t('Wall-to-Wall'),
			'vwall' => t('via Wall-To-Wall:'),
			'profile_url' => $profile_link,
			'thread_action_menu' => thread_action_menu($item,$conv->get_mode()),
			'thread_author_menu' => thread_author_menu($item,$conv->get_mode()),
			'dreport' => $dreport,
			'dreport_link' => $dreport_link,
			'name' => $profile_name,
			'thumb' => $profile_avatar,
			'osparkle' => $osparkle,
			'sparkle' => $sparkle,
			'title' => $item['title'],
			'title_tosource' => get_pconfig($conv->get_profile_owner(),'system','title_tosource'),
			'app' => $item['app'],
			'str_app' => sprintf( t('from %s'), $item['app']),
			'isotime' => datetime_convert('UTC', date_default_timezone_get(), $item['created'], 'c'),
			'localtime' => datetime_convert('UTC', date_default_timezone_get(), $item['created']),
			'editedtime' => (($item['edited'] != $item['created']) ? sprintf(t('Last edited %s'), relative_time($item['edited'])) : ''),
			'expiretime' => (($item['expires'] > NULL_DATE) ? sprintf(t('Expires %s'), relative_time($item['expires'])) : ''),
			'lock' => $lock,
			'locktype' => $locktype,
			'delayed' => (($item['item_delayed']) ? sprintf(t('Published %s'), relative_time($item['created'])) : ''),
			'privacy_warning' => $privacy_warning,
			'verified' => $verified,
			'unverified' => $unverified,
			'forged' => $forged,
			'location' => $location,
			'divider' => get_pconfig($conv->get_profile_owner(),'system','item_divider'),
			'attend_label' => t('Attend'),
			'attend_title' => t('Attendance Options'),
			'vote_label' => t('Vote'),
			'vote_title' => t('Voting Options'),
			'is_comment' => $is_comment,
			'is_new' => $is_new,
			'owner_addr' => $this->get_owner_addr(),
			'owner_url' => $this->get_owner_url(),
			'owner_photo' => $this->get_owner_photo(),
			'owner_name' => $this->get_owner_name(),
			'photo' => $body['photo'],
			'event' => $body['event'],
			'has_tags' => $has_tags,
			'reactions' => $this->reactions,
			// Item toolbar buttons
			'emojis'	=> (($this->is_toplevel() && $this->is_commentable() && $observer && feature_enabled($conv->get_profile_owner(),'emojis')) ? '1' : ''),
			'reply_to'	=> ((feature_enabled($conv->get_profile_owner(),'reply_to')) ? $reply_to : ''),
			'top_hint'	=> t("Go to previous comment"),
			'share'     => $share,
			'embed'     => $embed,
			'rawmid'	=> $item['mid'],
			'parent_mid' => $item['parent_mid'],
			'plink'     => get_plink($item),
			'edpost'    => $edpost,
			'star'      => ((feature_enabled($conv->get_profile_owner(),'star_posts') && ($item['item_type'] == ITEM_TYPE_POST)) ? $star : ''),
			'tagger'    => ((feature_enabled($conv->get_profile_owner(),'commtag')) ? $tagger : ''),
			'filer'     => ((feature_enabled($conv->get_profile_owner(),'filing') && ($item['item_type'] == ITEM_TYPE_POST)) ? $filer : ''),
			'pinned'    => ($pinned ? t('Pinned post') : ''),
			'pinnable'  => (($this->is_toplevel() && local_channel() && $item['owner_xchan'] == $observer['xchan_hash'] && $allowed_type && $item['item_private'] == 0 && $item['item_delayed'] == 0) ? '1' : ''),
			'pinme'     => ($pinned ? t('Unpin from the top') : t('Pin to the top')),
			'bookmark'  => (($conv->get_profile_owner() == local_channel() && local_channel() && $has_bookmarks) ? t('Save Bookmarks') : ''),
			'addtocal'  => (($has_event) ? t('Add to Calendar') : ''),
			'drop'      => $drop,
			'dropdown_extras' => $dropdown_extras,
			// end toolbar buttons
			'unseen_comments' => $unseen_comments,
			'comment_count' => $total_children,
			'comment_count_txt' => $comment_count_txt,
			'list_unseen_txt' => $list_unseen_txt,
			'markseen' => t('Mark all comments seen'),
			'responses' => $responses,
		//	'my_responses' => $my_responses,
			'modal_dismiss' => t('Close'),
			'comment' => ($item['item_delayed'] ? '' : $this->get_comment_box()),
			'comment_hidden' => feature_enabled($conv->get_profile_owner(),'reply_to'),
			'no_comment' => (($item['item_thread_top'] && $item['item_nocomment'])? t('Comments disabled') : ''),
			'previewing' => ($conv->is_preview() ? true : false ),
			'preview_lbl' => t('This is an unsaved preview'),
			'wait' => t('Please wait'),
			'thread_level' => $thread_level,
			'settings' => $settings,
			'thr_parent_uuid' => (($item['parent_mid'] !== $item['thr_parent'] && isset($conv->mid_uuid_map[$item['thr_parent']])) ? $conv->mid_uuid_map[$item['thr_parent']] : ''),
			'contact_id' => (($contact) ? $contact['abook_id'] : ''),
			'moderate' => ($item['item_blocked'] == ITEM_MODERATED),
			'moderate_approve' => t('Approve'),
			'moderate_delete' => t('Delete'),
			'rtl' => in_array($item['lang'], rtl_languages()),
			'reactions_allowed' => $reactions_allowed,
			'reaction_str' => [t('Add yours'), t('Remove yours')],
			'is_contained' => $this->is_toplevel() && str_contains($item['tgt_type'], 'Collection'),
			'observer_activity' => [
				'like' => intval($item['observer_like_count'] ?? 0),
				'dislike' => intval($item['observer_dislike_count'] ?? 0),
				'announce' => intval($item['observer_announce_count'] ?? 0),
				'comment' => intval($item['observer_comment_count'] ?? 0),
				'accept' => intval($item['observer_accept_count'] ?? 0),
				'reject' => intval($item['observer_reject_count'] ?? 0),
				'tentativeaccept' => intval($item['observer_tentativeaccept_count'] ?? 0)
			],
			'threaded' => $this->threaded,
			'blog_mode' => $this->get_display_mode() === 'list',
			'collapse_comments' => t('show less'),
			'expand_comments' =>  $this->threaded ? t('show more') : t('show all'),
			'load_more' => $load_more,
			'load_more_title' => $load_more_title,
			'comments_total' => $conv->comments_total,
			'comments_total_percent' => $comments_total_percent
		);

		$arr = array('item' => $item, 'output' => $tmp_item);
		call_hooks('display_item', $arr);

		$result = $arr['output'];

		$result['children'] = array();
		$nb_children = count($children);

		$visible_comments = 3; // Config::Get('system', 'expanded_comments', 3);

		if(($this->get_display_mode() === 'normal') && ($nb_children > 0)) {
			foreach($children as $child) {
				$result['children'][] = $child->get_template_data($thread_level + 1, $conv_flags);
			}

			// Collapse
			if($thread_level === 1 && $nb_children > $visible_comments) {
				$result['children'][0]['comment_firstcollapsed'] = true;
				$result['children'][0]['num_comments'] = $comment_count_txt['label'];
				$result['children'][$nb_children - ($visible_comments + 1)]['comment_lastcollapsed'] = true;
			}
		}

		$result['private'] = $item['item_private'];
		$result['toplevel'] = ($this->is_toplevel() ? 'toplevel_item' : '');

		if($this->is_threaded()) {
			$result['flatten'] = false;
			$result['threaded'] = true;
		}
		else {
			$result['flatten'] = true;
			$result['threaded'] = false;
		}

		return $result;
	}

	public function get_id() {
		return $this->get_data_value('id');
	}

	public function get_display_mode() {
		return $this->display_mode;
	}

	public function set_display_mode($mode) {
		$this->display_mode = $mode;
	}

	public function is_threaded() {
		return $this->threaded;
	}

	public function set_reload($val) {
		$this->reload = $val;
	}

	public function get_reload() {
		return $this->reload;
	}

	public function set_commentable($val) {
		$this->commentable = $val;
		foreach($this->get_children() as $child)
			$child->set_commentable($val);
	}

	public function is_commentable() {
		return $this->commentable;
	}

	/**
	 * Add a child item
	 */
	public function add_child($item) {
		$item_id = $item->get_id();
		if(!$item_id) {
			logger('[ERROR] Item::add_child : Item has no ID!!', LOGGER_DEBUG);
			return false;
		}
		if($this->get_child($item->get_id())) {
			logger('[WARN] Item::add_child : Item already exists ('. $item->get_id() .').', LOGGER_DEBUG);
			return false;
		}
		/*
		 * Only add what will be displayed
		 */

		if(activity_match($item->get_data_value('verb'), ['Like', 'Dislike', ACTIVITY_LIKE, ACTIVITY_DISLIKE])) {
			return false;
		}

		$item->set_parent($this);
		$this->children[] = $item;
		return end($this->children);
	}

	/**
	 * Get a child by its ID
	 */
	public function get_child($id) {
		foreach($this->get_children() as $child) {
			if($child->get_id() == $id)
				return $child;
		}
		return null;
	}

	/**
	 * Get all our children
	 */
	public function get_children() {
		return $this->children;
	}

	/**
	 * Set our parent
	 */
	protected function set_parent($item) {
		$parent = $this->get_parent();
		if($parent) {
			$parent->remove_child($this);
		}
		$this->parent = $item;
		$this->set_conversation($item->get_conversation());
	}

	/**
	 * Remove our parent
	 */
	protected function remove_parent() {
		$this->parent = null;
		$this->conversation = null;
	}

	/**
	 * Remove a child
	 */
	public function remove_child($item) {
		$id = $item->get_id();
		foreach($this->get_children() as $key => $child) {
			if($child->get_id() == $id) {
				$child->remove_parent();
				unset($this->children[$key]);
				// Reindex the array, in order to make sure there won't be any trouble on loops using count()
				$this->children = array_values($this->children);
				return true;
			}
		}
		logger('[WARN] Item::remove_child : Item is not a child ('. $id .').', LOGGER_DEBUG);
		return false;
	}

	/**
	 * Get parent item
	 */
	protected function get_parent() {
		return $this->parent;
	}

	/**
	 * set conversation
	 */
	public function set_conversation($conv) {
		$previous_mode = ($this->conversation ? $this->conversation->get_mode() : '');

		$this->conversation = $conv;

		// Set it on our children too
		foreach($this->get_children() as $child)
			$child->set_conversation($conv);
	}

	/**
	 * get conversation
	 */
	public function get_conversation() {
		return $this->conversation;
	}

	/**
	 * Get raw data
	 *
	 * We shouldn't need this
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Get a data value
	 *
	 * Returns:
	 *      _ value on success
	 *      _ false on failure
	 */
	public function get_data_value($name) {
		if(!isset($this->data[$name])) {
//			logger('[ERROR] Item::get_data_value : Item has no value name "'. $name .'".', LOGGER_DEBUG);
			return false;
		}

		return $this->data[$name];
	}

	/**
	 * Get template
	 */
	public function get_template() {
		return $this->template;
	}


	public function set_template($t) {
		$this->template = $t;
	}

	/**
	 * Check if this is a toplevel post
	 */
	private function is_toplevel() {
		return $this->toplevel;
	}

	/**
	 * Count the total of our descendants
	 */
	private function count_descendants() {
		$children = $this->get_children();
		$total = count($children);
		if($total > 0) {
			foreach($children as $child) {
				$total += $child->count_descendants();
			}
		}
		return $total;
	}

	private function count_unseen_descendants() {
		$children = $this->get_children();
		$total = count($children);
		if($total > 0) {
			$total = 0;
			foreach($children as $child) {
				if((! visible_activity($child->data)) || array_key_exists('author_blocked',$child->data)) {
					continue;
				}
				if(intval($child->data['item_unseen']))
					$total ++;
			}
		}
		return $total;
	}


	/**
	 * Get the template for the comment box
	 */
	private function get_comment_box_template() {
		return $this->comment_box_template;
	}

	/**
	 * Get the comment box
	 *
	 * Returns:
	 *      _ The comment box string (empty if no comment box)
	 *      _ false on failure
	 */
	private function get_comment_box() {

		if(!$this->is_toplevel()) {
			return '';
		}

		$comment_box = '';
		$conv = $this->get_conversation();

//		logger('Commentable conv: ' . $conv->is_commentable());

		if(! $this->is_commentable())
			return;

		$template = get_markup_template($this->get_comment_box_template());

		$observer = $conv->get_observer();

		$arr = array('comment_buttons' => '','id' => $this->get_id());
		call_hooks('comment_buttons',$arr);
		$comment_buttons = $arr['comment_buttons'];

		$comment_box = replace_macros($template,array(
			'$return_path' => '',
			'$threaded' => $this->is_threaded(),
			'$jsreload' => $conv->reload,
			'$type' => (($conv->get_mode() === 'channel') ? 'wall-comment' : 'net-comment'),
			'$id' => $this->get_id(),
			'$parent' => $this->get_id(),
			'$comment_buttons' => $comment_buttons,
			'$profile_uid' =>  $conv->get_profile_owner(),
			'$mylink' => $observer['xchan_url'],
			'$mytitle' => t('This is you'),
			'$myphoto' => $observer['xchan_photo_s'],
			'$comment' => t('Comment'),
			'$submit' => t('Submit'),
			'$edbold' => t('Bold'),
			'$editalic' => t('Italic'),
			'$edhighlighter' => t('Highlight selected text'),
			'$eduline' => t('Underline'),
			'$edquote' => t('Quote'),
			'$edcode' => t('Code'),
			'$edimg' => t('Embed (existing) photo from your photo albums'),
			'$edatt' => t('Attach/Upload file'),
			'$edurl' => t('Insert Link'),
			'$edvideo' => t('Video'),
			'$preview' => t('Preview'),
			'$can_upload' => (perm_is_allowed($conv->get_profile_owner(),get_observer_hash(),'write_storage') && $conv->is_uploadable()),
			'$feature_encrypt' => ((feature_enabled($conv->get_profile_owner(),'content_encrypt')) ? true : false),
			'$encrypt' => t('Encrypt text'),
			'$cipher' => $conv->get_cipher(),
			'$sourceapp' => App::$sourcename,
			'$observer' => get_observer_hash(),
			'$anoncomments' => ((in_array($conv->get_mode(), ['channel', 'display', 'cards', 'articles']) && perm_is_allowed($conv->get_profile_owner(),'','post_comments')) ? true : false),
			'$anonname' => [ 'anonname', t('Your full name (required)') ],
			'$anonmail' => [ 'anonmail', t('Your email address (required)') ],
			'$anonurl'  => [ 'anonurl',  t('Your website URL (optional)') ]
		));

		return $comment_box;
	}

	private function get_redirect_url() {
		return $this->redirect_url;
	}

	/**
	 * Check if we are a wall to wall or announce item and set the relevant properties
	 */
	protected function check_wall_to_wall() {
		$conv = $this->get_conversation();
		$this->wall_to_wall = false;
		$this->owner_url = '';
		$this->owner_addr = '';
		$this->owner_photo = '';
		$this->owner_name = '';

		if($conv->get_mode() === 'channel')
			return;

		if($this->is_toplevel() && ($this->get_data_value('author_xchan') != $this->get_data_value('owner_xchan'))) {
			$this->owner_url = chanlink_hash($this->data['owner']['xchan_hash']);
			$this->owner_addr = $this->data['owner']['xchan_addr'];
			$this->owner_photo = $this->data['owner']['xchan_photo_s'];
			$this->owner_name = $this->data['owner']['xchan_name'];
			$this->wall_to_wall = true;
		}
		elseif($this->is_toplevel() && $this->get_data_value('verb') === 'Announce' && isset($this->data['source'])) {
			$this->owner_url = chanlink_hash($this->data['source']['xchan_hash']);
			$this->owner_addr = $this->data['source']['xchan_addr'];
			$this->owner_photo = $this->data['source']['xchan_photo_s'];
			$this->owner_name = $this->data['source']['xchan_name'];
			$this->wall_to_wall = true;
		}
	}

	private function is_wall_to_wall() {
		return $this->wall_to_wall;
	}

	private function get_owner_url() {
		return $this->owner_url;
	}

	private function get_owner_addr() {
		return $this->owner_addr;
	}

	private function get_owner_photo() {
		return $this->owner_photo;
	}

	private function get_owner_name() {
		return $this->owner_name;
	}

	private function is_visiting() {
		return $this->visiting;
	}

}
