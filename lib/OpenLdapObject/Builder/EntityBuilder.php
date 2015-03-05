<?php

namespace OpenLdapObject\Builder;


use OpenLdapObject\Manager\EntityAnalyzer;
use OpenLdapObject\Utils;

class EntityBuilder {
    private static $space = '   ';
    private static $eol = PHP_EOL;
    private static $getterVisibility = 'public';
    private static $setterVisibility = 'public';
    private static $getterTemplate = '<space><visibility> function get<column|capitalize>() {<eol><space><space>return $this-><column>;<eol><space>}<eol>';
    private static $setterTemplate = '<space><visibility> function set<column|capitalize>($value) {<eol><space><space>$this-><column> = $value;<eol><space><space>return $this;<eol><space>}<eol>';
    private static $adderTemplate = '<space><visibility> function add<column|capitalize>($value) {<eol><space><space>$this-><column>[] = $value;<eol><space><space>return $this;<eol><space>}<eol>';
    private static $removerTemplate = '<space><visibility> function remove<column|capitalize>($value) {<eol><space><space>if(($key = array_search($value, $this-><column>)) !== false) {<eol><space><space><space>unset($this-><column>[$key]);<eol><space><space>}<eol><space><space>return $this;<eol><space>}<eol>';


    private $className;
    private $analyzer;

    public function __construct($className) {
        $this->className = $className;
        $this->analyzer = new EntityAnalyzer($this->className);
    }

    public function createGetter($property) {
        return $this->buildMethod(self::$getterTemplate, self::$getterVisibility, $property);
    }

    public function createSetter($property) {
        return $this->buildMethod(self::$setterTemplate, self::$setterVisibility, $property);
    }

    public function createAdder($property) {
        return $this->buildMethod(self::$adderTemplate, self::$setterVisibility, $property);
    }

    public function createRemover($property) {
        return $this->buildMethod(self::$removerTemplate, self::$setterVisibility, $property);
    }

    protected function buildMethod($template, $visibility, $property) {
        $template = str_replace('<visibility>', $visibility, $template);
        $template = str_replace('<space>', self::$space, $template);
        $template = str_replace('<eol>', self::$eol, $template);
        $template = str_replace('<column|capitalize>', Utils::capitalize($property), $template);
        $template = str_replace('<column>', $property, $template);

        return $template;
    }
}

?>