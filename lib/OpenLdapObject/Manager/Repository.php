<?php

namespace OpenLdapObject\Manager;

use OpenLdapObject\LdapClient\Client;
use OpenLdapObject\Manager\Hydrate\Hydrater;

class Repository {
    private $em;
    private $className;
    private $analyzer;
    private $columns;
    private $index;
    private $baseDn;
    private $hydrater;

    public function __construct(EntityManager $em, $className) {
        $this->em = $em;
        $this->className = $className;
        $this->analyzer = new EntityAnalyzer($className);
        $column = $this->analyzer->listColumns();
        $index = $this->analyzer->getIndex();
        $this->columns = array_keys($column);
        $this->baseDn = $this->analyzer->getBaseDn();
        $this->hydrater = new Hydrater($this->className);
    }

    private function query($query, $limit = 0) {
        $result = $this->em->getClient()->search($query, $this->columns, $limit, $this->baseDn);
        $result = Client::cleanResult($result);

        $entities = array();
        foreach($result as $data) {
            $entity = $this->hydrater->hydrate($data['data']);
            $entity->_setDn($data['dn']);
            $entity->_setOriginData($data['data']);
            $entities[] = $entity;
        }

        return $entities;
    }

    public function getHydrater() {
        return $this->hydrater;
    }

    public function getAnalyzer() {
        return $this->analyzer;
    }

    public function find($value) {
        $index = $this->analyzer->getIndex();
        if($index === false) {
            throw new \InvalidArgumentException('The ' . $this->className . ' Entity have no index');
        }

        return $this->findOneBy(array($index => $value));
    }

    public function findBy(array $search, $limit = 0) {
        $query = '(&(objectclass=*)';
        foreach($search as $column => $value) {
            if(!in_array($column, $this->columns)) {
                throw new \InvalidArgumentException('No column name ' . $column . '. Column available : ['.implode(',', $this->columns).']');
            }
            $query .= '('.$column.'='.$value.')';
        }
        $query .= ')';

        return $this->query($query, $limit);
    }

    public function findOneBy(array $search) {
        $res = $this->findBy($search, 1);
        if(count($res) == 0) {
            return false;
        }
        return $res[0];
    }
}