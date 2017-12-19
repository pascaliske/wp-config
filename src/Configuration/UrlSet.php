<?php
namespace PI\Configuration;

class UrlSet {
    /** --- constants --- **/

    /**
     * Production URLs.
     *
     * @access private
     * @var array
     */
    private $production;

    /**
     * Staging URLs.
     *
     * @access private
     * @var array
     */
    private $staging;

    /**
     * Development URLs.
     *
     * @access private
     * @var array
     */
    private $development;

    /** --- constructor --- **/

    /**
     * Instanciates a new url set with default urls.
     *
     * @return void
     */
    public function __construct() {
        $this->environment['production'] = array();
        $this->environment['staging'] = array();
        $this->environment['development'] = array();
    }

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
    public function set(string $env = null, $urls = null) {
        $env = $env ?: 'production';

        if (!is_array($urls)) {
            $urls = array($urls);
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
    public function get(string $env = null) {
        $env = $env ?: 'production';

        return $this->environment[$env];
    }
}
