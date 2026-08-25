<?php
namespace Zotlabs\Module;

use Zotlabs\Lib\Libsync;

require_once('include/attach.php');
require_once('include/channel.php');
require_once('include/photos.php');


class File_upload extends \Zotlabs\Web\Controller {

	function post() {
		logger('file upload: ' . print_r($_POST,true));
		logger('file upload: ' . print_r($_FILES,true));

		$channel = (($_POST['channick']) ? channelx_by_nick($_POST['channick']) : null);

		if (!$channel) {
			logger('channel not found');
			is_ajax() ? killme() : goaway(z_root() . '/' . $_POST['return_url']);
		}

		$_POST['source'] = 'file_upload';

		if($channel['channel_id'] != local_channel()) {
			$_POST['contact_allow'] = expand_acl($channel['channel_allow_cid']);
			$_POST['group_allow']   = expand_acl($channel['channel_allow_gid']);
			$_POST['contact_deny']  = expand_acl($channel['channel_deny_cid']);
			$_POST['group_deny']    = expand_acl($channel['channel_deny_gid']);
		}

		$_POST['allow_cid'] = ((isset($_POST['contact_allow'])) ? perms2str($_POST['contact_allow']) : '');
		$_POST['allow_gid'] = ((isset($_POST['group_allow'])) ? perms2str($_POST['group_allow']) : '');
		$_POST['deny_cid']  = ((isset($_POST['contact_deny'])) ? perms2str($_POST['contact_deny']) : '');
		$_POST['deny_gid']  = ((isset($_POST['group_deny'])) ? perms2str($_POST['group_deny']) : '');

		if(isset($_POST['filename']) && strlen($_POST['filename'])) {
			$r = attach_mkdir($channel, get_observer_hash(), $_POST);

			if (!$r['success']) {
				notice($r['message'] . EOL);
				is_ajax() ? killme() : goaway(z_root() . '/' . $_POST['return_url']);
			}

			$hash = $r['data']['hash'];
			$sync = attach_export_data($channel,$hash);
			if ($sync) {
				Libsync::build_sync_packet($channel['channel_id'], ['file' => [$sync]]);
			}
		}
		else {

			$matches = [];
			$partial = false;

			if(array_key_exists('HTTP_CONTENT_RANGE',$_SERVER)) {
				$pm = preg_match('/bytes (\d*)\-(\d*)\/(\d*)/',$_SERVER['HTTP_CONTENT_RANGE'],$matches);
				if($pm) {
					logger('Content-Range: ' . print_r($matches,true));
					$partial = true;
				}
			}

			if($partial) {
				$x = save_chunk($channel,$matches[1],$matches[2],$matches[3]);

				if($x['partial']) {
					header('Range: bytes=0-' . (($x['length']) ? $x['length'] - 1 : 0));
					json_return_and_die($x);
				}
				else {
					header('Range: bytes=0-' . (($x['size']) ? $x['size'] - 1 : 0));

					$_FILES['userfile'] = [
						'name'     => $x['name'],
						'type'     => $x['type'],
						'tmp_name' => $x['tmp_name'],
						'error'    => $x['error'],
						'size'     => $x['size']
					];
				}
			}
			else {
				if(! array_key_exists('userfile',$_FILES)) {
					$_FILES['userfile'] = [
						'name'     => $_FILES['files']['name'],
						'type'     => $_FILES['files']['type'],
						'tmp_name' => $_FILES['files']['tmp_name'],
						'error'    => $_FILES['files']['error'],
						'size'     => $_FILES['files']['size']
					];
				}
			}

			$_POST['type'] = $_FILES['userfile']['type'] ?? '';

			$r = attach_store($channel, get_observer_hash(), '', $_POST);
			if (!$r['success']) {
				notice($r['message'] . EOL);
				is_ajax() ? killme() : goaway(z_root() . '/' . $_POST['return_url']);
			}

			$sync = attach_export_data($channel,$r['data']['hash']);
			if ($sync) {
				Libsync::build_sync_packet($channel['channel_id'], ['file' => [$sync]]);
			}
		}

		is_ajax() ? killme() : goaway(z_root() . '/' . $_POST['return_url']);

	}

}
