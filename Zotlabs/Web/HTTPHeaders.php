<?php

namespace Zotlabs\Web;

class HTTPHeaders {

	private $in_progress = [];
	private $parsed = [];

	public function __construct($headers) {

		if (!$headers) {
			return;
		}

		$lines = explode("\n", str_replace("\r", '', $headers));

		foreach($lines as $line) {

			$line = rtrim($line);

			// Dismiss empty and status lines
			if(!$line || preg_match('/^HTTP\/\d+(?:\.\d+)?\s+\d{3}(?:\s|$)/i', $line)) {
				continue;
			}

			// Folded header
			if(preg_match('/^[ \t]+/', $line)) {
				if(isset($this->in_progress['k'])) {
					$this->in_progress['v'] .= ' ' . ltrim($line);
				}
				continue;
			}

			if(isset($this->in_progress['k'])) {
				$this->parsed[] = [
					$this->in_progress['k'] => $this->in_progress['v']
				];
				$this->in_progress = [];
			}

			$pos = strpos($line, ':');

			if($pos === false || $pos === 0) {
				continue;
			}

			$this->in_progress['k'] = strtolower(substr($line, 0, $pos));
			$this->in_progress['v'] = ltrim(substr($line, $pos + 1));
		}

		if (isset($this->in_progress['k'])) {
			$this->parsed[] = [
				$this->in_progress['k'] => $this->in_progress['v']
			];
			$this->in_progress = [];
		}
	}

	public function fetch() {
		return $this->parsed;
	}

	public function fetcharr() {

		$ret = [];

		foreach($this->parsed as $x) {
			foreach($x as $y => $z) {
				$ret[$y] = $z;
			}
		}

		return $ret;
	}
}
