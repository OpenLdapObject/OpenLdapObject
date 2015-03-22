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

namespace OpenLdapObject\Manager\Hydrate;


use OpenLdapObject\Exception\InvalidHydrateException;
use OpenLdapObject\Manager\EntityAnalyzer;
use OpenLdapObject\Collection\EntityCollection;
use OpenLdapObject\Manager\EntityManager;
use OpenLdapObject\Utils;

class Hydrater {
    /**
     * @var string
     */
    private $className;
    /**
     * @var EntityAnalyzer
     */
    private $analyzer;

    /**
     * @var \OpenLdapObject\Manager\EntityManager
     */
    private $em;

    public function __construct($className, EntityManager $em = null) {
        $this->className = $className;
        $this->analyzer = EntityAnalyzer::get($className);
        $this->em = $em;
    }

    /**
     * Hydrate an entity
     * @param array $data
     * @return mixed Entity
     * @throws \OpenLdapObject\Exception\InvalidHydrateException
     */
    public function hydrate(array $data) {
        $entity = new $this->className();
        // To fix a bug: Ldap column name is always to lower case
        $column = array();
        foreach($this->analyzer->listColumns() as $name => $info) {
            $column[strtolower($name)] = array_merge($info, array('realname' => $name));
        }

        foreach($data as $key => $value) {
            $keyLow = strtolower($key);

            if(!array_key_exists($keyLow, $column)) {
                continue;
            }

            if(is_array($value) && $column[$keyLow]['type'] === 'string') {
                throw new InvalidHydrateException('Column ' . $key . ' define as a string but data is array');
            }

            if($column[$keyLow]['type'] === 'array') {
                $method = 'add' . Utils::capitalize($column[$keyLow]['realname']);
                if(is_array($value)) {
                    foreach($value as $e) {
                        $entity->$method($e);
                    }
                } else {
                    $entity->$method($value);
                }
            } elseif($column[$keyLow]['type'] === 'entity') {
                $multi = $this->analyzer->isEntityRelationMultiple($keyLow);
                if($multi) {
                    $property = $this->analyzer->getReflection()->getProperty($keyLow);
                    $isAccessible = $property->isPublic();
                    $property->setAccessible(true);
                    $property->setValue($entity, new EntityCollection(EntityCollection::DN, $this->em->getRepository($column[$keyLow]['relation']['classname']), $value));
                    if(!$isAccessible) {
                        $property->setAccessible(false);
                    }
                } else {
                    $method = 'set' . Utils::capitalize($column[$keyLow]['realname']);
                    $entity->$method($this->repository->read($value));
                }
            } else {
                $method = 'set' . Utils::capitalize($column[$keyLow]['realname']);
                $entity->$method($value);
            }
        }

        return $entity;
    }

    /**
     * Inverse the hydrate method
     * @param $entity
     * @return array
     */
    public function getData($entity) {
        $column = $this->analyzer->listColumns();
        $data = array();

        foreach($column as $key => $value) {
            $method = 'get' . Utils::capitalize($key);
            $data[$key] = $entity->$method();
        }

        return $data;
    }
}

?>