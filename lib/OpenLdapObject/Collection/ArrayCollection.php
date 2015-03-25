<?php

namespace OpenLdapObject\Collection;


use Traversable;

class ArrayCollection implements Collection {
	private $array;

	public function __construct($baseElement = null) {
		if(is_array($baseElement)) {
			$this->array = $baseElement;
		} else if($baseElement instanceof Collection) {
			$this->array = $baseElement->toArray();
		} else if(is_null($baseElement)) {
			$this->array = array();
		} else {
			throw new \InvalidArgumentException('$baseElement must be null, array or a Collection');
		}
	}

	public function add($element) {
		$this->array[] = $element;
	}

	public function clear() {
		$this->array = array();
	}

	public function contains($element) {
		return $this->indexOf($element) !== false;
	}

	public function containsKey($key) {
		return array_key_exists($key, $this->array);
	}

	public function get($key) {
		return $this->array[$key];
	}

	public function isEmpty() {
		return $this->count() < 1;
	}

	public function indexOf($element) {
		foreach($this->array as $key => $value) {
			if($value === $element) {
				return $key;
			}
		}
		return false;
	}

	public function remove($key) {
		unset($this->array[$key]);
	}

	public function removeElement($element) {
		foreach($this->array as $key => $value) {
			if($element === $value) {
				$this->remove($key);
			}
		}
	}

	public function set($key, $value) {
		$this->array[$key] = $value;
	}

	public function toArray() {
		return $this->array;
	}

	public function getIterator() {
		return new \ArrayIterator($this->array);
	}

	public function offsetExists($offset) {
		return $this->containsKey($offset);
	}

	public function offsetGet($offset) {
		return $this->get($offset);
	}

	public function offsetSet($offset, $value) {
		if(is_null($offset))
			$this->add($value);
		else
			$this->set($offset, $value);
	}

	public function offsetUnset($offset) {
		$this->remove($offset);
	}

	public function count() {
		return count($this->array);
	}

}