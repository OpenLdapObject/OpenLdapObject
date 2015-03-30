<?php

namespace OpenLdapObject\Command;


use OpenLdapObject\Builder\EntityBuilder;

class ReGenerateCommand extends GenerateCommand {
    protected function generate($className) {
        echo 'ReGenerate entity...' . PHP_EOL;
        $builder = new EntityBuilder($className);
        $builder->regenerateGetterSetter();

        echo 'Entity is generate.' . PHP_EOL;
    }
} 