<?php
namespace rtens\tempan;

class Renderer {

    private static $tagPairs = array(
        '<html><body>' => array(),
        '<body>' => array('<html>', '</html>'),
        '<html>' => array('<body>', '</body>'),
        '' => array('<html><body>', '</body></html>'),
    );

    private $log;

    private $logginEnabled;

    private $model;

    function __construct($model) {
        $this->model = $model;
    }

    public function render($template) {
        $root = $this->stringToElement($template);
        $animator = new Animator($this->model);
        $animator->enableLogging($this->logginEnabled);
        $animator->animate(new Element($root));

        if ($this->logginEnabled) {
            $this->log = $animator->getLog();
        }

        return $this->elementToString($root, $template);
    }

    private function stringToElement($html) {
        $doc = new \DOMDocument();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        if (!$doc->loadHTML($html)) {
            throw new \Exception('Error while parsing mark-up.');
        }

        return $doc->documentElement;
    }

    public function elementToString(\DOMElement $element, $template) {
        $doc = $element->ownerDocument;
        $doc->formatOutput = true;
        $content = $doc->saveHTML($element);

        $input = trim(preg_replace('/>\s+?</', '><', $template));
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