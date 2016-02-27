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


use OpenLdapObject\Exception\BadQueryException;
use OpenLdapObject\Manager\Repository;

class Query
{
    const CAND = 0, COR = 1;
    private $query = [];
    private static $operator = [Query::CAND => '&', Query::COR => '|'];
    private $queryOperator;

    public function __construct($operator)
    {
        if (!array_key_exists($operator, self::$operator)) {
            throw new BadQueryException('Bad operator');
        }
        $this->queryOperator = $operator;
    }

    /**
     * @param Query|Condition[] $query
     */
    public function cAnd($query)
    {
        $this->addQuery($query, Query::CAND);
    }

    /**
     * @param Query|Condition[] $query
     */
    public function cOr($query)
    {
        $this->addQuery($query, Query::COR);
    }

    private function addQuery($query, $operator)
    {
        if (is_array($query)) {
            foreach ($query as $condition) {
                if (!$condition instanceof Condition) {
                    throw new BadQueryException('All element of array must be Condition');
                }
            }
            $this->query[] = [
                'condition' => $query,
                'operator' => $operator
            ];
        } elseif ($query instanceof Query) {
            $this->query[] = [
                'query' => $query,
                'operator' => $operator
            ];
        } else {
            throw new BadQueryException('Bad query element');
        }

    }

    public function getQueryForRepository(Repository $repository)
    {
        $queryString = '(' . self::$operator[$this->queryOperator];
        foreach ($this->query as $queryPart) {
            $queryString .= '(' . self::$operator[$queryPart['operator']];
            if (array_key_exists('condition', $queryPart)) {
                $queryString .= $this->getConditionQuery($queryPart['condition'], $repository);
            } else {
                $queryString .= $queryPart['query']->getQueryForRepository($repository);
            }
            $queryString .= ')';
        }
        return $queryString . ')';
    }

    private function getConditionQuery(array $conditions, Repository $repository)
    {
        $queryString = '';
        foreach ($conditions as $condition) {
            $queryString .= $condition->getQueryForRepository($repository);
        }
        return $queryString;
    }
}