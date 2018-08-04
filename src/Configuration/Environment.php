<?php
namespace PI\Configuration;

use ComposerLocator;

class Environment {
    /** --- constants --- **/

    /**
     * The env variables.
     *
     * @access private
     * @var array
     */
    private $argv;

    /**
     * The root path.
     *
     * @access private
     * @var string
     */
    private $root;

    /**
     * The urls.
     *
     * @access private
     * @var UrlSet
     */
    private $urls;

    /**
     * The version string. (fetched from composer.json)
     *
     * @access public
     * @var string
     */
    public $version;

    /**
     * The resolved hostname.
     *
     * @access public
     * @var string
     */
    public $hostname;

    /**
     * The resolved environment name.
     *
     * @access public
     * @var string
     */
    public $name;

    /** --- constructor --- **/

    /**
     * Initializes the environment.
     *
     * @access public
     * @return Environment
     */
    public function __construct(UrlSet $urls) {
        global $argv;

        $this->argv = $argv ?: array();
        $this->root = ComposerLocator::getRootPath();
        $this->urls = $urls;

        $this->resolveMetadata();
        $this->resolveHostname();
        $this->resolveEnvironment();
    }

    /** --- private --- **/

    /**
     * Resolves some metadata from project file.
     *
     * @access private
     * @param  string $file
     * @return void
     */
    private function resolveMetadata(string $file = 'composer.json') {
        $file = sprintf('%s/%s', $this->root, $file);
        $contents = json_decode(file_get_contents($file));

        $this->version = $contents->version;
    }

    /**
     * Resolves the hostname.
     *
     * @access private
     * @return void
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

        // ignore ports on urls
        if (strpos($hostname, ':') !== false) {
            $hostname = substr($hostname, 0, strpos($hostname, ':'));
        }

        $this->hostname = $hostname;
    }

    /**
     * Resolves the environment.
     *
     * @access private
     * @return void
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

        $this->name = $environment;
    }

    /** --- public --- **/

    /**
     * Returns the value for the given key.
     *
     * @access public
     * @param  string $environment
     * @return Configuration
     */
    public function config(string $environment = null) {
        if (!is_null($environment)) {
            return new Configuration($this->root, $environment);
        }

        return new Configuration($this->root, $this->name);
    }
}
