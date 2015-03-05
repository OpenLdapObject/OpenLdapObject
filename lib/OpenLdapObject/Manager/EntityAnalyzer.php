<?php

namespace OpenLdapObject\Manager;
use Doctrine\Common\Annotations\AnnotationReader;
use OpenLdapObject\Annotations\Annotation;
use OpenLdapObject\Annotations\AnnotationManager;
use OpenLdapObject\Annotations\InvalidAnnotationException;
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
    private $className;

    private $reflection;
    private $annotationReader;

    // Caching method's Result
    private $listColumns;
    private $listRequiredMethod;
    private $listMissingMethod;

    private static $ColumnAnnotation = 'OpenLdapObject\Annotations\Column';
    private static $IndexAnnotation = 'OpenLdapObject\Annotations\Index';
    private static $dnAnnotation = 'OpenLdapObject\Annotations\Dn';

    public function __construct($className) {
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

                $columns[$property->getName()] = $column;
            }
        }

        $this->listColumns = $columns;
        return $columns;
    }

    public function getClassAnnotation() {
        $annotation = $this->annotationReader->getClassAnnotation($this->reflection, self::$dnAnnotation);
        $annotation->check();

        return array('dn' => $annotation->value);
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
            $methodList[] = 'get' . Utils::capitalize($name);
            switch($schema['type']) {
                case 'array':
                    $methodList[] = 'add' . Utils::Capitalize($name);
                    $methodList[] = 'remove' . Utils::Capitalize($name);
                    break;
                case 'string':
                    $methodList[] = 'set' . Utils::Capitalize($name);
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

        foreach($required as $methodName) {
            if(!$this->reflection->hasMethod($methodName)) {
                $missing[] = $methodName;
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
}