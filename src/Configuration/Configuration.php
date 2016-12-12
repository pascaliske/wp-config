<?php
namespace PI\Configuration;

use Symfony\Component\Yaml\Yaml;

class Configuration {
	private $environment;
	private $hostname;
	private $argv;
	private $options;

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

	private function resolveHostname() {
		$hostname = $_SERVER['HTTP_HOST'] ?: '';

		// fetch hostname
		if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && !empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
			$hostname = $_SERVER['HTTP_X_FORWARDED_HOST'];
		}

		// are we in a cli?
		if ((PHP_SAPI == 'cli' || php_sapi_name() == 'cli')) {
			$hostname = 'cli';
		}

		$this->hostname = $hostname;
	}

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
	}

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
