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
use Doctrine\Common\Annotations\AnnotationReader;
use OpenLdapObject\Annotations\Annotation;
use OpenLdapObject\Annotations\AnnotationManager;
use OpenLdapObject\Annotations\InvalidAnnotationException;
use OpenLdapObject\Exception\InvalidEntityException;
use OpenLdapObject\Utils;

/**
 * Class EntityAnalyzer
 *
 * Use to analyze an Entity Class
 *
 * @package OpenLdapObject\Manager
 * @author Toshy62 <yoshi62@live.fr>
 */
class EntityAnalyzer {
    const GETTER = 0, SETTER = 1, ADDER = 2, REMOVER = 3;

    private $className;

    private $reflection;
    private $annotationReader;

    // Caching method's Result
    private $listColumns;
    private $listRequiredMethod;
    private $listMissingMethod;
    private $classAnnotation;
    private $index;

    private static $ColumnAnnotation = 'OpenLdapObject\Annotations\Column';
    private static $IndexAnnotation = 'OpenLdapObject\Annotations\Index';
    private static $dnAnnotation = 'OpenLdapObject\Annotations\Dn';
    private static $entityAnnotation = 'OpenLdapObject\Annotations\Entity';
    private static $entityRelationAnnotation = 'OpenLdapObject\Annotations\EntityRelation';

    private static $instances = array();

    public static function get($className) {
        if(!array_key_exists($className, self::$instances)) {
            self::$instances[$className] = new EntityAnalyzer($className);
        }
        return self::$instances[$className];
    }

    private function __construct($className) {
        $this->className = $className;

        $this->reflection = new \ReflectionClass($className);
        $this->annotationReader = new AnnotationReader();
    }

    /**
     * List the Ldap Columns of an Entity Class
     * @return array
     * @throws \OpenLdapObject\Annotations\InvalidAnnotationException
     */
    public function listColumns() {
        if(!is_null($this->listColumns)) {
            return $this->listColumns;
        }

        // We use this method to load the annotation classes, because the annotation Library Doctrine does not
        AnnotationManager::autoLoadAnnotation();

        // Set the list of properties, an array to contain the list of columns and a boolean to check if the class has several indexes
        $properties = $this->reflection->getProperties();
        $haveIndex = false;
        $columns = array();

        foreach($properties as $property) {
            // For each properties, get the list of annotations
            $propertyAnnotation = $this->annotationReader->getPropertyAnnotations($property);
            // Check the property has a Column Annotation
            if(($columnAnnotation = self::haveAnnotation(self::$ColumnAnnotation, $propertyAnnotation)) !== false) {
                $columnAnnotation->check();
                $column = array(
                    'type' => $columnAnnotation->type
                );
                // Check the property has an Index Annotation
                if(($indexAnnotation = self::haveAnnotation(self::$IndexAnnotation, $propertyAnnotation)) !== false) {
                    $indexAnnotation->check();
                    // Verify that the class have a unique index annotation
                    if($haveIndex == true) {
                        throw new InvalidAnnotationException($indexAnnotation, $property->getName(), 'Class ' . $this->className . ' have already an index when he read the annotation of ' . $property->getName());
                    }
                    $column['index'] = true;
                    $haveIndex = true;
                } else {
                    $column['index'] = false;
                }

                // If is an entity Column, check entityRelationAnnotation
                if($column['type'] == 'entity') {
                    if(($relationAnnotation = self::haveAnnotation(self::$entityRelationAnnotation, $propertyAnnotation)) !== false) {
                        $relationAnnotation->check();
                        try {
                            $annotationClassOfRelation = EntityAnalyzer::get($relationAnnotation->classname)->getClassAnnotation();
                        } catch(InvalidEntityException $e) {
                            throw new InvalidAnnotationException($relationAnnotation, 'classname', 'The class ' . $relationAnnotation->classname . ' is not an Entity');
                        }

                        $column['relation'] = array(
                            'classname' => $relationAnnotation->classname,
                            'multi' => $relationAnnotation->multi
                        );
                    } else {
                        throw new InvalidAnnotationException(null, null, 'A Entity Column must have a EntityRelation annotations');
                    }
                }

                $columns[$property->getName()] = $column;
            }
        }

        $this->listColumns = $columns;
        return $columns;
    }

    public function getIndex() {
        if(!is_null($this->index)) return $this->index;
        $column = $this->listColumns();

        foreach($column as $name => $data) {
            if($data['index'] == true) {
                $this->index = $name;
            }
        }
        if(is_null($this->index)) {
            $this->index = false;
        }

        return $this->index;
    }

    public function getClassAnnotation() {
        if(!is_null($this->classAnnotation)) return $this->classAnnotation;

        $annotation = $this->annotationReader->getClassAnnotation($this->reflection, self::$dnAnnotation);
        if(is_null($annotation)) {
            throw new InvalidEntityException($this->className . ' have no ' . self::$dnAnnotation . ' annotation');
        }
        $annotation->check();

        $this->classAnnotation = array('dn' => $annotation->value);

        $annotation = $this->annotationReader->getClassAnnotation($this->reflection, self::$entityAnnotation);
        if(is_null($annotation)) {
            throw new InvalidEntityException($this->className . ' have no ' . self::$entityAnnotation . ' annotation');
        }
        $annotation->check();
        $this->classAnnotation['objectclass'] = $annotation->objectclass;

        return $this->classAnnotation;
    }

    public function getBaseDn() {
        return $this->getClassAnnotation()['dn'];
    }

    public function getObjectclass() {
        return $this->getClassAnnotation()['objectclass'];
    }

    /**
     * Get the list of require method
     * @return array
     */
    public function listRequiredMethod() {
        if(!is_null($this->listRequiredMethod)) return $this->listRequiredMethod;

        $columns = $this->listColumns();
        $methodList = array();

        foreach($columns as $name => $schema) {
            $methodList['get' . Utils::capitalize($name)] = array('type' => self::GETTER, 'column' => $name);
            switch($schema['type']) {
                case 'array':
                    $methodList['add' . Utils::Capitalize($name)] = array('type' => self::ADDER, 'column' => $name);
                    $methodList['remove' . Utils::Capitalize($name)] = array('type' => self::REMOVER, 'column' => $name);
                    break;
                case 'string':
                    $methodList['set' . Utils::Capitalize($name)] = array('type' => self::SETTER, 'column' => $name);
                    break;
                case 'entity':
                    $methodList['add' . Utils::Capitalize($name)] = array('type' => self::ADDER, 'column' => $name);
                    $methodList['remove' . Utils::Capitalize($name)] = array('type' => self::REMOVER, 'column' => $name);
                    break;
            }
        }

        $this->listRequiredMethod = $methodList;

        return $methodList;
    }

    /**
     * Get the list of missing require method
     */
    public function listMissingMethod() {
        if(!is_null($this->listMissingMethod)) return $this->listMissingMethod;

        $required = $this->listRequiredMethod();
        $missing = array();

        foreach($required as $methodName => $data) {
            if(!$this->reflection->hasMethod($methodName)) {
                $missing[$methodName] = $data;
            }
        }

        $this->listMissingMethod = $missing;

        return $missing;
    }

    /**
     * Get the annotation of the class in the list or return false
     * @param $annotationClass
     * @param array $annotationList
     * @return bool|Annotation
     */
    private static function haveAnnotation($annotationClass, array $annotationList) {
        foreach($annotationList as $annotation) {
            if(is_a($annotation, $annotationClass)) {
                return $annotation;
            }
        }
        return false;
    }

    /**
     * @return \ReflectionClass
     */
    public function getReflection() {
        return $this->reflection;
    }

    public function isEntityRelation($column) {
        if(!array_key_exists($column, $this->listColumns())) {
            return false;
        }
        return $this->listColumns()[$column]['type'] === 'entity';
    }

    public function isEntityRelationMultiple($column) {
        if($this->isEntityRelation($column)) {
            return $this->listColumns()[$column]['relation']['multi'];
        }
        return false;
    }
}