<?php
namespace spec\watoki\tempan;

use watoki\tempan\Renderer;

abstract class Test extends \PHPUnit_Framework_TestCase {

    /** @var string */
    private $rendered;

    private $model;

    protected function background() {
    }

    protected function setUp() {
        parent::setUp();

        $this->background();
    }

    protected function givenTheModel($json) {
        $this->givenTheModelObject(json_decode($json, true));
        $this->givenTheModelObject(json_decode($json));
    }

    protected function givenTheModelObject($model) {
        $this->model = $model;
    }

    protected function whenIRender($markup) {
        $renderer = new Renderer("<div>$markup</div>");
        $rendered = $renderer->render($this->model);
        $this->rendered = substr($rendered, 5, -6);
    }

    protected function thenTheResultShouldBe($expected) {
        $this->assertEquals($this->clean($expected), $this->clean($this->rendered));
    }

    protected function clean($string) {
        return trim(preg_replace('/\s*(\S.+\S)\s*/', '$1', $string));
    }
}
