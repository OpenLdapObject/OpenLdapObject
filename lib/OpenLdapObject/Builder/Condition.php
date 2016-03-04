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

namespace OpenLdapObject\Builder;


use OpenLdapObject\Manager\Repository;

class Condition
{
    private $key;
    private $value;
    private $not;
    private $approx;

    public function __construct($key, $value, $not = false, $approx = false)
    {
        $this->key = $key;
        $this->value = $value;
        $this->not = $not;
        $this->approx = $approx;
    }

    public function getQueryForRepository(Repository $repository)
    {
        $columns = $repository->getAnalyzer()->listColumns();
        if (!array_key_exists($this->key, $columns)) {
            throw new \InvalidArgumentException('No column name ' . $this->key . '. Column available : [' . implode(',', array_keys($columns)) . ']');
        }
        return ($this->not ? '!' : '') . '(' . $this->key . ($this->approx ? '~' : '') . '=' . $this->value . ')';
    }
}
