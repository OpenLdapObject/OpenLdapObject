<?php

namespace OpenLdapObject\Collection;

use OpenLdapObject\Exception\BadRelationException;
use OpenLdapObject\Exception\InvalidEntityException;
use OpenLdapObject\Manager\Repository;

class EntityCollection implements Collection {
	const DN = 0, SEARCH = 1;

	private $type;

	private $searchInfo;

	/**
	 * @var Repository
	 */
	private $repository;
	private $classname;

	private $index = array();

	private $data = array();

	private $info = array();

	private $mustQueryBefore = false;

	public function __construct($type, $repository = null, array $index = null, array $info = array(), $data = array()) {
		if(!in_array($type, array(EntityCollection::DN, EntityCollection::SEARCH))) {
			throw new \InvalidArgumentException('Bad type of EntityCollection');
		}

		$this->type = $type;
		if(is_string($repository)) {
			$this->classname = $repository;
		} else {
			$this->repository = $repository;
			$this->classname = $this->repository->getClassName();
		}
		$this->info = $info;

		if($type === EntityCollection::SEARCH) {
			throw new \Exception('Search not already implements');
			if(!array_key_exists('searchQuery', $info)) {
				throw new \Exception('Type SEARCH but have no $info[\'searchQuery\']');
			}
			$this->searchInfo = $info['searchQuery'];
		}

		$this->index = $index;
		$this->data = $data;

		// Case: ignore_errors => true, we search all results at the first query
		if(array_key_exists('ignore_errors', $this->info) && $this->info['ignore_errors'] === true)
			$this->mustQueryBefore = true;
	}

	private function checkMustQueryBefore() {
		if(!$this->mustQueryBefore) return;

		$cleanIndex = array();
		$cleanData = array();

		foreach($this->index as $index) {
			$data = $this->repository->read($index);
			if($data !== false) {
				$cleanIndex[] = $index;
				$cleanData[] = $data;
			}
		}

		$this->index = $cleanIndex;
		$this->data = $cleanData;

		$this->mustQueryBefore = false;
	}

	public function add($element) {
		$this->checkMustQueryBefore();

		if(!is_object($element) || get_class($element) !== $this->classname) {
			throw new InvalidEntityException('Cannot add entity of type ' . (is_object($element) ? get_class($element) : gettype($element)). ' to an EntityCollection of ' . $this->classname);
		}
		$this->index[] = $element->_getDn();
		$this->data[] = $element;
	}

	public function clear() {
		$this->index = array();
		$this->data = array();
	}

	public function contains($element) {
		return $this->indexOf($element) !== false;
	}

	public function containsKey($key) {
		$this->checkMustQueryBefore();

		return array_key_exists($key, $this->index);
	}

	public function get($key) {
		$this->checkMustQueryBefore();

		if(!array_key_exists($key, $this->index)) {
			throw new \OutOfBoundsException($key);
		}
		if(!array_key_exists($key, $this->data)) {
			$this->data[$key] = $this->repository->read($this->index[$key]);
		}
		if($this->data[$key] === false) {
			throw new BadRelationException('Not found entry: ' . $this->index[$key]);
		}
		return $this->data[$key];
	}

	public function isEmpty() {
		$this->checkMustQueryBefore();

		return $this->count() < 1;
	}

	public function indexOf($element) {
		$this->checkMustQueryBefore();

		foreach($this as $key => $value) {
			if($value === $element) {
				return $key;
			}
		}
		return false;
	}

	public function remove($key) {
		$this->checkMustQueryBefore();

		unset($this->index[$key]);
		unset($this->data[$key]);
	}

	public function removeElement($element) {
		$this->checkMustQueryBefore();

		foreach($this as $key => $value) {
			if($element === $value) {
				$this->remove($key);
			}
		}
	}

	public function set($key, $value) {
		$this->checkMustQueryBefore();

		$this->index[$key] = $value->_getDn();
		$this->data[$key] = $value;
	}

	public function toArray() {
		$this->checkMustQueryBefore();

		$array = array();
		foreach($this->index as $key => $dn) {
			$array[$key] = $this->get($key);
		}
		return $array;
	}

	public function getIterator() {
		$this->checkMustQueryBefore();

		return new \ArrayIterator($this->toArray());
	}

	public function offsetExists($offset) {
		return $this->containsKey($offset);
	}

	public function offsetGet($offset) {
		return $this->get($offset);
	}

	public function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}

	public function offsetUnset($offset) {
		$this->remove($offset);
	}

	public function count() {
		$this->checkMustQueryBefore();
		return count($this->index);
	}

	/**
	 * @return mixed
	 */
	public function getClassname() {
		return $this->classname;
	}

	/**
	 * @param mixed $classname
	 */
	public function setClassname($classname) {
		$this->classname = $classname;
	}

	/**
	 * @return Repository
	 */
	public function getRepository() {
		return $this->repository;
	}

	/**
	 * @param Repository $repository
	 */
	public function setRepository($repository) {
		$this->repository = $repository;
	}
}