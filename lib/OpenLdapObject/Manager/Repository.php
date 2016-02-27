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

namespace OpenLdapObject\Manager;

use OpenLdapObject\Builder\Condition;
use OpenLdapObject\Builder\Query;
use OpenLdapObject\LdapClient\Client;
use OpenLdapObject\Manager\Hydrate\Hydrater;

class Repository
{
    private $em;
    private $className;
    private $analyzer;
    private $columns;
    private $index;
    private $baseDn;
    private $hydrater;

    public function __construct(EntityManager $em, $className)
    {
        $this->em = $em;
        $this->className = $className;
        $this->analyzer = EntityAnalyzer::get($className);
        $column = $this->analyzer->listColumns();
        $index = $this->analyzer->getIndex();
        $this->columns = array_keys($column);
        $this->baseDn = $this->analyzer->getBaseDn();
        $this->hydrater = new Hydrater($this->className, $em);
    }

    private function query($query, $limit = 0)
    {
        $result = $this->em->getClient()->search($query, array_merge($this->columns, array('objectclass')), $limit, $this->baseDn);
        return $this->manage($result);
    }

    public function getHydrater()
    {
        return $this->hydrater;
    }

    /**
     * @return EntityAnalyzer
     */
    public function getAnalyzer()
    {
        return $this->analyzer;
    }

    public function find($value)
    {
        $index = $this->analyzer->getIndex();
        if ($index === false) {
            throw new \InvalidArgumentException('The ' . $this->className . ' Entity have no index');
        }

        return $this->findOneBy(array($index => $value));
    }

    public function findByQuery(Query $query, $limit = 0)
    {
        $query = '(&(objectclass=*)' . $query->getQueryForRepository($this) . ')';
        return $this->query($query, $limit);
    }

    public function findBy(array $search, $limit = 0)
    {
        $query = new Query(Query::CAND);
        $conditions = [];
        foreach ($search as $key => $value) {
            $conditions[] = new Condition($key, $value);
        }
        $query->cAnd($conditions);
        return $this->findByQuery($query, $limit);
    }

    public function findOneBy(array $search)
    {
        $res = $this->findBy($search, 1);
        if (count($res) == 0) {
            return false;
        }
        return $res[0];
    }

    public function manage(array $result)
    {
        $result = Client::cleanResult($result);

        $entities = array();
        foreach ($result as $data) {
            $entity = $this->hydrater->hydrate($data['data']);
            $entity->_setDn($data['dn']);
            $entity->_setOriginData($data['data']);
            $entities[] = $entity;
        }

        return $entities;
    }

    public function read($dn, $limit = 1)
    {
        $res = $this->manage($this->em->getClient()->read($dn, $this->columns, $limit));
        if (count($res) == 0) {
            return false;
        }
        return $res[0];
    }

    public function getClassName()
    {
        return $this->className;
    }
}