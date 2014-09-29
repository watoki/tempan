<?php
namespace watoki\tempan\model;

class QuantityModel {

    public $value;

    public function __construct($value) {
        $this->value = $value;
    }

    public function isZero() {
        return $this->value == 0;
    }

    public function isOne() {
        return $this->value == 1;
    }

    public function isNotOne() {
        return !$this->isOne();
    }

    public function isMany() {
        return $this->value > 1;
    }

} 