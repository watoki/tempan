<?php
namespace spec\watoki\tempan;

class DynamicValuesTest extends Test {

    public function testMethodCall() {
        $this->givenTheClass('class IntegerModel {
            public $value;
            public function __construct($int) {
                $this->value = $int;
            }
            public function isBig() {
                return $this->value > 3;
            }
            public function isNotBig() {
                return !$this->isBig();
            }
        }');

        $this->givenTheClass('class MethodModel {
            public $number;
            public function __construct($number) {
                $this->number = new IntegerModel($number);
            }
        }');

        $this->givenTheModelIsInstanceOfClass('MethodModel', 2);

        $this->whenIRender('
            <div property="number">
                <span property="value">X</span> is a <span property="isBig">BIG</span><span property="isNotBig">small</span> number
            </div>
        ');

        $this->thenTheResultShouldBe('
            <div property="number">
                <span property="value">2</span> is a <span property="isNotBig">small</span> number
            </div>
        ');
    }

    public function testClosureCall() {
        $this->givenTheClass('class StringModel {
            public function __construct() {
                $this->shorten = function ($element, $animator) {
                    return substr($element->getChildren()->first()->getText(), 0, 7);
                };
            }
        }');

        $this->givenTheModelIsInstanceOfClass('StringModel');

        $this->whenIRender('<div property="shorten">Shorten this</div>');

        $this->thenTheResultShouldBe('<div property="shorten">Shorten</div>');
    }

    public function testAnimateBeforeTransformation() {
        $this->givenTheClass('class AnotherModel {
            public $content = "Some long string";
            public function __construct() {
                $this->shorten = function ($element, $animator) {
                    $animator->animateChildren($element);
                    $element->setAttribute("title", $element->getChildren()->first()->getChildren()->first()->getText());
                    return substr($element->getChildren()->first()->getChildren()->first()->getText(), 0, 4);
                };
            }
        }');

        $this->givenTheModelIsInstanceOfClass('AnotherModel');

        $this->whenIRender('<div property="shorten"><span property="content">Shorten this</span></div>');

        $this->thenTheResultShouldBe('<div property="shorten" title="Some long string">Some</div>');
    }

    private function givenTheClass($def) {
        eval($def);
    }

    private function givenTheModelIsInstanceOfClass($classname) {
        $params = func_get_args();
        array_shift($params);

        $refl = new \ReflectionClass($classname);
        $model = $refl->newInstanceArgs($params);
        $this->givenTheModelObject($model);
    }

}