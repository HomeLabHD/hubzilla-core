<?php
namespace Zotlabs\Module;


class Login extends \Zotlabs\Web\Controller {

	function get() {
		if (local_channel()) {
			goaway(z_root());
		}

		if (remote_channel() && $_SESSION['atoken']) {
			goaway(z_root());
		}

		if (!empty($_GET['retry'])) {
			notice( t('Login failed.') . EOL );
		}

		$o = '<div class="generic-content-wrapper">';
		$o .= '<div class="section-title-wrapper">';
		$o .= '<h2 class="">' . t('Login') . '</h2>';
		$o .= '</div>';
		$o .= '<div class="section-content-wrapper">';
		$o .= login(true);
		$o .= '</div>';
		$o .= '</div>';

		return $o;
	}

}
