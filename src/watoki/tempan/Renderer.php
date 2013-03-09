<?php
namespace watoki\tempan;

class Renderer {

    /**
     * @var HtmlParser
     */
    public $parser;

    /**
     * @var Element
     */
    private $rootElement;

    /**
     * @var array
     */
    private $log;

    /**
     * @var boolean
     */
    private $logginEnabled;

    function __construct($template) {
        $this->parser = new HtmlParser($template);
    }

    public function getRoot() {
        if (!$this->rootElement) {
            $this->rootElement = new Element($this->parser->getRoot());
        }

        return $this->rootElement;
    }

    /**
     * @param array|object $model
     * @return string
     */
    public function render($model = array()) {
        $animator = new Animator($model);
        $animator->enableLogging($this->logginEnabled);
        $animator->animate($this->getRoot());

        if ($this->logginEnabled) {
            $this->log = $animator->getLog();
        }

        return $this->parser->toString();
    }

    public function enableLogging($enable = true) {
        $this->logginEnabled = $enable;
    }

    public function getLog() {
        return $this->log;
    }

}