<?php
namespace spec\watoki\tempan;

use watoki\tempan\model\ListModel;
use watoki\tempan\model\QuantityModel;

class ModelTest extends Test {

    public function testQuantityModel() {
        $this->givenTheModelObject(array(
                'first' => new QuantityModel(17),
                'then' => new QuantityModel(1),
        ));

        $this->whenIRender('
            <div property="first">
                I have <span property="value">42</span> banana<span property="isNotOne">s</span>
            </div>
            <div property="then">
                Now I have <span property="value">1</span> banana<span property="isNotOne">s</span>
            </div>
        ');

        $this->thenTheResultShouldBe('
            <div property="first">
                I have <span property="value">17</span> banana<span property="isNotOne">s</span>
            </div>
            <div property="then">
                Now I have <span property="value">1</span> banana
            </div>
        ');
    }

    public function testListModel() {
        $this->givenTheModelObject(array(
                'myList' => new ListModel(array('one', 'two', 'three', 'four', 'five'))
        ));

        $this->whenIRender('
        <div property="myList">
            <div property="isEmpty">Sorry, no numbers today</div>
            <div property="size">
                There <span property="isNotOne">are</span><span property="isOne">is</span> <span property="value">12</span> number<span property="isNotOne">s</span> today
            </div>
            <div property="chunk" data-size="2">
                <div property="size">
                    <span property="value">7</span> in this chunk
                </div>
                <span property="item">The Number</span>
            </div>
        </div>');
        $this->thenTheResultShouldBe('
        <div property="myList">
            <div property="size">There <span property="isNotOne">are</span> <span property="value">5</span> number<span property="isNotOne">s</span> today</div>
            <div property="chunk" data-size="2">
                <div property="size">
                    <span property="value">2</span> in this chunk
                </div>
                <span property="item">one</span>
                <span property="item">two</span>
            </div>
            <div property="chunk" data-size="2">
                <div property="size">
                    <span property="value">2</span> in this chunk
                </div>
                <span property="item">three</span>
                <span property="item">four</span>
            </div>
            <div property="chunk" data-size="2">
                <div property="size">
                    <span property="value">1</span> in this chunk
                </div>
                <span property="item">five</span>
            </div>
        </div>');
    }

} 