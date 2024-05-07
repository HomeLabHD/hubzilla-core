<?php
namespace Zotlabs\Module;

use Michelf\MarkdownExtra;

/**
 * You can create local site resources in doc/Site.md and either link to doc/Home.md for the standard resources
 * or use our include mechanism to include it on your local page.
 *@code
 * #include doc/Home.md;
 *@endcode
 *
 * The syntax is somewhat strict.
 */
class Help extends \Zotlabs\Web\Controller {

	use \Zotlabs\Lib\Traits\HelpHelperTrait;

	private string $heading_slug = '';

	/**
	 * Associative array containing the detected language.
	 */
	public array $lang = [
		'language' => 'en',		//! Detected language, 2-letter ISO 639-1 code ("en")
		'from_url' => false,	//! true if language from URL overrides browser default
	];

	/**
	 * Pre-check before processing request.
	 *
	 * Determine language requested, and ensure that a topic was requested.
	 * If no topic was requested, redirect to the about page, and abort
	 * processing.
	 */
	public function init() {
		$this->determine_help_language();

		if (argc() === 1) {
			goaway("/help/{$this->lang['language']}/about/about");
			killme();
		}
	}

	/**
	 * Process get request for the help module.
	 *
	 * Loads the correct help file from the `doc/` directory, and passes it to
	 * the help template in `view/tpl/help.tpl`.
	 *
	 * If the requested help topic does not exist for the currently selected
	 * language, a 404 status is returned instead.
	 *
	 * This function currently also handles search and serving static assets
	 * that may be used by the help files.
	 *
	 * @return string	The rendered help page or a 404 page if help topic was
	 *	not found.
	 */
	public function get() {
		nav_set_selected('Help');

		$o = '';

		if(isset($_REQUEST['search']) && $_REQUEST['search']) {
			$o .= '<div id="help-content" class="generic-content-wrapper">';
			$o .= '<div class="section-title-wrapper">';
			$o .= '<h2>' . t('Documentation Search') . ' - ' . htmlspecialchars($_REQUEST['search']) . '</h2>';
			$o .= '</div>';
			$o .= '<div class="section-content-wrapper">';

			$r = search_doc_files($_REQUEST['search']);
			if($r) {
				$o .= '<ul class="help-searchlist">';
				foreach($r as $rr) {
					$dirname = dirname($rr['v']);
					$fname = basename($rr['v']);
					$fname = substr($fname, 0, strrpos($fname, '.'));
					$path = trim(substr($dirname, 4), '/');

					$o .= '<li><a href="help/' . (($path) ? $path . '/' : '') . $fname . '" >' . ucwords(str_replace('_',' ',notags($fname))) . '</a><br>'
						. '<b><i>' . 'help/' . (($path) ? $path . '/' : '') . $fname . '</i></b><br>'
						. '...' . str_replace('$Projectname', \Zotlabs\Lib\System::get_platform_name(), $rr['text']) . '...<br><br></li>';
				}
				$o .= '</ul>';
				$o .= '</div>';
				$o .= '</div>';
			}

			return $o;
		}


		if(argc() > 2 && argv(argc()-2) === 'assets') {
			$path = '';
			for($x = 1; $x < argc(); $x ++) {
				if(strlen($path))
					$path .= '/';
				$path .= argv($x);
			}
			$realpath = 'doc/' . $path;
			 //Set the content-type header as appropriate
			$imageInfo = getimagesize($realpath);
			switch ($imageInfo[2]) {
				case IMAGETYPE_JPEG:
					header("Content-Type: image/jpeg");
					break;
				case IMAGETYPE_GIF:
					header("Content-Type: image/gif");
					break;
				case IMAGETYPE_PNG:
					header("Content-Type: image/png");
					break;
				case IMAGETYPE_WEBP:
					header("Content-Type: image/webp");
					break;
				default:
					break;
			}
			header("Content-Length: " . filesize($realpath));

			// dump the picture and stop the script
			readfile($realpath);
			killme();
		}

		//
		// The args to the module will be along this pattern:
		//
		//     help/<lang>/<subdir..>/<topic>
		//
		// Where `<lang>` is the language which we want to fetch the topic. This
		// element is optional, but will be used to override the browser language
		// preference if it exists.
		//
		// There may be zero or more `<subdir...>` elements. If there are any
		// present, the first subdir will be used as the slug to find the
		// heading of the help page.
		//
		// The `<topic>` should be the name of a file within the given language
		// and subdirectory tree under the `doc/` directory of the site file
		// system. The topic is given _without_ the file extension, which will be
		// determined by the module.
		//
		// The valid file extensions for help topic are:
		//
		//   - `.md` for markdown formatted source files.
		//   - `.bb` for bbcode formatted source files.
		//   - `.html` for help topics in html format.
		//

		// Strip away the module name from the args
		$args = array_slice(\App::$argv, 1);

		// Remove language if necessary
		//
		// The language was determined during pre-request processing in the
		// `init` function.
		if ($this->lang['from_url']) {
			array_shift($args);
		}

		// Keep the first remaining arg as the heading slug
		$this->heading_slug = $args[0];

		// Locate the file for the topic in the doc directory
		$this->find_help_file(implode('/', $args), $this->lang['language']);

		$this->set_page_title();

		if (empty($this->file_name)) {
			header($_SERVER["SERVER_PROTOCOL"] . ' 404 ' . t('Not Found'));
			$tpl = get_markup_template("404.tpl");
			return replace_macros($tpl, array(
				'$message' => t('Page not found.')
			));

		} else {
			$tpl = get_markup_template('help.tpl');
			return replace_macros($tpl, [ '$module' => $this ]);
		}
	}

	public function render_content(): string {
		return $this->render_help_file($this->file_name, $this->file_type);
	}

	public function render_help_file(string $file_name, string $file_type): string {
		$raw_text = file_get_contents($file_name);

		switch ($file_type) {
		case 'md':
			// We need to escape the `#include` statements in the original file,
			// to be sure it's not rendered as a heading by markdown.
			$raw_text = preg_replace('/#include/ism', '%%include', $raw_text);
			$content = MarkdownExtra::defaultTransform($raw_text);
			$content = preg_replace('/%%include/ism', '#include', $content);
			break;

		case 'bb':
			$content = zidify_links(bbcode($raw_text));
			break;

		case 'html':
			$content = parseIdentityAwareHTML($raw_text);
			break;
		}

		// Replace includes with the contents of the included file
		$content = preg_replace_callback(
			"/#include (.*?)\;/ism",
			function ($matches) {
				$sub_file_type = array_pop(explode('.', $matches[1]));
				$included_content = $this->render_help_file($matches[1], $sub_file_type);
				return str_replace($matches[0], $included_content, $matches[0]);
			},
			$content
		);

		return translate_projectname($content);
	}

	public function get_page_title(): string {
		$title = t('$Projectname Documentation');
		$heading = $this->get_heading();

		if (! empty($heading)) {
			$title .= ': ' . $heading;
		}

		return $title;
	}

	public function get_toc_heading(): string {
		return t('Contents');
	}


	private function get_heading(): string {
		$headings = [
			'about'     => t('About'),
			'member'    => t('Members'),
			'admin'     => t('Administrators'),
			'developer' => t('Developers'),
			'tutorials' => t('Tutorials')
		];

		if(array_key_exists($this->heading_slug, $headings)) {
			return $headings[$this->heading_slug];
		} else {
			return '';
		}
	}

	/**
	 * Set the page title to an unslugified version of the file name.
	 *
	 * @Note This modifies the global `App::$page['title']` property.
	 */
	private function set_page_title(): void {
		$title = basename($this->file_name, ".{$this->file_type}");
		\App::$page['title'] =
			t('Help:') . ' '
			. ucwords(str_replace(['-', '_'],' ',notags($title)));
	}
}
