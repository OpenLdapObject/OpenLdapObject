<?php

namespace OpenLdapObject\Annotations;


abstract class AnnotationManager {
    private static $annotationPackage = 'OpenLdapObject\Annotations\\';
    private static $annotationList = array('Column', 'Index', 'Dn', 'Entity');
    private static $annotationIsLoad = false;

    public static function autoLoadAnnotation() {
        if(!self::$annotationIsLoad) {
            foreach(self::$annotationList as $annotation) {
                spl_autoload_call(self::$annotationPackage . $annotation);
            }

            self::$annotationIsLoad = true;
        }
    }

    public static function getAnnotationList() {
        return self::$annotationList;
    }
} 