<?php

namespace Zotlabs\Web;

/**
 * Base controller class for Modules.
 *
 * Modules should extend this class, and override the methods needed. Emtpy
 * default implementations allow Modules to only override the methods they
 * need.
 *
 * The methods defined by this class is invoked in order:
 *
 *   - init()
 *   - post() -- only for POST requests
 *   - get()
 *
 * Modules which emit other serialisations besides HTML (XML,JSON, etc.) should
 * do so within the module `init` and/or `post` functions and then invoke
 * `killme()` to terminate further processing.
 */
abstract class Controller {

	/**
	 * Initialize request processing.
	 *
	 * This method is called before any other request processing, and
	 * regardless of the request method. The theme is not yet loaded when
	 * this method is invoked.
	 */
	public function init() {
	}

	/**
	 * Process POST requests.
	 *
	 * This method is called if the incoming request is a POST request. It is
	 * invoked after the theme has been loaded. This method should not normally
	 * render HTML output, as processing will fall through to the GET processing
	 * if this method completes without error or stopping processing in other
	 * ways.
	 */
	public function post() {
	}

	/**
	 * Process GET requests or the body part of POST requests.
	 *
	 * This method is called directly for GET requests, and immediately after the
	 * `post()` method for POST requests.
	 *
	 * It will return the module content as a HTML string.
	 *
	 * @return string HTML content for the module.
	 */
	public function get() {
		return '';
	}
}
