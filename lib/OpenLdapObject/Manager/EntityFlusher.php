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


use OpenLdapObject\Entity;
use OpenLdapObject\Exception\InflushableException;
use OpenLdapObject\Manager\Hydrate\Hydrater;

class EntityFlusher {
    const CREATE = 0, RENAME = 1, DELETE = 2;
    /**
     * @var EntityManager
     */
    private $em;
    private $param = array();

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function setParam(array $param) {
        $keys = array(EntityFlusher::CREATE, EntityFlusher::RENAME, EntityFlusher::DELETE);
        foreach($keys as $key) {
            if(!array_key_exists($key, $param)) {
                $this->param[$key] = true;
            } else {
                $this->param[$key] = $param[$key];
            }
        }
    }

    /**
     * @param Entity $entity
     * @param Hydrater $hydrater
     * @param EntityAnalyzer $analyzer
     */
    public function flushEntity($entity, Hydrater $hydrater, EntityAnalyzer $analyzer) {
        $originData = $entity->_getOriginData();
        $originName = array();

        if(is_null($originData)) {
            $originData = array();
            foreach($analyzer->listColumns() as $name => $data) {
                if($data['type'] === 'array') {
                    $originData[strtolower($name)] = array();
                } else {
                    $originData[strtolower($name)] = NULL;
                }
            }
        }

        foreach($analyzer->listColumns() as $name => $data) {
            $originName[strtolower($name)] = $name;
        }

        $currentData = $hydrater->getData($entity);

        foreach($currentData as $column => $value) {
            if(array_key_exists($originName[strtolower($column)], $analyzer->listColumns()) && $analyzer->listColumns()[$originName[strtolower($column)]]['type'] === 'array' && is_null($value)) {
                $currentData[$column] = array();
            }
            // Convert array of entity to array of DN
            if($analyzer->isEntityRelation($originName[strtolower($column)])){
                $listDn = array();
                foreach($value as $e) {
                    $listDn[] = $e->_getDn();
                }
                $currentData[$column] = $listDn;
            }
        }

        $diff = self::dataDiff($currentData, $originData);

        $dn = $entity->_getDn();
        if(is_null($dn)) {
            if($this->param[EntityFlusher::CREATE]) {
                $this->create($entity, $currentData, $diff, $analyzer);
            } else {
                throw new InflushableException('Unable to create entity, Param::Create is false');
            }
        } else {
            if(array_key_exists($analyzer->getIndex(), $diff)) {
                // If key index is diff => rename
                if($this->param[EntityFlusher::RENAME]) {
                    $this->rename($entity, $currentData, $diff, $analyzer);
                } else {
                    throw new InflushableException('Unable to rename entity, Param::Rename is false');
                }
            }
            if(count($diff) > 0) {
                $this->em->getClient()->update($entity->_getDn(), $diff);
            }
        }
    }

    public function removeEntity($entity, Hydrater $hydrater, EntityAnalyzer $analyzer) {
        if($this->param[EntityFlusher::DELETE]) {
            $this->em->getClient()->delete($entity->_getDn());
        } else {
            throw new InflushableException('Unable to delete entity, Param:Delete is false');
        }
    }

    private static function dataDiff($data, $origin) {
        $diff = array();
        foreach($data as $key => $value) {
            $lkey = strtolower($key);
            if(!array_key_exists($lkey, $origin)) {
                $diff[$key] = $value;
            } else {
                if($data[$key] !== $origin[$lkey]) {
                    $diff[$lkey] = $value;
                }
            }
        }

        return $diff;
    }

    private function create($entity, $currentData, $diff, EntityAnalyzer $analyzer) {
        $dn = $this->getNewDn($entity, $currentData, $analyzer);
        $entity->_setDn($dn);

        $diff['objectclass'] = $analyzer->getObjectclass();

        $this->em->getClient()->create($dn, $diff);
    }

    private function rename($entity, $currentData, $diff, EntityAnalyzer $analyzer) {
        $newDn = explode(',', $this->getNewDn($entity, $currentData, $analyzer));
        $oldDn = $entity->_getDn();
        $entity->_setDn(implode(',', $newDn));
        $this->em->getClient()->rename($oldDn, $newDn[0]);
    }

    private function getNewDn($entity, $currentData, EntityAnalyzer $analyzer) {
        $index = $analyzer->getIndex();
        if($index === false) {
            throw new InflushableException('Entity ' . get_class($entity) . 'have no index');
        }
        $dnPiece = array();
        $dnPiece[] = $index . '=' . $currentData[$index];
        if(is_string($analyzer->getBaseDn())) {
            $dnPiece[] = $analyzer->getBaseDn();
        }
        if(is_string($this->em->getClient()->getBaseDn())) {
            $dnPiece[] = $this->em->getClient()->getBaseDn();
        }
        return implode(',', $dnPiece);
    }
} 