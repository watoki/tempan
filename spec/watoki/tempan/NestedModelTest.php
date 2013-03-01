<?php
namespace spec\watoki\tempan;

class NestedModelTest extends Test {

    public function testNestedProperty() {
        $this->givenTheModel('{"post":{"author":"John", "text":"Hello World"}}');
        $this->whenIRender('<div property="post"><div property="author">Me</div><div property="text">Hi</div></div>');
        $this->thenTheResultShouldBe('<div property="post"><div property="author">John</div><div property="text">Hello World</div></div>');
    }

    public function testLeafInsideLeaf() {
        $this->givenTheModel('{"email":{"href":"mailto:test@example.com"}, "name":"Test"}');
        $this->whenIRender('<a property="email" href=""><span property="name">Name</span></a>');
        $this->thenTheResultShouldBe('<a property="email" href="mailto:test@example.com"><span property="name">Test</span></a>');
    }

    public function testPropertyInsideUndefinedNode() {
        $this->givenTheModel('{"text":"Hello World"}');
        $this->whenIRender('Hello <div property="undefined"><span property="text">World</span></div>');
        $this->thenTheResultShouldBe('Hello <div property="undefined"><span property="text">Hello World</span></div>');
    }

    public function testDeeplyNestedModel() {
        $this->givenTheModel('{
            "post": {
                "text": "Hello World!",
                "author": {
                    "name": "Timmy Tester",
                    "image": {
                        "alt": "Timmys Image",
                        "src": "http://example.com/image.jpg"
                    },
                    "email": {
                        "href": "mailto:timmy@tester.com"
                    }
                }
            }
        }');
        $this->whenIRender('
            <div property="post">
                <div property="author">
                    <img property="image" src="" alt="Nothing">
                    <a property="email" href="mailto:john.doe@example.com">
                        <span property="name">John Doe</span>
                    </a>
                </div>
                <span property="text">Some Text</span>
            </div>');
        $this->thenTheResultShouldBe('
            <div property="post">
                <div property="author">
                    <img property="image" src="http://example.com/image.jpg" alt="Timmys Image">
                    <a property="email" href="mailto:timmy@tester.com">
                        <span property="name">Timmy Tester</span>
                    </a>
                </div>
                <span property="text">Hello World!</span>
            </div>');
    }

    public function testScalarModelWithInnerProperties() {
        $this->givenTheModel('{"nonObject":1}');
        $this->whenIRender('
            <div property="nonObject"><span property="ignore"><span property="me">Nothing</span></span></div>
            <div property="nonObject">One</div>');
        $this->thenTheResultShouldBe('
            <div property="nonObject">1</div>
            <div property="nonObject">1</div>');
    }

    public function testInnerFalseOrNullValues() {
        $this->givenTheModel('{
            "outer": {
                "no": false,
                "empty": null,
                "yes": true
            }
        }');
        $this->whenIRender('
            <div property="outer">
                <span property="no">No</span>
                <span property="empty">Empty</span>
                <span property="yes">Yes</span>
            </div>');
        $this->thenTheResultShouldBe('
            <div property="outer">
                <span property="yes">Yes</span>
            </div>');
    }

    public function testLexicalScope() {
        $this->givenTheModel('{
            "outer": 1,
            "inner": 1,
            "local": 1,
            "list": [
                {
                    "inner": 2,
                    "local": 2,
                    "child": {
                        "local": 3,
                        "own": 4
                    }
                }
            ]
        }');
        $this->whenIRender('
            <div property="outer"></div>
            <div property="inner"></div>
            <div property="local"></div>
            <div property="list">
                <div property="outer"></div>
                <div property="inner"></div>
                <div property="local"></div>
                <div property="child">
                    <div property="outer"></div>
                    <div property="inner"></div>
                    <div property="local"></div>
                    <div property="own"></div>
                </div>
            </div>
        ');
        $this->thenTheResultShouldBe('
            <div property="outer">1</div>
            <div property="inner">1</div>
            <div property="local">1</div>
            <div property="list">
                <div property="outer">1</div>
                <div property="inner">2</div>
                <div property="local">2</div>
                <div property="child">
                    <div property="outer">1</div>
                    <div property="inner">2</div>
                    <div property="local">3</div>
                    <div property="own">4</div>
                </div>
            </div>
        ');
    }

    public function testPropertiesInsideTrueValue() {
        $this->givenTheModel('{"isTrue":true, "message":"Hello World"}');
        $this->whenIRender('<div property="isTrue"><div property="message">Hi</div></div>');
        $this->thenTheResultShouldBe('<div property="isTrue"><div property="message">Hello World</div></div>');
    }
}
