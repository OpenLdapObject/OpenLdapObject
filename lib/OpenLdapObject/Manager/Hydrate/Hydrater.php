<?php

namespace OpenLdapObject\Manager\Hydrate;


use OpenLdapObject\Exception\InvalidHydrateException;
use OpenLdapObject\Manager\EntityAnalyzer;
use OpenLdapObject\Utils;

class Hydrater {
    private $className;
    private $analyzer;

    public function __construct($className) {
        $this->className = $className;
        $this->analyzer = new EntityAnalyzer($className);
    }

    /**
     * Hydrate an entity
     * @param array $data
     * @return mixed Entity
     * @throws \OpenLdapObject\Exception\InvalidHydrateException
     */
    public function hydrate(array $data) {
        $entity = new $this->className();
        $column = $this->analyzer->listColumns();

        foreach($data as $key => $value) {
            if(!array_key_exists($key, $column)) {
                continue;
            }

            if(is_array($value) && $column[$key]['type'] !== 'array') {
                throw new InvalidHydrateException('Column ' . $key . ' define as a string but data is array');
            }

            if($column[$key]['type'] === 'array') {
                $method = 'add' . Utils::capitalize($key);
                if(is_array($data)) {
                    foreach($value as $e) {
                        $entity->$method($e);
                    }
                } else {
                    $entity->$method($value);
                }
            } else {
                $method = 'set' . Utils::capitalize($key);
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