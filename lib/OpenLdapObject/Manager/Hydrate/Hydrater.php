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

use OpenLdapObject\Collection\ArrayCollection;
use OpenLdapObject\Entity;
use OpenLdapObject\Exception\InvalidHydrateException;
use OpenLdapObject\Manager\EntityAnalyzer;
use OpenLdapObject\Collection\EntityCollection;
use OpenLdapObject\Manager\EntityManager;
use OpenLdapObject\OpenLdapObject;
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
    public function hydrate(array &$data) {
        $entity = new $this->className();
        // To fix a bug: Ldap column name is always to lower case
        $column = array();
        foreach($this->analyzer->listColumns() as $name => $info) {
            $column[strtolower($name)] = array_merge($info, array('realname' => $name));
        }

        foreach($data as $key => $value) {
            $keyLow = strtolower($key);

            if(!array_key_exists($keyLow, $column)) {
				if($keyLow === 'objectclass') {
					foreach($value as $objectClass) {
						if(!$entity->getObjectClass()->contains($objectClass)) $entity->addObjectClass($objectClass);
					}
				}
				continue;
            }

            if(is_array($value) && $column[$keyLow]['type'] === 'string') {
				if(!$column[$keyLow]['strict'] || !OpenLdapObject::isStrict()) {
					// If is not strict, given the first element of the array
					$value = reset($value);
				} else {
					throw new InvalidHydrateException('Column ' . $key . ' define as a string but data is array');
				}
            }

            if($column[$keyLow]['type'] === 'array') {
               // $method = 'add' . Utils::capitalize($column[$keyLow]['realname']);
                if(!is_array($value)) {
                    $data[$key] = array($value);
                    $value = array($value);
                }
				$property = $this->analyzer->getReflection()->getProperty($column[$keyLow]['realname']);
				$isAccessible = $property->isPublic();
				$property->setAccessible(true);
				$property->setValue($entity, new ArrayCollection($value));
				if(!$isAccessible) {
					$property->setAccessible(false);
				}
            } elseif($column[$keyLow]['type'] === 'entity') {
                $multi = $this->analyzer->isEntityRelationMultiple($column[$keyLow]['realname']);
                if($multi) {
                    $property = $this->analyzer->getReflection()->getProperty($column[$keyLow]['realname']);
                    $isAccessible = $property->isPublic();
                    $property->setAccessible(true);
                    // Manage multi entity but only one
                    if(!is_array($value)) {
                        $data[$key] = array($value);
                        $value = array($value);
                    }
                    $property->setValue($entity, new EntityCollection(EntityCollection::DN, $this->em->getRepository($column[$keyLow]['relation']['classname']), $value));
                    if(!$isAccessible) {
                        $property->setAccessible(false);
                    }
                } else {
                    $method = 'set' . Utils::capitalize($column[$keyLow]['realname']);
                    $entity->$method($this->em->getRepository($column[$keyLow]['relation']['classname'])->read($value));
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
    public function getData(Entity $entity) {
        $column = $this->analyzer->listColumns();
        $data = array();

        foreach($column as $key => $value) {
            $method = 'get' . Utils::capitalize($key);
            $data[$key] = $entity->$method();
            if(is_null($data[$key])) $data[$key] = array();
			if($data[$key] instanceof ArrayCollection) $data[$key] = $data[$key]->toArray();
        }

		$data['objectclass'] = $entity->getObjectClass()->toArray();

        return $data;
    }

	public function defineCollection(Entity $entity) {
		foreach($this->analyzer->listColumns() as $name => $info) {
			switch($info['type']) {
				case 'array':
					$value = new ArrayCollection();
					break;
				case 'entity':
					if($info['relation']['multi']) {
						$value = new EntityCollection(EntityCollection::DN, $info['relation']['classname'], array());
					} else {
						$value = null;
					}
					break;
				default:
					$value = null;
			}
			if(!is_null($value)) {
				$property = $this->analyzer->getReflection()->getProperty($name);
				$isAccessible = $property->isPublic();
				$property->setAccessible(true);
				$property->setValue($entity, $value);
				if(!$isAccessible) {
					$property->setAccessible(false);
				}
			}
		}
	}

	public function defineObjectClass(Entity $entity) {
		$classAnnotation = $this->analyzer->getClassAnnotation();
		foreach($classAnnotation['objectclass'] as $objectClass) {
			$entity->addObjectClass($objectClass);
		}
	}
}

?>