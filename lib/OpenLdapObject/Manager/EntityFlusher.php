<?php

namespace OpenLdapObject\Manager;


use OpenLdapObject\Entity;
use OpenLdapObject\Exception\InflushableException;
use OpenLdapObject\Manager\Hydrate\Hydrater;

class EntityFlusher {
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
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
            $this->create($entity, $currentData, $diff, $analyzer);
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

        $this->em->getClient()->create($dn, $diff);
    }
} 