<?php
namespace PI\Configuration;

class Environment {
    /** --- constants --- **/

    /**
     * The root path.
     *
     * @access private
     * @var string
     */
    private $rootPath;

    /**
     * The package.
     *
     * @access private
     * @var array
     */
    private $package;

    /** --- constructor --- **/

    /**
     * Initializes the environment.
     *
     * @access public
     * @param string $rootPath
     * @return Environment
     */
    public function __construct($rootPath = '') {
        $this->rootPath = $rootPath;
        $this->package = $this->fetchPackage();
    }

    /** --- private --- **/

    /**
     * Fetches the package.
     *
     * @access protected
     * @return array
     */
    protected function fetchPackage() {
        $file = sprintf('%s/package.json', $this->rootPath);
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
            $data = $this->package;

            foreach (explode(':', $key) as $key) {
                if (!isset($data[$key])) {
                    break;
                }

                $data = $data[$key];
            }

            return $data ?: $default;
        }

        return $this->package[$key] ?: $default;
    }
}
