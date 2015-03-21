<?php

namespace OpenLdapObject\Annotations;

/**
 * Class EntityRelation
 * @package OpenLdapObject\Annotations
 * @Annotation
 */
class EntityRelation implements Annotation {
    public $classname;
    public $multi;

    public function check() {
        return (is_bool($this->multi) && !empty($this->classname));
    }


} 