<?php

namespace Zotlabs\Lib;

class Text {

	/**
	 * use this on "body" or "content" input where angle chars shouldn't be removed,
	 * and allow them to be safely displayed.
	 *
	 * @param string $string
	 *
	 * @return string
	 */

	public static function escape_tags(string $string): string {
		if (!$string) {
			return EMPTY_STR;
		}

		return (htmlspecialchars($string, ENT_COMPAT, 'UTF-8', false));
	}

}
