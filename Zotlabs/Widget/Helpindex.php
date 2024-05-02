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

	use \Zotlabs\Lib\Traits\HelpHelper;

	private string $contents = '';

	function widget() {

		$this->determine_help_language();
		$this->find_help_file('toc', $this->lang['language']);

		if (! empty($this->file_name)) {
			$this->contents = file_get_contents($this->file_name);
		}

		$tpl = get_markup_template('widget.tpl');
		return replace_macros($tpl, [ '$widget' => $this ]);
	}

	public function title(): string {
		return '';
	}

	public function contents(): string {
		return $this->contents;
	}
}
