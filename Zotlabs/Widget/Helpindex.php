<?php
/**
 * Widget to show the help index.
 *
 * By default used by the left sidebar by the help module.
 *
 *   * Name: Help index
 *   * Description: Help pages index
 */

namespace Zotlabs\Widget;

class Helpindex {

	use \Zotlabs\Lib\Traits\HelpHelperTrait;

	private string $contents = '';

	function widget() {

		$this->determine_help_language();
		$this->find_help_file('toc', $this->lang['language']);
    logger('Helpindex file_name=' . var_export($this->file_name,true));

		$sections = [];
		$this->contents = '';

		if (!empty($this->file_name) && is_readable($this->file_name)) {
			$json = file_get_contents($this->file_name);
			$this->contents = translate_projectname($json);

			$decoded = json_decode($json, true);
			if (is_array($decoded)) {
				$sections = $decoded;
			}
		} else {
			$this->contents = '<em>' . t('No documentation index found.') . '</em>';
		}
    logger('Helpindex file_name=' . $this->contents); 
		$tpl = get_markup_template('help-index.tpl');

		return replace_macros($tpl, [
			'$title'    => t('Documentation Index'),
			'$sections' => $sections,
			'$contents' => $this->contents
		]);
	}

	public function title(): string {
		return '';
	}

	public function contents(): string {
		return $this->contents;
	}
}
