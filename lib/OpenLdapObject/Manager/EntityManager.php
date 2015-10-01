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


use OpenLdapObject\LdapClient\Client;

class EntityManager {
    private static $availableManager = array();
    private $client;
    private $flusher;
    private $repository = array();

    private $toPersistEntity = array();
    private $toRemoveEntity = array();

    public static function addEntityManager($name, Client $client, $ignore = false) {
        if(!is_string($name)) {
            throw new \InvalidArgumentException('$name must be a string');
        }

        if(array_key_exists($name, self::$availableManager) && !$ignore) {
            throw new \InvalidArgumentException('A "'.$name.'" manager already defined');
        }

        self::$availableManager[$name] = new EntityManager($client);
    }

    public static function getEntityManager($name = 'default') {
        if(!is_string($name)) {
            throw new \InvalidArgumentException('$name must be a string');
        }

        if(!array_key_exists($name, self::$availableManager)) {
            throw new \InvalidArgumentException('The "'.$name.'" manager is not defined');
        }

        return self::$availableManager[$name];
    }

    private function __construct(Client $client) {
        $this->client = $client;
        $this->flusher = new EntityFlusher($this);
    }

    /**
     * @param $entityClass
     * @return Repository
     */
    public function getRepository($entityClass) {
        if(!array_key_exists($entityClass, $this->repository)) {
            $this->repository[$entityClass] = new Repository($this, $entityClass);
        }

        return $this->repository[$entityClass];
    }

    public function getClient() {
        return $this->client;
    }

    public function persist($entity) {
        if(!is_subclass_of($entity, 'OpenLdapObject\Entity')) {
            throw new \InvalidArgumentException('The entity is not a valid entity');
        }

        if(!in_array($entity, $this->toPersistEntity)) {
            $this->toPersistEntity[] = $entity;
        }
    }

    public function remove($entity) {
        if(!is_subclass_of($entity, 'OpenLdapObject\Entity')) {
            throw new \InvalidArgumentException('The entity is not a valid entity');
        }

        if(!in_array($entity, $this->toRemoveEntity)) {
            $this->toRemoveEntity[] = $entity;
        }
    }

    public function flush(array $param = array()) {
        $this->flusher->setParam($param);
        foreach($this->toPersistEntity as $entity) {
            $repository = $this->getRepository(get_class($entity));
            $this->flusher->flushEntity($entity, $repository->getHydrater(), $repository->getAnalyzer());
        }
        foreach($this->toRemoveEntity as $entity) {
            $repository = $this->getRepository(get_class($entity));
            $this->flusher->removeEntity($entity, $repository->getHydrater(), $repository->getAnalyzer());
        }

        $this->toPersistEntity = array();
        $this->toRemoveEntity = array();
    }
    
    public function setClient(Client $client) {
        $this->client = $client;
    }
}
