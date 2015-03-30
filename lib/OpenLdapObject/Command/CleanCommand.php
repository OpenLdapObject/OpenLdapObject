<?php

namespace OpenLdapObject\Command;


use OpenLdapObject\Builder\EntityBuilder;

class CleanCommand extends GenerateCommand {
    protected function generate($className) {
        echo 'Clean entity...' . PHP_EOL;
        $builder = new EntityBuilder($className);
        $builder->cleanGetterSetter();

        echo 'Entity is clean.' . PHP_EOL;
    }
} 