<?php
namespace PI\Configuration;

use Symfony\Component\Yaml\Yaml;

class Configuration {
    /** --- constants --- **/

    /**
     * The root path.
     *
     * @access private
     * @var string
     */
    private $root;

    /**
     * The environment.
     *
     * @access private
     * @var string
     */
    private $environment;

    /**
     * The values.
     *
     * @access private
     * @var array
     */
    private $values;

    /** --- constructor --- **/

    /**
     * Instanciates configuration helper.
     *
     * @access public
     * @param UrlSet $urls
     * @return void
     */
    public function __construct($root, $environment = 'production') {
        $this->root = $root;
        $this->environment = $environment;
        $this->values = array();

        $this->resolveConfig();
    }

    /** --- private --- **/

    /**
     * Resolves all config files.
     *
     * @access private
     * @return void
     */
    private function resolveConfig() {
        $data = array();
        $files = sprintf('%s/conf/%s/*.yml', $this->root, $this->environment);

        foreach (glob($files) as $file) {
            $filename = explode('/', $file);
            $filename = str_replace('.yml', '', end($filename));
            $data[$filename] = Yaml::parse(file_get_contents($file));
        }

        $this->values = array_merge($this->values, $data);
    }

    /** --- public --- **/

    /**
     * Returns the value for the given key path.
     *
     * @access public
     * @param  string $path
     * @param  mixed $default
     * @return mixed
     */
    public function get($path, $default = null) {
        if (strpos($path, ':') != false) {
            $values = $this->values;

            foreach (explode(':', $path) as $key) {
                if (!isset($values[$key])) {
                    break;
                }

                $values = $values[$key];
            }

            return $values ?: $default;
        }

        return $this->values[$path] ?: $default;
    }
}
