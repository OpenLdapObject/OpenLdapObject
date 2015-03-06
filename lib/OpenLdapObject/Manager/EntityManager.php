<?php

namespace OpenLdapObject\Manager;


use OpenLdapObject\LdapClient\Client;

class EntityManager {
    private static $availableManager = array();
    private $client;
    private $flusher;
    private $repository = array();

    private $toPersistEntity = array();

    public static function addEntityManager($name, Client $client) {
        if(!is_string($name)) {
            throw new \InvalidArgumentException('$name must be a string');
        }

        if(array_key_exists($name, self::$availableManager)) {
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

    public function flush(array $param = array()) {
        $this->flusher->setParam($param);
        foreach($this->toPersistEntity as $entity) {
            $repository = $this->getRepository(get_class($entity));
            $this->flusher->flushEntity($entity, $repository->getHydrater(), $repository->getAnalyzer());
        }
    }
}