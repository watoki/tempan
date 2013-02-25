<?php
namespace spec\rtens\tempan;

class FlatModelTest extends Test {

    public function testUndefinedLeaf() {
        $this->givenTheModel('{}');
        $this->whenIRender('Hello<span property="undefined">World</span>');
        $this->thenTheResultShouldBe('Hello');
    }

    public function testFalseAndNullValues() {
        $this->givenTheModel('{"no":false, "notThere":null}');

        $this->whenIRender('Hello<span property="no"> All</span><span property="notThere"> World</span>');
        $this->thenTheResultShouldBe('Hello');
    }

    public function testTrueValue() {
        $this->givenTheModel('{"yes":true}');
        $this->whenIRender('<span property="yes">Hello World</span>');
        $this->thenTheResultShouldBe('<span property="yes">Hello World</span>');
    }

    public function testContentValue() {
        $this->givenTheModel('{"greetings":"Hello", "name":"World"}');

        $this->whenIRender('
            <span property="greetings">Hey</span>
            <span property="name">You</span>');
        $this->thenTheResultShouldBe('
            <span property="greetings">Hello</span>
            <span property="name">World</span>');
    }

    public function testZeroLeafValue() {
        $this->givenTheModel('{"zero":0}');

        $this->whenIRender('This is <span property="zero">zero</span>');
        $this->thenTheResultShouldBe('This is <span property="zero">0</span>');
    }

    public function testNestedElementStructure() {
        $this->givenTheModel('{"message":"Hello World"}');
        $this->whenIRender('<div><h1><a property="message">My Message</a></h1></div>');
        $this->thenTheResultShouldBe('<div><h1><a property="message">Hello World</a></h1></div>');
    }

}
