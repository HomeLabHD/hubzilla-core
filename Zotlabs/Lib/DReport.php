<?php
namespace Zotlabs\Lib;

use Zotlabs\Lib\Config;
use Zotlabs\Lib\PConfig;

class DReport {

	private $location;
	private $sender;
	private $recipient;
	private $name;
	private $message_id;
	private $message_uuid;
	private $status;
	private $date;

	function __construct($location, $sender, $recipient, $message_id, $message_uuid = '', $status = 'deliver') {
		$this->location     = $location;
		$this->sender       = $sender;
		$this->recipient    = $recipient;
		$this->name         = EMPTY_STR;
		$this->message_id   = $message_id;
		$this->message_uuid = $message_uuid;
		$this->status       = $status;
		$this->date         = datetime_convert();
	}

	function update($status) {
		$this->status = $status;
		$this->date   = datetime_convert();
	}

	function set_name($name) {
		$this->name = $name;
	}

	function addto_update($status) {
		$this->status = $this->status . ', ' . $status;
	}


	function set($arr) {
		$this->location     = $arr['location'];
		$this->sender       = $arr['sender'];
		$this->recipient    = $arr['recipient'];
		$this->name         = $arr['name'];
		$this->message_id   = $arr['message_id'];
		$this->message_uuid = $arr['message_uuid'] ?? '';
		$this->status       = $arr['status'];
		$this->date         = $arr['date'];
	}

	function get() {
		return array(
			'location'     => $this->location,
			'sender'       => $this->sender,
			'recipient'    => $this->recipient,
			'name'         => $this->name,
			'message_id'   => $this->message_id,
			'message_uuid' => $this->message_uuid,
			'status'       => $this->status,
			'date'         => $this->date
		);
	}

	/**
	 * @brief decide whether to store a returned delivery report
	 *
	 * @param array $dr
	 * @return boolean
	 */

	static function is_storable($dr) {

		if (Config::Get('system', 'disable_dreport')) {
			return false;
		}

		/**
		 * @hooks dreport_is_storable
		 *   Called before storing a dreport record to determine whether to store it.
		 *   * \e array
		 */

		call_hooks('dreport_is_storable', $dr);

		// let plugins accept or reject - if neither, continue on
		if (array_key_exists('accept',$dr) && intval($dr['accept'])) {
			return true;
		}

		if (array_key_exists('reject',$dr) && intval($dr['reject'])) {
			return false;
		}

		if (!$dr['sender']) {
			return false;
		}

		// do not store dismissed create activities
		if ($dr['status'] === 'not a collection activity') {
			return false;
		}

		// Is the sender one of our channels?

		$channel_id = channel_id_by_hash($dr['sender']);

		if (!$channel_id) {
			return false;
		}

		// is the recipient one of our connections, or do we want to store every report?

		if (PConfig::Get($channel_id, 'system', 'dreport_store_all')) {
			return true;
		}

		// We always add ourself as a recipient to private and relayed posts
		// So if a remote site says they can't find us, that's no big surprise
		// and just creates a lot of extra report noise

		if (($dr['location'] !== z_root()) && ($dr['sender'] === $dr['recipient']) && ($dr['status'] === 'recipient not found')) {
			return false;
		}

		// If you have a private post with a recipient list, every single site is going to report
		// back a failed delivery for anybody on that list that isn't local to them. We're only
		// concerned about this if we have a local hubloc record which says we expected them to
		// have a channel on that site.

		if ($dr['status'] === 'recipient_not_found') {
			$r = q("select hubloc_id from hubloc where hubloc_hash = '%s' and hubloc_url = '%s'",
				dbesc($dr['recipient']),
				dbesc($dr['location'])
			);

			if (!$r) {
				return false;
			}
		}

		$r = abook_id_by_hash($dr['recipient'], $channel_id);

		if ($r) {
			return true;
		}

		return false;
	}


}
