<?php
namespace watoki\tempan;

use watoki\collections\Liste;
use watoki\dom\Element;
use watoki\dom\Text;

class Animator {

    private $log = array();

    private $loggingEnabled = false;

    /**
     * @var array Current context of models
     */
    private $stack;

    /**
     * @var boolean True if the last findProperty() was successful
     */
    private $foundProperty;

    function __construct($model) {
        $this->stack = array($model);
    }

    public function animate(Element $element) {
        if ($this->isProperty($element)) {
            $property = $this->getPropertyName($element);
            $value = $this->findProperty($property, $element);

            if (!$this->foundProperty) {
                $this->animateChildren($element);
            } else if ($this->isNull($value)) {
                $element->getParent()->getChildren()->removeElement($element);
            } else if ($this->isContent($value)) {
                $this->setContent($element, $value);
            } else if ($this->isTrue($value)) {
                $this->animateChildren($element);
            } else if ($this->isList($value)) {
                $this->stack[] = $value;
                $this->animateList($element, $property);
                array_pop($this->stack);
            } else if ($this->isNodeModel($value)) {
                $this->stack[] = $value;
                $this->animateAttributes($element);
                $this->animateChildren($element);
                array_pop($this->stack);
            }
        } else {
            $this->animateChildren($element);
        }
    }

    private function setContent(Element $element, $content) {
        $element->getChildren()->clear();
        $element->getChildren()->append(new Text($this->getContent($content)));
    }

    /**
     * @param Element $element
     */
    public function animateAttributes(Element $element) {
        foreach ($element->getAttributes() as $attribute) {
            if (!$this->hasModelField(end($this->stack), $attribute->getName())) {
                continue;
            }

            $value = $this->getModelField(end($this->stack), $attribute->getName(), $element);
            if ($this->isNull($value)) {
                $element->getAttributes()->removeElement($attribute);
            } else if ($this->isContent($value)) {
                $attribute->setValue($value);
            }
        }
    }

    public function animateChildren(Element $element) {
        foreach ($element->getChildren() as $child) {
            if (!$child instanceof Element) {
                continue;
            }

            if ($child->getParent()) {
                $this->animate($child);
            }
        }
    }

    private function animateList(Element $element, $key) {
        foreach (end($this->stack) as $itemModel) {
            $this->stack[] = array($key => $itemModel);
            $this->insertAndAnimateSibling($element);
            array_pop($this->stack);
        }

        $this->removeSiblingProperties($element, $key);
    }

    private function insertAndAnimateSibling(Element $element) {
        $itemElement = $element->copy();
        $siblings = $element->getParent()->getChildren();
        $siblings->insert($itemElement, $siblings->indexOf($element));

        $this->animate($itemElement);
    }

    private function removeSiblingProperties(Element $child, $key) {
        /** @var Liste $siblings */
        $siblings = $child->getParent()->getChildren();
        $index = $siblings->indexOf($child);

        while ($siblings->isInBound($index)) {
            $child = $siblings->get($index);
            if (!$child instanceof Element) {
                $index++;
                continue;
            } else if (!($this->isProperty($child) && $this->getPropertyName($child) == $key)) {
                break;
            }
            $siblings->remove($index);
        }
    }

    private function findProperty($property, Element $element) {
        for ($i = count($this->stack)- 1; $i >= 0; $i--) {
            if ($this->hasModelField($this->stack[$i], $property)) {
                $this->foundProperty = true;
                $this->log("Property $property found.");
                return $this->getModelField($this->stack[$i], $property, $element);
            }
        }
        $this->foundProperty = false;
        $this->log("Property $property not found in " . json_encode(end($this->stack)));
        return null;
    }

    private function isProperty(Element $element) {
        return $element->getAttribute('property') !== null;
    }

    private function getPropertyName(Element $element) {
        return $element->getAttribute('property')->getValue();
    }

    private function hasModelField($model, $field) {
        return (is_array($model) && array_key_exists($field, $model))
                || (is_object($model) && property_exists($model, $field))
                || (is_object($model) && method_exists($model, $field));
    }

    private function getModelField($model, $field, Element $element) {
        if (is_array($model)) {
            return isset($model[$field]) ? $model[$field] : null;
        } else if (is_object($model)) {
            if (property_exists($model, $field)) {
                if (is_callable($model->$field)) {
                    $callable = $model->$field;
                    return $callable($element, $this);
                }
                return $model->$field;
            } else if (method_exists($model, $field)) {
                return $model->$field($element, $this);
            }
        }
        return null;
    }

    private function isNull($value) {
        return $value === false || $value === null || is_array($value) && empty($value);
    }

    private function isTrue($value) {
        return $value === true;
    }

    private function isContent($value) {
        return !$this->isNull($value) && $value !== true && !$this->isNodeModel($value);
    }

    private function isNodeModel($model) {
        return is_object($model) || is_array($model);
    }

    private function getContent($model) {
        return (string) $model;
    }

    private function isList($model) {
        if (!is_array($model)) {
            return false;
        }

        $i = 0;
        foreach ($model as $key => $value) {
            if (!is_numeric($key) || $key !== $i) {
                return false;
            }
            $i++;
        }
        return true;
    }

    private function log($msg) {
        if ($this->loggingEnabled) {
            $this->log[] = $msg;
        }
    }

    public function enableLogging($enable = true) {
        $this->loggingEnabled = $enable;
    }

    /**
     * @return array
     */
    public function getLog() {
        return $this->log;
    }
}