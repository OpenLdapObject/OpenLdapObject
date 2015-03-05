<?php

namespace OpenLdapObject\Annotations;

/**
 * Class Column
 * @package OpenLdapObject\Annotations
 * @Annotation
 */
class Column implements Annotation {
    private static $listType = array('string', 'array');

    public $type;

    public function check() {
        if(!in_array($this->type, self::$listType)) {
            throw new InvalidAnnotationException($this, $this->type, $this->type . ' is not a valid type . (' . implode(',', self::$listType) . ')');
        }
    }
}