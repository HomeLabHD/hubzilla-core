<?php
namespace Zotlabs\Module;


class Smilies extends \Zotlabs\Web\Controller {

	function init() {
		json_return_and_die(get_emojis());
	}

}
