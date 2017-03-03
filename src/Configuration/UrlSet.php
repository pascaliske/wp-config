<?php
namespace PI\Configuration;

class UrlSet {
    /** --- constants --- **/

	/**
	 * Production URLs
	 *
	 * @access private
	 * @var [Array]
	 */
	private $production;

	/**
	 * Staging URLs
	 *
	 * @access private
	 * @var [Array]
	 */
	private $staging;

	/**
	 * Development URLs
	 *
	 * @access private
	 * @var [Array]
	 */
	private $development;

	/** --- constructor --- **/

	/**
	 * Instanciates a new url set with default urls
	 *
	 * @return [void]
	 */
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

	/** --- private --- **/

    /** --- public --- **/

	/**
	 * Sets new urls to an environment
	 *
	 * @access public
	 * @param [String] $env
	 * @param [Mixed] $urls
	 * @return [void]
	 */
	public function set(string $env=null, $urls) {
        $env = $env ?: 'production';

		if (!is_array($urls)) {
			$urls = array($urls);
		}

		// merge with existing values
		$this->environment[$env] = array_merge($this->environment[$env], $urls);
	}

	/**
	 * Returns urls of an environment
	 *
	 * @access public
	 * @param  [String] $key
	 * @return [Array]
	 */
	public function get(string $env=null) {
        $env = $env ?: 'production';

		return $this->environment[$env];
	}
}
