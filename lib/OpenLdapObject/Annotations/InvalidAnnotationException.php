<?php
namespace OpenLdapObject\Annotations;

class InvalidAnnotationException extends \Exception {
    private $annotation;
    private $fieldName;
    private $msg;

    public function __construct(Annotation $annotation, $fieldName, $msg) {
        parent::__construct($msg);
        $this->fieldName = $fieldName;
        $this->annotation = $annotation;
    }

    /**
     * @return string
     */
    public function getFieldName() {
        return $this->fieldName;
    }

    /**
     * @return \OpenLdapObject\Annotations\Annotation
     */
    public function getAnnotation() {
        return $this->annotation;
    }
}
?>