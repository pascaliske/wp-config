<?php
namespace PI\Configuration;

class UrlSet {
    /** --- constants --- **/

    /**
     * Environment URLs.
     *
     * @access private
     * @var array
     */
    private $environment;

    /** --- constructor --- **/

    /**
     * Instanciates a new url set with default urls.
     *
     * @return void
     */
    public function __construct() {}

    /** --- private --- **/

    /** --- public --- **/

    /**
     * Sets new urls to an environment.
     *
     * @access public
     * @param string $env
     * @param mixed $urls
     * @return void
     */
    public function set(string $env = 'production', $urls = null) {
        if (is_null($urls)) {
            return null;
        }

        if (!is_array($urls)) {
            $urls = array($urls);
        }

        if (!isset($this->environment[$env])) {
            $this->environment[$env] = array();
        }

        // merge with existing values
        $this->environment[$env] = array_merge($this->environment[$env], $urls);
    }

    /**
     * Returns urls of an environment.
     *
     * @access public
     * @param  string $key
     * @return array
     */
    public function get(string $env = 'production') {
        if (!isset($this->environment[$env])) {
            return null;
        }

        return $this->environment[$env];
    }
}
