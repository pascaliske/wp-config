<?php
namespace PI\Configuration;

class UrlSet {
	private $production;
	private $staging;
	private $development;

	public function __construct() {
		$this->environment['production'] = array();
		$this->environment['staging'] = array(
			'staging.pascal-iske.de'
		);
		$this->environment['development'] = array(
			'localhost:5000',
			'cli'
		);
	}

	public function set($key='production', $urls) {
		if (!is_array($urls)) {
			$urls = array($urls);
		}

		$this->environment[$key] = array_merge($this->environment[$key], $urls);
	}

	public function get($key='production') {
		return $this->environment[$key];
	}
}
