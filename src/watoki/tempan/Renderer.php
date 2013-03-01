<?php
namespace watoki\tempan;

class Renderer {

    private static $tagPairs = array(
        '<html><body>' => array(),
        '<body>' => array('<html>', '</html>'),
        '<html>' => array('<body>', '</body>'),
        '' => array('<html><body>', '</body></html>'),
    );

    /**
     * @var string
     */
    private $template;

    /**
     * @var \DOMElement
     */
    private $root;

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
        $this->template = mb_convert_encoding($template, 'HTML-ENTITIES', 'UTF-8');
    }

    public function getRoot() {
        if (!$this->rootElement) {
            $this->root = $this->createDocument();
            $this->rootElement = new Element($this->root);
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

        return $this->__toString();
    }

    private function createDocument() {
        $doc = new \DOMDocument();

        if (!$doc->loadHTML($this->template)) {
            throw new \Exception('Error while parsing mark-up.');
        }

        return $doc->documentElement;
    }

    public function __toString() {
        $this->getRoot();
        $doc = $this->root->ownerDocument;
        $doc->formatOutput = true;
        $content = $doc->saveHTML($this->root);

        $input = trim(preg_replace('/>\s+?</', '><', $this->template));
        foreach (self::$tagPairs as $match => $replace) {
            if (substr($input, 0, strlen($match)) == $match) {
                $content = str_replace($replace, '', $content);
                break;
            }
        }

        return $content;
    }

    public function enableLogging($enable = true) {
        $this->logginEnabled = $enable;
    }

    public function getLog() {
        return $this->log;
    }

}