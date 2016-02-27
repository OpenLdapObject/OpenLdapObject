<?php

namespace OpenLdapObject\Collection;

use Countable;
use IteratorAggregate;
use ArrayAccess;

interface Collection extends Countable, IteratorAggregate, ArrayAccess
{
    public function add($element);

    public function clear();

    public function contains($element);

    public function containsKey($key);

    public function get($key);

    public function isEmpty();

    public function indexOf($element);

    public function remove($key);

    public function removeElement($element);

    public function set($key, $value);

    public function toArray();
}