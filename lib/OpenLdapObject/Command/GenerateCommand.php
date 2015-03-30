<?php

namespace OpenLdapObject\Command;


use OpenLdapObject\Builder\EntityBuilder;

class GenerateCommand implements Command {
    private $args;

    public function __construct(array $args) {
        $this->args = $args;
    }

    public function exec() {
        $entityClassName = str_replace('/', '\\', $this->args['entity']);
        if(!array_key_exists('file', $this->args)) {
            $isLoad = $this->autoload($entityClassName);
        } else {
            $isLoad = $this->load($entityClassName, $this->args['file']);
        }

        if($isLoad) {
            $this->generate($entityClassName);
        }
    }

    protected function autoload($className) {
        echo 'No path is defined, try to autoload class...' . PHP_EOL . PHP_EOL;

        if(!class_exists($className)) spl_autoload_call($className);
        if(!class_exists($className)) {
            echo 'Unable to autoload the entity class('.$className.'), try with a path' . PHP_EOL;
            return false;
        } else {
            echo 'Class is load.' . PHP_EOL;
            return true;
        }
    }

    protected function load($className, $file) {
        if(!file_exists($file)) {
            echo 'The file ' . $file . ' don\'t exist';
            return true;
        } else {
            require_once $file;
            if(!class_exists($className)) {
                echo 'The File ' . $file . ' don\'t define the class ' . $className . PHP_EOL;
                return false;
            }
            return true;
        }
    }

    protected function generate($className) {
        echo 'Generate entity...' . PHP_EOL;
        $builder = new EntityBuilder($className);
        $builder->completeEntity();

        echo 'Entity is generate.' . PHP_EOL;
    }
} 