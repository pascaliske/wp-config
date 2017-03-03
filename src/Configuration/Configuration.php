<?php
namespace PI\Configuration;

use Symfony\Component\Yaml\Yaml;

class Configuration {
	/** --- constants --- **/

	/**
	 * The env variables
	 *
	 * @access private
	 * @var [Array]
	 */
	private $argv;

	/**
	 * The root path
	 *
	 * @access private
	 * @var [String]
	 */
	private $rootPath;

	/**
	 * The urls
	 *
	 * @access private
	 * @var [UrlSet]
	 */
	private $urls;

	/**
	 * The options
	 *
	 * @access private
	 * @var [Array]
	 */
	private $options;

	/**
	 * The resolved hostname
	 *
	 * @access private
	 * @var [String]
	 */
	private $hostname;

	/**
	 * The resolved environment
	 *
	 * @access private
	 * @var [String]
	 */
	private $environment;

    /** --- constructor --- **/

	/**
	 * Instanciates configuration helper
	 *
	 * @access public
	 * @param [String] $rootPath
	 * @param [UrlSet] $urls
	 * @return [void]
	 */
	public function __construct($rootPath, UrlSet $urls) {
		global $argv;

		$this->argv = $argv ?: array();
		$this->rootPath = $rootPath;
		$this->urls = $urls;

		$this->options = array();
		$this->resolveHostname();
		$this->resolveEnvironment();

		$this->resolveConfig();
	}

    /** --- private --- **/

	/**
	 * Resolves the hostname
	 *
	 * @access private
	 * @return [void]
	 */
	private function resolveHostname() {
		$hostname = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';

		// fetch hostname
		if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && !empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
			$hostname = $_SERVER['HTTP_X_FORWARDED_HOST'];
		}

		// are we in a cli?
		if ((PHP_SAPI == 'cli' || php_sapi_name() == 'cli')) {
			$hostname = 'cli';
		}

		$this->hostname = $hostname;
		$this->options['hostname'] = $hostname;
	}

	/**
	 * Resolves the environment
	 *
	 * @access private
	 * @return [void]
	 */
	private function resolveEnvironment() {
		$environment = 'production';

		// try env var
		if (getenv('WP_ENV') !== false) {
			$environment = preg_replace('/[^a-z]/', '', getenv('WP_ENV'));
		}

		// try env from hostnames
		foreach (array('production', 'staging', 'development') as $env) {
			if (in_array($this->hostname, $this->urls->get($env))) {
				$environment = $env;
			}
		}

		// try cli arguments
		if ((PHP_SAPI == 'cli' || php_sapi_name() == 'cli')) {
			foreach ($this->argv as $arg) {
				if (preg_match('/--env=(.+)/', $arg, $match)) {
					$environment = $match[1];
				}
			}
		}

		$this->environment = $environment;
		$this->options['environment'] = $environment;
	}

	/**
	 * Resolves all config files
	 *
	 * @access private
	 * @return [void]
	 */
	private function resolveConfig() {
		$data = array();
		$files = sprintf('%s/conf/%s/*.yml', $this->rootPath, $this->environment);

		foreach (glob($files) as $file) {
			$filename = explode('/', $file);
			$filename = str_replace('.yml', '', end($filename));
			$data[$filename] = Yaml::parse(file_get_contents($file));
		}

		$this->options = array_merge($this->options, $data);
	}

    /** --- public --- **/

	/**
	 * Returns the value for the given key
	 *
	 * @access public
	 * @param  [String] $option
	 * @param  [Mixed] $default
	 * @return [Mixed]
	 */
	public function get($option, $default=null) {
		if (strpos($option, ':') != false) {
			$options = $this->options;

			foreach (explode(':', $option) as $key) {
				if (!isset($options[$key])) {
					break;
				}

				$options = $options[$key];
			}

			return $options ?: $default;
		}

		return $this->options[$option] ?: $default;
	}
}
