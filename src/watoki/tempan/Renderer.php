<?php
namespace watoki\tempan;

use watoki\dom\Element;
use watoki\dom\Parser;
use watoki\dom\Printer;

class Renderer {

    static $CLASS = __CLASS__;

    /**
     * @var Parser
     */
    public $parser;

    /**
     * @var Element
     */
    private $root;

    /**
     * @var array
     */
    private $log;

    /**
     * @var boolean
     */
    private $logginEnabled;

    function __construct($template) {
        $this->parser = new Parser($template);
    }

    /**
     * @param array|object $model
     * @return string
     */
    public function render($model = array()) {
        $animator = new Animator($model);
        $animator->enableLogging($this->logginEnabled);
        foreach ($this->parser->getNodes() as $node) {
            if ($node instanceof Element) {
                $animator->animate($node);
            }
        }

        if ($this->logginEnabled) {
            $this->log = $animator->getLog();
        }

        $printer = new Printer();
        return $printer->printNodes($this->parser->getNodes());
    }

    public function enableLogging($enable = true) {
        $this->logginEnabled = $enable;
    }

    public function getLog() {
        return $this->log;
    }

}