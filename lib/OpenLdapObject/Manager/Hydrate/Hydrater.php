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

            if(is_array($value) && $column[$keyLow]['type'] !== 'array') {
                throw new InvalidHydrateException('Column ' . $key . ' define as a string but data is array');
            }

            if($column[$keyLow]['type'] === 'array') {
                $method = 'add' . Utils::capitalize($column[$keyLow]['realname']);
                if(is_array($data)) {
                    foreach($value as $e) {
                        $entity->$method($e);
                    }
                } else {
                    $entity->$method($value);
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