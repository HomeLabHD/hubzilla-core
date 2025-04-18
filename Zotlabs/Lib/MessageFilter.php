<?php

namespace Zotlabs\Lib;

require_once('include/html2plain.php');

class MessageFilter {

	public static function evaluate($item, $incl, $excl) {

		$text = prepare_text($item['body'],((isset($item['mimetype'])) ? $item['mimetype'] : 'text/bbcode'));
		$text = html2plain(($item['title']) ? $item['title'] . ' ' . $text : $text);

		$lang = null;
		if ((strpos($incl, 'lang=') !== false) || (strpos($excl, 'lang=') !== false) || (strpos($incl, 'lang!=') !== false) || (strpos($excl, 'lang!=') !== false)) {
			$lang = detect_language($text);
		}

		$tags = ((isset($item['term']) && is_array($item['term']) && count($item['term'])) ? $item['term'] : false);

		$until = null;

		// exclude always has priority

		$exclude = (($excl) ? explode("\n", $excl) : null);

		if ($exclude) {
			foreach ($exclude as $word) {
				$word = html_entity_decode(trim($word));
				if (! $word) {
					continue;
				}
				if (isset($lang) && ((strpos($word, 'lang=') === 0) || (strpos($word, 'lang!=') === 0))) {
					if (!strlen($lang)) {
						// Result is ambiguous. As we are matching deny rules only at this time, continue tests.
						// Any matching deny rule concludes testing.
						continue;
					}
					if (strpos($word, 'lang=') === 0 && strcasecmp($lang, trim(substr($word, 5))) == 0) {
						return false;
					} elseif (strpos($word, 'lang!=') === 0 && strcasecmp($lang, trim(substr($word, 6))) != 0) {
						return false;
					}
				}
				elseif (str_starts_with($word, 'until=')) {
					$until = strtotime(trim(substr($word, 6)));
					if ($until < strtotime($item['created'])) {
						return false;
					}
				}
				elseif (substr($word, 0, 1) === '#' && $tags) {
					foreach ($tags as $t) {
						if ((($t['ttype'] == TERM_HASHTAG) || ($t['ttype'] == TERM_COMMUNITYTAG)) && (($t['term'] === substr($word, 1)) || (substr($word, 1) === '*'))) {
							return false;
						}
					}
				} elseif (substr($word, 0, 1) === '$' && $tags) {
					foreach ($tags as $t) {
						if (($t['ttype'] == TERM_CATEGORY) && (($t['term'] === substr($word, 1)) || (substr($word, 1) === '*'))) {
							return false;
						}
					}
				} elseif (substr($word, 0, 2) === '?+') {
					if (self::test_condition(substr($word, 2), $item['obj'])) {
						return false;
					}
				} elseif (substr($word, 0, 1) === '?') {
					if (self::test_condition(substr($word, 1), $item)) {
						return false;
					}
				} elseif ((strpos($word, '/') === 0) && preg_match($word, $text)) {
					return false;
				} elseif (stristr($text, $word) !== false) {
					return false;
				}
			}
		}

		$include = (($incl) ? explode("\n", $incl) : null);

		if ($include) {
			foreach ($include as $word) {
				$word = html_entity_decode(trim($word));
				if (! $word) {
					continue;
				}
				if (isset($lang) && ((strpos($word, 'lang=') === 0) || (strpos($word, 'lang!=') === 0))) {
					if (!strlen($lang))  {
						// Result is ambiguous. However we are checking allow rules
						// and an ambiguous language is always permitted.
						return true;
					}
					if (strpos($word, 'lang=') === 0 && strcasecmp($lang, trim(substr($word, 5))) == 0) {
						return true;
					} elseif (strpos($word, 'lang!=') === 0 && strcasecmp($lang, trim(substr($word, 6))) != 0) {
						return true;
					}
				}
				elseif (str_starts_with($word, 'until=')) {
					$until = strtotime(trim(substr($word, 6)));
					if ($until < strtotime($item['created'])) {
						return true;
					}
				}
				elseif (substr($word, 0, 1) === '#' && $tags) {
					foreach ($tags as $t) {
						if ((($t['ttype'] == TERM_HASHTAG) || ($t['ttype'] == TERM_COMMUNITYTAG)) && (($t['term'] === substr($word, 1)) || (substr($word, 1) === '*'))) {
							return true;
						}
					}
				} elseif (substr($word, 0, 1) === '$' && $tags) {
					foreach ($tags as $t) {
						if (($t['ttype'] == TERM_CATEGORY) && (($t['term'] === substr($word, 1)) || (substr($word, 1) === '*'))) {
							return true;
						}
					}
				} elseif (substr($word, 0, 2) === '?+') {
					if (self::test_condition(substr($word, 2), $item['obj'])) {
						return true;
					}
				} elseif (substr($word, 0, 1) === '?') {
					if (self::test_condition(substr($word, 1), $item)) {
						return true;
					}
				} elseif ((strpos($word, '/') === 0) && preg_match($word, $text)) {
					return true;
				} elseif (stristr($text, $word) !== false) {
					return true;
				}
			}
		} else {
			return true;
		}

		return false;
	}


	/**
	 * Evaluate a conditional expression with support for AND (&&) and OR (||) operators.
	 *
	 * - ?foo ~= baz which will check if item.foo contains the string 'baz';
	 * - ?foo == baz which will check if item.foo is the string 'baz';
	 * - ?foo != baz which will check if item.foo is not the string 'baz';
	 * - ?foo >= 3 which will check if item.foo is greater than or equal to 3;
	 * - ?foo > 3 which will check if item.foo is greater than 3;
	 * - ?foo <= 3 which will check if item.foo is less than or equal to 3;
	 * - ?foo < 3 which will check if item.foo is less than 3;
	 *
	 * - ?foo {} baz which will check if 'baz' is an array element in item.foo
	 * - ?foo {*} baz which will check if 'baz' is an array key in item.foo
	 * - ?foo which will check for a return of a true condition for item.foo;
     * - ?!foo which will check for a return of a false condition for item.foo;
     *
	 * The values 0, '', an empty array, and an unset value will all evaluate to false.
	 *
	 * @param  string $s    The condition string to evaluate.
	 * @param  array  $item The associative array providing variable values.
	 * @return bool         True if the condition is met, false otherwise.
	 */

	public static function test_condition($s, $item) {
		$s = trim($s);

		// Handle OR (||)
		// Split on '||' not inside quotes
		$or_parts = preg_split('/\s*\|\|\s*/', $s);
		if (count($or_parts) > 1) {
			foreach ($or_parts as $part) {
				if (self::test_condition(ltrim($part, '?+'), $item)) {
					return true;
				}
			}
			return false;
		}

		// Handle AND (&&)
		// Split on '&&' not inside quotes
		$and_parts = preg_split('/\s*\&\&\s*/', $s);
		if (count($and_parts) > 1) {
			foreach ($and_parts as $part) {
				if (!self::test_condition(ltrim($part, '?+'), $item)) {
					return false;
				}
			}
			return true;
		}

		// Basic checks

		// Contains substring (case-insensitive)
		if (preg_match('/(.*?)\s\~\=\s(.*?)$/', $s, $matches)) {
			$x = ((array_key_exists(trim($matches[1]), $item)) ? $item[trim($matches[1])] : EMPTY_STR);
			return (stripos($x, trim($matches[2])) !== false);
		}

		// Equality
		if (preg_match('/(.*?)\s\=\=\s(.*?)$/', $s, $matches)) {
			$x = ((array_key_exists(trim($matches[1]), $item)) ? $item[trim($matches[1])] : EMPTY_STR);
			return ($x == trim($matches[2]));
		}

		// Inequality
		if (preg_match('/(.*?)\s\!\=\s(.*?)$/', $s, $matches)) {
			$x = ((array_key_exists(trim($matches[1]), $item)) ? $item[trim($matches[1])] : EMPTY_STR);
			return ($x != trim($matches[2]));
		}

		// Greater than or equal
		if (preg_match('/(.*?)\s\>\=\s(.*?)$/', $s, $matches)) {
			$x = ((array_key_exists(trim($matches[1]), $item)) ? $item[trim($matches[1])] : EMPTY_STR);
			return ($x >= trim($matches[2]));
		}

		// Less than or equal
		if (preg_match('/(.*?)\s\<\=\s(.*?)$/', $s, $matches)) {
			$x = ((array_key_exists(trim($matches[1]), $item)) ? $item[trim($matches[1])] : EMPTY_STR);
			return ($x <= trim($matches[2]));
		}

		// Greater than
		if (preg_match('/(.*?)\s\>\s(.*?)$/', $s, $matches)) {
			$x = ((array_key_exists(trim($matches[1]), $item)) ? $item[trim($matches[1])] : EMPTY_STR);
			return ($x > trim($matches[2]));
		}

		// Less than
		if (preg_match('/(.*?)\s\<\s(.*?)$/', $s, $matches)) {
			$x = ((array_key_exists(trim($matches[1]), $item)) ? $item[trim($matches[1])] : EMPTY_STR);
			return ($x < trim($matches[2]));
		}

		// Array contains value
		if (preg_match('/(.*?)\s\{\}\s(.*?)$/', $s, $matches)) {
			$x = ((array_key_exists(trim($matches[1]), $item)) ? $item[trim($matches[1])] : EMPTY_STR);
			return (is_array($x) && in_array(trim($matches[2]), $x));
		}

		// Array contains key
		if (preg_match('/(.*?)\s\{\*\}\s(.*?)$/', $s, $matches)) {
			$x = ((array_key_exists(trim($matches[1]), $item)) ? $item[trim($matches[1])] : EMPTY_STR);
			return (is_array($x) && array_key_exists(trim($matches[2]), $x));
		}

		// Ordering of this check (for falsiness) with relation to the following one (check for truthiness) is important.
		// Falsy check
		if (preg_match('/\!(.*?)$/', $s, $matches)) {
			$x = ((array_key_exists(trim($matches[1]), $item)) ? $item[trim($matches[1])] : EMPTY_STR);
			return !$x;
		}

		// Truthy check (default)
		if (preg_match('/(.*?)$/', $s, $matches)) {
			$x = ((array_key_exists(trim($matches[1]), $item)) ? $item[trim($matches[1])] : EMPTY_STR);
			return (bool)$x;
		}

		// If no conditions matched, return false
		return false;
	}


}
