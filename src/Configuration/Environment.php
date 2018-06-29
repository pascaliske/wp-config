<?php
namespace PI\Configuration;

use Composer\Factory;

class Environment {
    /** --- constants --- **/

    /**
     * The root path.
     *
     * @access private
     * @var string
     */
    private $root;

    /**
     * The env.
     *
     * @access private
     * @var array
     */
    private $env;

    /** --- constructor --- **/

    /**
     * Initializes the environment.
     *
     * @access public
     * @return Environment
     */
    public function __construct() {
        $this->root = dirname(Factory::getComposerFile());
        $this->env = $this->fetchEnvironment();
    }

    /** --- private --- **/

    /**
     * Fetches the env.
     *
     * @access protected
     * @return array
     */
    protected function fetchEnvironment($file = 'composer.json') {
        $file = sprintf('%s/%s', $this->root, $file);
        $contents = file_get_contents($file);
        return json_decode($contents, true);
    }

    /** --- public --- **/

    /**
     * Returns the value for the given key.
     *
     * @access public
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function get($key, $default = null) {
        if (strpos($key, ':') != false) {
            $data = $this->env;

            foreach (explode(':', $key) as $key) {
                if (!isset($data[$key])) {
                    break;
                }

                $data = $data[$key];
            }

            return $data ?: $default;
        }

        return $this->env[$key] ?: $default;
    }
}
