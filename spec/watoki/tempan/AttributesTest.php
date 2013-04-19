<?php
namespace spec\watoki\tempan;
 
class AttributesTest extends Test {

    public function testFalseAndNullLeafAttributesShouldBeRemoved() {
        $this->givenTheModel('{"name": {"title":false, "class":null}}');
        $this->whenIRender('<div property="name" title="test" class="nothing"></div>');
        $this->thenTheResultShouldBe('<div property="name"></div>');
    }

    public function testLeafAttributesShouldBeReplaced() {
        $this->givenTheModel('{"image": {"src":"http://example.com", "alt":"Test"}}');
        $this->whenIRender('<img property="image" src="" alt="nothing"/>');
        $this->thenTheResultShouldBe('<img property="image" src="http://example.com" alt="Test"/>');
    }

    public function testChildWithNameOfAttribute() {
        $this->givenTheModel('{"item": {"title": "Hello"}}');
        $this->whenIRender('<span property="item" title=""><span property="title"></span></span>');
        $this->thenTheResultShouldBe('<span property="item" title="Hello"><span property="title">Hello</span></span>');
    }

    public function testAttributeScope() {
        $this->givenTheModel('{"title": "None", "message": {"text": "Hello World"} }');
        $this->whenIRender('<span property="message" title="Dont change me"><span property="text">Hi</span></span>');
        $this->thenTheResultShouldBe('<span property="message" title="Dont change me"><span property="text">Hello World</span></span>');
    }

}
