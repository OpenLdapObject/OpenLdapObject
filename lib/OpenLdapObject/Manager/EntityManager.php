<?php

namespace OpenLdapObject\Manager;


use OpenLdapObject\LdapClient\Client;

class EntityManager {
    private static $availableManager = array();
    private $client;
    private $repository = array();
    private $knowsEntities = array();

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

    public function know($dn, $entity, $originData) {
        $alreadySave = false;
        foreach($this->knowsEntities as $fetchEntity) {
            if($fetchEntity == $entity) {
                $alreadySave = true;
            }
        }
        if(!$alreadySave) {
            $this->knowsEntities[] = array(
                'dn' => $dn,
                'entity' => $entity,
                'origin' => $originData
            );
        }
    }

    public function getClient() {
        return $this->client;
    }


} 