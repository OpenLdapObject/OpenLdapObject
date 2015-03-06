<?php

namespace OpenLdapObject\Manager;


use OpenLdapObject\Entity;
use OpenLdapObject\Exception\InflushableException;
use OpenLdapObject\Manager\Hydrate\Hydrater;

class EntityFlusher {
    const CREATE = 0, REPLACE = 1, DELETE = 2;
    /**
     * @var EntityManager
     */
    private $em;
    private $param = array();

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function setParam(array $param) {
        $keys = array(EntityFlusher::CREATE, EntityFlusher::REPLACE, EntityFlusher::DELETE);
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

        $currentData = $hydrater->getData($entity);

        $diff = self::dataDiff($currentData, $originData);

        $dn = $entity->_getDn();
        if(is_null($dn)) {
            if($this->param[EntityFlusher::CREATE]) {
                $this->create($entity, $currentData, $diff, $analyzer);
            } else {
                throw new InflushableException('Unable to create entity, Param::Create is false');
            }
        }
        //var_dump($diff);
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
        $dn = implode(',', $dnPiece);

        $diff['objectclass'] = $analyzer->getObjectclass();

        $this->em->getClient()->create($dn, $diff);
    }
} 