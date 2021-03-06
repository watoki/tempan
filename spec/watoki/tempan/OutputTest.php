<?php
namespace spec\watoki\tempan;

use watoki\collections\Map;
use watoki\tempan\Renderer;

class OutputTest extends Test {

    public function testUtf8Encoding() {
        $html = '<html><body>öäü</body></html>';

        $renderer = new Renderer($html);

        $this->assertEquals($html, $renderer->render());
    }

    public function testOutputShouldEqualInput() {
        $in = array(
            "\n<html><body><div><b>Hello</b></div></body></html>",
            '<html><head></head><body>Hi</body></html>',
            '<html><div><b>Hello</b></div></html>',
            '<body><div><b>Hello</b></div></body>',
            '<div><b>Hello</b></div>'
        );

        $out = array(
            "<html><body><div><b>Hello</b></div></body></html>",
            $in[1],
            $in[2],
            $in[3],
            $in[4]
        );

        foreach ($in as $i => $html) {
            $renderer = new Renderer($html);
            $this->assertEquals($out[$i], str_replace("\n", "", $renderer->render()));
        }
    }

}