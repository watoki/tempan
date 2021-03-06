<?php
namespace spec\watoki\tempan;

use watoki\dom\Element;

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

    public function testValueFromClosure() {
        $this->givenTheClass('class StringModel {
            public function __construct() {
                $this->shorten = function ($element) {
                    return substr($element->getChildren()->first()->getText(), 0, 7);
                };
            }
        }');

        $this->givenTheModelIsInstanceOfClass('StringModel');

        $this->whenIRender('<div property="shorten">Shorten this</div>');

        $this->thenTheResultShouldBe('<div property="shorten">Shorten</div>');
    }

    public function testValueFromClosureInArray() {
        $this->givenTheModelObject(array('test' => function (Element $element) {
                    $element->setAttribute('class', 'this');
                    return 'Test';
                }));

        $this->whenIRender('<div property="test">Replace this</div>');

        $this->thenTheResultShouldBe('<div property="test" class="this">Test</div>');
    }

    public function testDoNotMistakeFunctionNamesForCallable() {
        $this->givenTheModelObject(array('notCallable' => 'date'));
        $this->whenIRender('<div property="notCallable"></div>');
        $this->thenTheResultShouldBe('<div property="notCallable">date</div>');

        $this->givenTheClass('class SomeModel {
            public function __construct() {
                $this->neither = "date";
            }
        }');
        $this->givenTheModelIsInstanceOfClass('SomeModel');
        $this->whenIRender('<div property="neither"></div>');
        $this->thenTheResultShouldBe('<div property="neither">date</div>');
    }

    public function testPassArguments() {
        $this->givenTheModelObject(array(
                'say' => function (Element $element) {
                            return $element->getAttribute('data-what')->getValue();
                        }
        ));

        $this->whenIRender('<div property="say" data-what="Hello World">Say it</div>');
        $this->thenTheResultShouldBe('<div property="say" data-what="Hello World">Hello World</div>');
    }

    public function testFavorMethods() {
        $this->givenTheClass('class PrivateAttribute {
            private $value;
            public function __construct($int) {
                $this->value = $int;
            }
            public function value() {
                return $this->value . "!";
            }
        }');

        $this->givenTheModelIsInstanceOfClass('PrivateAttribute', 'foo');

        $this->whenIRender('<div property="value"></div>');
        $this->thenTheResultShouldBe('<div property="value">foo!</div>');
    }

    ###############################################################################################################

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