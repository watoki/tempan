<?php
namespace watoki\tempan;

class HtmlParser {

    private static $tagPairs = array(
        '<html><body>' => array(),
        '<html><head>' => array(),
        '<body>' => array('<html>', '</html>'),
        '<html>' => array('<body>', '</body>'),
        '' => array('<html><body>', '</body></html>'),
    );

    /**
     * @var \DOMDocument
     */
    private $doc;

    /**
     * @var string
     */
    private $html;

    function __construct($html) {
        $this->html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
    }

    public function isHtmlDocument() {
        return substr(trim($this->html), 0, 5) == '<html';
    }

    /**
     * @return \DOMElement
     */
    public function getRoot() {
        if (!$this->doc) {
            $this->parse();
        }
        return $this->doc->documentElement;
    }

    private function parse() {
        $this->doc = new \DOMDocument();

        $internal_errors = libxml_use_internal_errors(true);
        if (!$this->doc->loadHTML($this->html) || libxml_get_errors()) {
            $errors = json_encode(libxml_get_errors());
            libxml_clear_errors();
            throw new \Exception('Error while parsing mark-up: ' . $errors . ' While parsing: ' . $this->html);
        }
        libxml_use_internal_errors($internal_errors);
    }

    public function toString(\DOMElement $element = null) {
        if (!$this->doc) {
            return $this->html;
        }

        $this->doc->formatOutput = true;
        $content = $this->doc->saveHTML($element ?: $this->doc->documentElement);

        $content = trim($content);

        $input = trim(preg_replace('/>\s+?</', '><', $this->html));
        foreach (self::$tagPairs as $match => $replace) {
            if (substr($input, 0, strlen($match)) == $match) {
                $content = str_replace($replace, '', $content);
                break;
            }
        }

        return $content;
    }

    /**
     * @return \DOMDocument
     */
    public function getDocument() {
        return $this->doc;
    }

}