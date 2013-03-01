<?php
namespace watoki\tempan;

use watoki\collections\events\MapSetEvent;
use watoki\collections\events\MapRemoveEvent;
use watoki\collections\Map;
use watoki\collections\Liste;

class Element {

    /**
     * @var Liste|null|Element[]
     */
    public $children;

    /**
     * @var \DOMNode
     */
    private $element;

    /**
     * @var null|Map
     */
    private $attributes;

    /**
     * @var Element|null
     */
    private $parent;

    /**
     * @var Element|null
     */
    private $nextSibling;

    public function __construct(\DOMNode $element, Element $parent = null) {
        $this->element = $element;
        $this->parent = $parent;
    }

    /**
     * @return \watoki\collections\Map
     */
    public function getAttributes() {
        if (!$this->attributes) {
            $this->attributes = new Map();

            foreach ($this->element->attributes as $name => $attr) {
                $this->attributes->set($name, $attr->value);
            }

            $this->setAttributesListeners($this->attributes, $this->element);
        }
        return $this->attributes;
    }

    private function setAttributesListeners(Map $attributes, \DOMElement $node) {
        $attributes->on(MapSetEvent::$CLASSNAME, function (MapSetEvent $event) use ($node) {
            /** @var $node \DOMElement */
            $node->setAttribute($event->getKey(), $event->getValue());
        });

        $attributes->on(MapRemoveEvent::$CLASSNAME, function (MapRemoveEvent $event) use ($node) {
            /** @var $node \DOMElement */
            $node->removeAttribute($event->getKey());
        });
    }

    public function insertSibling(Element $child) {
        $this->parent->insertBefore($child, $this);
    }

    private function insertBefore(Element $insert, Element $child) {
        if ($this->children) {
            $childIndex = $this->getChildren()->indexOf($child);
            if ($childIndex > 0) {
                $this->getChildren()->get($childIndex - 1)->nextSibling = $insert;
            }
            $insert->nextSibling = $child;
            $this->getChildren()->insert($insert, $childIndex);
        }
        $this->element->insertBefore($insert->element, $child->element);
    }

    public function remove() {
        $this->parent->removeChild($this);
    }

    private function removeChild(Element $child) {
        if ($this->children) {
            $childIndex = $this->getChildren()->indexOf($child);
            if ($childIndex > 0) {
                if ($childIndex == $this->getChildren()->count() - 1) {
                    $this->getChildren()->get($childIndex - 1)->nextSibling = null;
                } else {
                    $this->getChildren()->get($childIndex - 1)->nextSibling = $this->getChildren()->get($childIndex + 1);
                }
            }
            $this->getChildren()->remove($childIndex);
        }
        $child->parent = null;
        $this->element->removeChild($child->element);
    }

    public function getNextSibling() {
        return $this->nextSibling;
    }

    /**
     * @param string|null $property Filter by property name
     * @return \watoki\collections\Liste|Element[]
     */
    public function getChildren($property = null) {
        if (!$this->children) {
            $this->children = new Liste();
            /** @var $lastChild Element|null */
            $lastChild = null;
            foreach ($this->element->childNodes as $child) {
                if ($child instanceof \DOMElement) {
                    $nextChild = new Element($child, $this);
                    $this->children->append($nextChild);

                    if ($lastChild) {
                        $lastChild->nextSibling = $nextChild;
                    }
                    $lastChild = $nextChild;
                }
            }
        }
        if ($property) {
            return $this->filterBy($property);
        }
        return $this->children;
    }

    /**
     * @param $property
     * @return \watoki\collections\Liste
     */
    private function filterBy($property) {
        $filterd = new Liste();
        foreach ($this->children as $child) {
            if ($child->getAttributes()->has('property') && $child->getAttributes()->get('property') == $property) {
                $filterd->append($child);
            }
        }
        return $filterd;
    }

    public function setContent($content) {
        foreach ($this->getChildren() as $child) {
            $this->removeChild($child);
        }

        foreach ($this->element->childNodes as $child) {
            $this->element->removeChild($child);
        }

        $this->element->appendChild(new \DOMText($content));
    }

    public function getContent() {
        return $this->element->textContent;
    }

    public function copy() {
        /** @var $clone \DOMElement */
        $clone = $this->element->cloneNode(true);
        return new Element($clone, $this->parent);
    }

    public function getParent() {
        return $this->parent;
    }
}
