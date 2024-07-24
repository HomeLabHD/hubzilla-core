<?php

namespace Zotlabs\Lib\Traits;

use CommerceGuys\Intl\Language\LanguageRepository;

trait HelpHelperTrait {

	// PHP versions before 8.2 does not support trait constants,
	// Leave this commented out until we drop support for PHP 8.1.
	//
	// const VALID_FILE_EXT = ['md', 'bb', 'html'];

	private string $file_name = '';
	private string $file_type = '';

	/**
	 * Associative array containing the detected language.
	 */
	private array $lang = [
		'language' => 'en',		//! Detected language, 2-letter ISO 639-1 code ("en")
		'from_url' => false,	//! true if language from URL overrides browser default
	];

	/**
	 * Determines help language.
	 *
	 * If the language was specified in the URL, override the language preference
	 * of the browser. Default to English if both of these are absent.
	 *
	 * Updates the `$lang` property of the module.
	 */
	private function determine_help_language() {

		$language_repository = new LanguageRepository;
		$languages = $language_repository->getList();

		if(array_key_exists(argv(1), $languages)) {
			$lang = argv(1);
			$from_url = true;
		} else {
			$lang = \App::$language;
			if(! isset($lang))
				$lang = 'en';

			$from_url = false;
		}

		$this->lang =  array('language' => $lang, 'from_url' => $from_url);
	}

	/**
	 * Find the full path name of the file, given it's base path and
	 * the language of the request.
	 *
	 * @param string $base_path		The path of the file to find, relative to the
	 *	doc root path, and without the extension.
	 */
	private function find_help_file(string $base_path, string $lang): void {

		// Use local variable until we can use trait constants.
		$valid_file_ext = ['md', 'bb', 'html'];

		$base_path_with_lang = "doc/{$lang}/${base_path}";

		foreach ($valid_file_ext as $ext) {
			$path = "{$base_path_with_lang}.{$ext}";
			if (file_exists($path)) {
				$this->file_name = $path;
				$this->file_type = $ext;

				break;
			}
		}

		if (empty($this->file_name) && $lang !== 'en') {
			$this->find_help_file($base_path, 'en');
		}
	}
}
