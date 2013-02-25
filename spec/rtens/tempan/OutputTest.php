<?php
namespace spec\rtens\tempan;

use rtens\collections\Map;
use rtens\tempan\Renderer;

class OutputTest extends Test {

    public function testUtf8Encoding() {
        $html = '<html><body>öäü</body></html>';

        $renderer = new Renderer(array());

        $this->assertEquals($html, $renderer->render($html));
    }

    public function testOutputShouldEqualInput() {
        $html1 = "\n <html>\n  \t<body><div><b>Hello</b></div></body></html>";
        $html1out = "<html><body><div><b>Hello</b></div></body></html>";
        $html2 = '<html><div><b>Hello</b></div></html>';
        $html3 = '<body><div><b>Hello</b></div></body>';
        $html4 = '<div><b>Hello</b></div>';

        $renderer = new Renderer(array());

        $this->assertEquals($html1out, $renderer->render($html1));
        $this->assertEquals($html2, $renderer->render($html2));
        $this->assertEquals($html3, $renderer->render($html3));
        $this->assertEquals($html4, $renderer->render($html4));
    }

}