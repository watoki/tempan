<?php
namespace watoki\tempan\model;

use watoki\dom\Element;

class ListModel {

    public $item;

    public $size;

    public function __construct($items = array()) {
        $this->item = $items;
        $this->size = new QuantityModel(count($items));
    }

    public function chunk(Element $element) {
        $size = $element->getAttribute('data-size')->getValue();
        $chunks = array_map(function ($chunk) {
            return new ListModel($chunk);
        }, array_chunk($this->item, $size));
        return $chunks;
    }

    public function isEmpty() {
        return empty($this->item);
    }

    public function isNotEmpty() {
        return !$this->isEmpty();
    }

} 