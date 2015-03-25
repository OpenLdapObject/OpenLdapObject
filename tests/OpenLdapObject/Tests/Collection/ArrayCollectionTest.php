<?php

namespace OpenLdapObject\Tests\Collection;

use OpenLdapObject\Collection\ArrayCollection;

class ArrayCollectionTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var ArrayCollection
	 */
	private $collection;

	public function setUp() {
		$this->collection = new ArrayCollection();
	}

	public function testIndexContains() {
		$this->collection->add('Hye');
		$this->collection->add(34);
		$this->collection->add(true);

		$this->assertEquals($this->collection->indexOf(true), 2);
		$this->assertEquals($this->collection->indexOf(34), 1);
		$this->assertTrue($this->collection->contains(34));
	}

	public function testCount() {
		$this->collection->add('Hye');
		$this->collection->add(34);

		$this->assertEquals(count($this->collection), 2);
	}

	public function testArrayAccess() {
		$this->collection->add('Hye');
		$this->collection->add(34);
		$this->collection[] = 24;
		$this->collection[0] = 'Good Bye';

		$this->assertEquals($this->collection->toArray(), array(
			'Good Bye',
			34,
			24
		));
	}

	public function testIterator() {
		$this->collection->add('Hye');
		$this->collection->add(34);
		$this->collection->add(array('Hello'));

		$array = array();
		foreach($this->collection as $key => $value) {
			$array[$key] = $value;
		}

		$this->assertEquals($array, $this->collection->toArray());
	}
}
