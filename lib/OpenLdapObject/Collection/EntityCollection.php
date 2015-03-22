<?php
/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Pierre Pélisset
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */


namespace OpenLdapObject\Collection;


use ArrayIterator;
use OpenLdapObject\Manager\Repository;

class EntityCollection extends \ArrayObject {
    const DN = 0, SEARCH = 1;

    private $type;

    private $searchInfo;

    private $repository;

    private $index = array();

    private $data = array();

    private $info = array();

    private $iterator;

    public function __construct($type, Repository $repository, array $index, array $info = array(), $data = array()) {
        parent::__construct();
        $this->setFlags(\ArrayObject::STD_PROP_LIST);
        if(!in_array($type, array(EntityCollection::DN, EntityCollection::SEARCH))) {
            throw new \Exception('Bad type of EntityCollection');
        }

        $this->type = $type;
        $this->repository = $repository;
        $this->info = $info;

        if($type === EntityCollection::SEARCH) {
            if(!array_key_exists('searchQuery', $info)) {
                throw new \Exception('Type SEARCH but have no $info[\'searchQuery\']');
            }
            $this->searchInfo = $info['searchQuery'];
        }

        $this->index = $index;
        $this->data = $data;
    }

    public function offsetExists($index) {
        return (array_key_exists($index, $this->index) || array_key_exists($index, $this->data));
    }

    public function offsetGet($index) {
        if(!array_key_exists($index, $this->data)) {
            $this->data[$index] = $this->repository->read($this->index[$index]);
        }
        return $this->data[$index];
    }

    public function offsetSet($index, $newval) {
        if(!is_a($newval, $this->repository->getClassName())) {
            throw new \InvalidArgumentException(sprintf('%s is not a %s', $newval, $this->repository->getClassName()));
        }
        $this->data[$index] = $newval;
    }

    public function offsetUnset($index) {
        if(array_key_exists($index, $this->data)) unset($this->data[$index]);
        if(array_key_exists($index, $this->index)) unset($this->index[$index]);
    }

    public function append($value) {
        if(!is_a($newval, $this->repository->getClassName())) {
            throw new \InvalidArgumentException(sprintf('%s is not a %s', $newval, $this->repository->getClassName()));
        }
        $this->data[] = $value;
        $this->index[] = $value->_getDn();
    }

    public function getArrayCopy() {
        return new EntityCollection($this->type, $this->repository, $this->index, $this->info, $this->data);
    }

    public function count() {
        return count($this->index);
    }

    public function getIterator() {
        if(is_null($this->iterator)) {
            $this->iterator = new EntityIterator($this);
        }
        return $this->iterator;
    }


}